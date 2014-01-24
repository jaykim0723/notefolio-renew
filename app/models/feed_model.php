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
        $data = (object)array(
            'status' => 'done',
            'alarm_all' => 2398,
            'alarm_unread' => 34,
            'feed_all' => 2930,
            'feed_unread' => 3
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

        $this->db
            ->select('count(id) as all, sum(if(isnull(readdate), 0, 1)) as unread')
            ->from('user_feeds')
            ->where('user_id', $params->user_id)
            ->limit($params->delimiter, ((($params->page)-1)*$params->delimiter)); //set

        switch($params->order_by){
            case "newest":
                $this->db->order_by('moddate', 'desc');
            break;
            case "oldest":
                $this->db->order_by('moddate', 'asc');
            break;
            default:
                if(is_array($params->order_by))
                    $this->db->order_by($params->order_by);
            break;
        }

        try{
            $info = $this->db->get()->row();
        }
        catch (Exception $e) {
            $data = (object)array(
                'status' => 'fail',
                'alarm_all' => 0,
                'alarm_unread' => 0,
            );

            return $data;
        }



        $data = (object)array(
            'status' => 'done',
            'alarm_all' => $info->all,
            'alarm_unread' => $info->unread,
        );

        return $data;

    }
}

/* End of file work_model.php */
/* Location: ./application/models/work_model.php */