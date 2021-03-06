<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class feed_model extends CI_Model {


    public function __construct()
    {
        parent::__construct();
        
    }

    function get_list($params=array()){
    	$params = (object)$params;
    	$default_params = (object)array(
            'id_before'  => 0, // call by...
            'id_after'  => 0, // call by...
    		'page' => 1,
            'delimiter' => 20, // 한 페이지당 작품 수
            'order_by'  => 'newest', // newest, oldest
            'user_id' => '', // 필수정보(누구의 피드인지)
            'type' => 'all' //type
    	);
    	foreach($default_params as $key => $value){
    		if(!isset($params->{$key}))
    			$params->{$key} = $value;
    	}

        if(!empty($params->id_before)   &&$params->id_before!=0)
            $this->db->where('user_feeds.id <', $params->id_before);

        if(!empty($params->id_after)    &&$params->id_after!=0)
            $this->db->where('user_feeds.id >', $params->id_after);

        switch($params->order_by){
            case "newest":
                $this->db->order_by('user_feeds.regdate', 'desc');
            break;
            case "oldest":
                $this->db->order_by('user_feeds.regdate', 'asc');
            break;
            default:
                if(is_array($params->order_by))
                    $this->db->order_by($params->order_by);
            break;
        }

        //-- feeds
        $table = "user_feeds";
        $fields = array('id', 'user_id', 'regdate',
                                'readdate', 'deldate');
        foreach($fields as $field){
            $this->db->select($table.'.'.$field);
        }
        unset($table, $fields, $field);
        //-- end

        //-- activity
        $table = "log_activity";
        $fields = array('ref_id', 'user_id', 'area', 'act', 'type',
                                'point_get', 'point_status', 'data');
        foreach($fields as $field){
            $this->db->select($table.'.'.$field);
        }
        $this->db->join($table, 'user_feeds.ref_id='.$table.'.id', 'left');
        unset($table, $fields, $field);
        //-- end

        //-- type:activity
        if($params->type=='activity'){
            $this->db->where_in('type', array('collect', 'comment', 'note'));
        }
        //-- end

        //-- type:work
        if($params->type=='work'){
            $this->db->where_in('type', array('enabled'));

            $table = "works";
            $fields = array('work_id', 'title', 'is_video');
            foreach($fields as $field){
                $this->db->select($table.'.'.$field);
            }
            $this->db->join($table, 'log_activity.ref_id='.$table.'.work_id', 'left');
            unset($table, $fields, $field);
            $table = "users";
            $fields = array('id as work_user_id', 'username as work_user_username', 'realname as work_user_realname', 'modified as work_user_modified');
            foreach($fields as $field){
                $this->db->select($table.'.'.$field);
            }
            $this->db->join($table, 'works.user_id='.$table.'.id', 'left');
            unset($table, $fields, $field);
        }
        //-- end

        $query = $this->db
            ->where('user_feeds.user_id', $params->user_id)
            ->limit($params->delimiter, ((($params->page)-1)*$params->delimiter))
            ->get('user_feeds');

        //error_log($this->db->last_query());

        $rows = array();
        foreach($query->result() as $row){
            $info = unserialize($row->data);

            $obj = (object)array(
                'regdate' => $row->regdate,
                'readdate' => $row->readdate,
                'area' => $row->area,
                'act' => $row->act,
                'type' => $row->type,
                'message' => $row->data,
                'info' => $info
            );

            if($params->type=='activity'){
            }
            else if($params->type=='work'){
                $obj->data = (object)array(
                    'user' => (object)array(
                        'id' => $row->work_user_id,
                        'username' => $row->work_user_username,
                        'realname' => $row->work_user_realname,
                        'modified' => $row->work_user_modified,
                        ),
                    'work_id' => $row->work_id,
                    'title' => $row->title,
                    'is_video' => $row->is_video,
                    );
            }

            $rows[] = $obj;
        }
        $data = (object)array(
            'status' => 'done',
            'page'   => $params->page,
            'rows'   => $rows
        );
        if(sizeof($rows)==0){
            $data->status = 'fail';
            return $data;
        }
        return $data;


    }

    function get_activity_list($params=array()){
        // 일단 야매로... 고쳐주세요!
        return $this->get_list();
    }






    function get_unread_count($user_id=''){
        $this->load->model('alarm_model');
        $user_id = (empty($user_id))?USER_ID:$user_id;

        $feed_count = $this->get_count(array('user_id'=>$user_id));
        $alarm_count = $this->alarm_model->get_count(array('user_id'=>$user_id));

        $data = (object)array(
            'status' => 'done',
            'alarm_all' => $alarm_count->all,
            'alarm_unread' => $alarm_count->unread,
            'feed_all' => $feed_count->all,
            'feed_unread' => $feed_count->unread
        );
        
        return $data;
    }

    function get_count($params=array()){
        $params = (object)$params;
        $default_params = (object)array(
            'user_id' => '' // 필수정보(누구의 피드인지)
        );
        foreach($default_params as $key => $value){
            if(!isset($params->{$key}))
                $params->{$key} = $value;
        }

        $query = $this->db->query("SELECT
            count(id) as all_count, 
            sum( isnull( readdate ) ) as unread
            from user_feeds 
            where user_id = ".$this->db->escape($params->user_id).";"); //set

        try{
            $info = $query->row();
        }
        catch (Exception $e) {
            $data = (object)array(
                'status' => 'fail',
                'all' => 0,
                'unread' => 0,
            );

            return $data;
        }



        $data = (object)array(
            'status' => 'done',
            'all' => (int)$info->all_count,
            'unread' => (int)$info->unread,
        );

        return $data;

    }

    function put_readdate($params=array()){
        $params = (object)$params;
        $default_params = (object)array(
            'readdate' => date('Y-m-d H:i:s'),
            'user_id' => '', // 필수정보(누구의 피드인지)
            'id_before'  => 0, // call by...
            'id_after'  => 0, // call by...
        );
        foreach($default_params as $key => $value){
            if(!isset($params->{$key}))
                $params->{$key} = $value;
        }

        if(!empty($params->id_before)   &&$params->id_before!=0)
            $this->db->where('user_feeds.id <', $params->id_before);

        if(!empty($params->id_after)    &&$params->id_after!=0)
            $this->db->where('user_feeds.id >', $params->id_after);

        try{
            $query = $this->db
                ->set('readdate', $params->readdate)
                ->where('user_id', $params->user_id)
                ->where('readdate', NULL)
                ->update('user_feeds'); //set
        }
        catch (Exception $e) {
            $data = (object)array(
                'status' => 'fail',
                'message' => 'db_update_fail'
            );

            return $data;
        }

        $data = (object)array(
            'status' => 'done',
        );

        return $data;
    }
}

/* End of file work_model.php */
/* Location: ./application/models/work_model.php */