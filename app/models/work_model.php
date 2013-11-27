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
            'page'      => 1, // 불러올 페이지
            'delimiter' => 16, // 한 페이지당 작품 수
            'order_by'  => 'newest', // newest, oldest
            'keywords'  => '', // *plain으로 들어오고 이곳 모델에서 코드로 변형을 해준다.
            'folder'    => '', // ''면 전체
            'user_id'   => '' // 프로필 등 특정 작가의 작품만을 조회할 때
    	);
    	foreach($default_params as $key => $value){
    		if(!isset($params->{$key}))
    			$params->{$key} = $value;
    	}

    	$this->db
            ->select('works.*, users.id as user_id, users.username, users.email, users.level, users.realname, users.last_ip, users.last_login, users.created, users.modified')
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
            $user = (object)array(
                'user_id'    => $row->user_id,
                'username'   => $row->username,
                'email'      => $row->email,
                'level'      => $row->level,
                'realname'   => $row->realname,
                'last_ip'    => $row->last_ip,
                'last_login' => $row->last_login,
                'created'    => $row->created,
                'modified'   => $row->modified
            );
            foreach($user as $key=>$value){
                unset($row->{$key});
            }
            $row->user = $user;
		    $rows[] = $row;
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

    /**
     * 작품의 자세한 정보를 불러들인다.
     * @param  array $params   (work_id, folder)
     * @return object          상태와 데이터값을 반환한다
     */
    function get_info($params=array()){
        $params = (object)$params;
        $default_params = (object)array(
            'work_id' => '',
            'folder'  => '' // ''면 모든 작품
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
     * @return object       (work content data)
     */
    function post_info(){
        $this->db->insert('works', array(
            'work_id' => NULL, // 자동 생성
            'user_id' => USER_ID
        ));
        $work_id = $this->db->insert_id();
        return $work_id;
    }

    /**
     * post work data when create/update.
     * only use UPDATE query; so you must run $this->post_info() before run this func.
     *
     * @param  array  $data (depend by field in table `works`)
     * @return object       (status return object. status=[done|fail])
     */
    function put_info($input=array()){
        // 값을 정규식으로 검사한다.
        
        $input->moddate = date('Y-m-d H:i:s'); // 무조건 수정이 발생하게 하기 위하여 현재 타임스탬프로 임의로 찍어준다.
        
        //-- work id is not for update
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

    /**
     * delete work record.
     * cannot undo after run this code, so you must be careful to use.
     *
     * @param  array  $data (depend by field in table `works` but only use `work_id`)
     * @return object       (status return object. status=[done|fail])
     */
    function delete_info($data=array()){
        // null > return fail
        if($data == array()){
            $data = (object)array(
                'status' => 'fail',
                'message' => 'no_input_data'
            );

            return $data;
        }

        $work_id = @$data['work_id'];

        // 본인것인지 여부에 따라 message다르게 하기
        $work = $this->db->where('work_id', $work_id)->get('works')->row(); 
        if($work->user_id == USER_ID){
            $this->db->flush_cache(); //clear active record
            
            $this->db->trans_start();
            $this->db->where('work_id', $work_id)->delete('works'); 
            $this->db->trans_complete();

            if($this->db->trans_status()){
                $data = (object)array(
                    'status' => 'done'
                );
            } else {
                $data = (object)array(
                    'status' => 'fail',
                    'message' => 'cannot_run_delete_sel'
                );
            }
        } else {
            $data = (object)array(
                'status' => 'fail',
                'message' => 'no_permission_to_delete'
            );
        }

        return $data;
    }

    

}

/* End of file work_model.php */
/* Location: ./application/models/work_model.php */