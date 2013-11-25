<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class work_model extends CI_Model {


    public function __construct()
    {
        parent::__construct();
        
    }

    /**
     * 작품의 리스트를 불러온다.
     * @param  array  $params 
     * @return object          상태와 데이터값을 반환한다
     */
    function get_list($params=array()){
    	$params = (object)$params;
    	$default_params = (object)array(
    		'page' => 1, // 불러올 페이지
    		'delimiter' => 12, // 한 페이지당 작품 수
    		'order_by' => 'newest', // newest, oldest
    		'keywords' => '', // *plain으로 들어오고 이곳 모델에서 코드로 변형을 해준다.
    	);
    	foreach($default_params as $key => $value){
    		if(!isset($params->{$key}))
    			$params->{$key} = $value;
    	}

    	$this->db
            ->select('works.*, users.*, users.id as user_id')
    		// ->select('work_id, title, realname, regdate, keywords, tags, user_id, folder, contents, moddate, hit_cnt, note_cnt, comment_cnt, collect_cnt, ccl, discoverbility')
    		->from('works')
    		->join('users', 'users.id = works.user_id', 'left')
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

    	$works = $this->db->get();

    	$rows = array();
    	foreach ($works->result() as $row)
		{
            // 값을 조작해야할 필요가 있을 때에는 여기에서 한다
            // do stuff
		    $rows[] = $row;
		}
        $data = (object)array(
            'status' => 'done',
            'page' => $params->page,
            'rows' => $rows
        );
        if(sizeof($rows)==0){
            $data->status = 'fail';
            return $data;
        }
        return $data;
    }

    /**
     * 작품의 자세한 정보를 불러들인다.
     * @param  string $work_id [description]
     * @return object          상태와 데이터값을 반환한다
     */
    function get_info($params=array()){
        $params = (object)$params;
        $default_params = (object)array(
            'work_id' => '',
            'folder' => '' // ''면 모든 작품
        );
        foreach($default_params as $key => $value){
            if(!isset($params->{$key}))
                $params->{$key} = $value;
        }

    	$this->db
            ->select('works.*, users.*, users.id as user_id')
    		// ->select('work_id, title, realname as user, regdate, keywords, tags, user_id, folder, contents, moddate, hit_cnt, note_cnt, comment_cnt, collect_cnt, ccl, discoverbility')
    		->from('works')
    		->join('users', 'users.id = works.user_id', 'left')
    		->where('works.work_id', $params->work_id)
    		->limit(1); //set
        $work = $this->db->get()->row();
        $data = (object)array(
            'status' => 'done',
            'row' => $work
        );
        if(!$work){
            $data->status = 'fail';
            return $data;
        }
        // 값을 조작해야할 필요가 있을 때에는 여기에서 한다
        // do stuff

    	return $data;
    }


    /**
     * 업로드할 때에 해당 유저에 대해서 비어 있는 work_id를 생성한다.
     * @return [type] [description]
     */
    function post_info(){
        $this->db->insert('works', array(
            'work_id' => NULL,
            'user_id' => USER_ID
        ));
        $work_id = $this->db->insert_id();
        return $this->get_info(array('work_id'=>$work_id));
    }

    /**
     * 삽입이나 수정할 때에도 이용을 한다.
     * @param  array  $data [description]
     * @return [type]       [description]
     */
    function put_info($input){
        // 값을 정규식으로 검사한다.
        $input->moddate = date('Y-m-d H:i:s'); // 무조건 수정이 발생하게 하기 위하여 현재 타임스탬프로 임의로 찍어준다.
        $work_id = $input->work_id;
        unset($input->work_id);
        $this->db->where('work_id', $work_id)->where('user_id', USER_ID)->update('works', $input);

        $data = (object)array(
            'status' => 'done'
        );
        if($this->db->affected_rows()==0){
            $data->status = 'fail';
        }
        return $data;
    }


    function delete_info($work_id){
        // 본인것인지 여부에 따라 message다르게 하기
    }

}

/* End of file work_model.php */
/* Location: ./application/models/work_model.php */