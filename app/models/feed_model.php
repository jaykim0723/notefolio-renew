<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class feed_model extends CI_Model {


    public function __construct()
    {
        parent::__construct();
        
    }

    function get_list($params=array()){
    	$params = (object)$params;
    	$default_params = (object)array(
    		'page' => 1,
            'user_id' => '' // 필수정보(누구의 피드인지)
    	);
    	foreach($default_params as $key => $value){
    		if(!isset($params->{$key}))
    			$params->{$key} = $value;
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

        $query = $this->db
            //->where('user_feeds.user_id', $params->user_id)
            ->get('user_feeds');

        $rows = array();
        foreach($query->result() as $row){
            $info = unserialize($row->data);

            $rows[] = (object)array(
                'regdate' => $row->regdate,
                'readdate' => $row->readdate,
                'area' => $row->area,
                'act' => $row->act,
                'type' => $row->type,
                'message' => $row->data,
                'info' => $info
            );
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
        
        // do stuff
        // 
        
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
            ifnull( sum( if( isnull( readdate ), 0, 1 ) ), 0 ) as unread
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
}

/* End of file work_model.php */
/* Location: ./application/models/work_model.php */