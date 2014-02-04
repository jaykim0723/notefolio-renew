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
        }

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
            'all' => $info->all_count,
            'unread' => $info->unread,
        );

        return $data;

    }
}

/* End of file work_model.php */
/* Location: ./application/models/work_model.php */