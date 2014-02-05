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
            ->get('user_feeds');

        var_export($query->result());
        exit();

        // DB 호출하
        // 출력값 조정하고
        // do stuff by 성수씨
        $row = (object)array(
            'user' => (object)array(
                'id' => 234,
                'realname' => '이흥현',
                'username' => '홍구'
            ),
            'work' => (object)array(
                'work_id' => 2398674,
                'title' => '노트폴리오 예제',
                'regdate' => '2013-01-23 11:20:12'
            ),
            'regdate' => '2013-01-23 11:20:12',
            'type' => 'create',
            'message' => '<a class="info_link" href="/gangsups">홍구</a>님이 <a class="info_link" href="#">새로운 작품</a>을 공개하였습니다.'
        );
        $rows = array();
        for($i=0; $i<20; $i++)
            $rows[] = $row;
        
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