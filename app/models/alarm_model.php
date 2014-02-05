<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class alarm_model extends CI_Model {


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
            ->get('user_alarms');

        $rows = array();
        foreach($query->result() as $row){
            $info = unserialize($row->data);

            $rows[] = (object)array(
                'user' => (object)array(
                    'id' => $info->user_A['id'],
                    'realname' => $info->user_A['realname'],
                    'username' => $info->user_A['username']
                ),
                'work' => (object)array(
                    'work_id' => $info->work['work_id'],
                    'title' => $info->work['title'],
                ),
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
            ifnull( sum( if( isnull( readdate ), 0, 1 ) ), 0 ) as unread
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
}

/* End of file work_model.php */
/* Location: ./application/models/work_model.php */