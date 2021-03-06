<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class alarm_model extends CI_Model {


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
            'user_id' => '' // 필수정보(누구의 피드인지)
        );
        foreach($default_params as $key => $value){
            if(!isset($params->{$key}))
                $params->{$key} = $value;
        }

        if(!empty($params->id_before)   &&$params->id_before!=0)
            $this->db->where('user_alarms.id <', $params->id_before);

        if(!empty($params->id_after)    &&$params->id_after!=0)
            $this->db->where('user_alarms.id >', $params->id_after);

        switch($params->order_by){
            case "newest":
                $this->db->order_by('user_alarms.regdate', 'desc');
            break;
            case "oldest":
                $this->db->order_by('user_alarms.regdate', 'asc');
            break;
            default:
                if(is_array($params->order_by))
                    $this->db->order_by($params->order_by);
            break;
        }

        //-- feeds
        $table = "user_alarms";
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
        $this->db->join($table, 'user_alarms.ref_id='.$table.'.id', 'left');
        unset($table, $fields, $field);
        //-- end

        $query = $this->db
            ->where('user_alarms.user_id', $params->user_id)
        ->limit($params->delimiter, ((($params->page)-1)*$params->delimiter))
            ->get('user_alarms');

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
            from user_alarms 
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
                ->update('user_alarms'); //set
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