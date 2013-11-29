<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class user_model extends CI_Model {


    public function __construct()
    {
        parent::__construct();

        $this->load->model('tank_auth/users');
        
    }

    /**
     * 사용자 리스트를 불러온다.
     * @param  array  $params 
     * @return object          상태와 데이터값을 반환한다
     */
    function get_list($params=array()){
    	$params = (object)$params;
    	$default_params = (object)array(
            'page'      => 1, // 불러올 페이지
            'delimiter' => 30, // 한 페이지당 작품 수
            'order_by'  => 'newest', // newest, oldest
            'keywords'  => '', // *plain으로 들어오고 이곳 모델에서 코드로 변형을 해준다.
    	);
    	foreach($default_params as $key => $value){
    		if(!isset($params->{$key}))
    			$params->{$key} = $value;
    	}

    	$this->db
            ->select('users.*')
    		->table('users')
    		->limit($params->delimiter, ((($params->page)-1)*$params->delimiter)); //set

    	switch($params->order_by){
    		case "newest":
    			$this->db->order_by('created', 'desc');
    		break;
    		case "oldest":
    			$this->db->order_by('created', 'asc');
    		break;
    		default:
    			if(is_array($params->order_by))
    				$this->db->order_by($params->order_by);
    		break;
    	}

    	$users = $this->db->get();

    	$rows = array();
    	foreach ($users->result() as $row)
		{
            // 값을 조작해야할 필요가 있을 때에는 여기에서 한다
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
            ->select('users.*')
    		->from('users')
    		->where('users.id', $params->work_id)
    		->limit(1); //set
        $user = $this->db->get()->row();
        $data = (object)array(
            'status' => 'done',
            'row' => $user
        );

    	return $data;
    }


    /**
     * create user => 기본 tank_auth 이용. 실제 일부 데이터만 저장하므로 나머지는 $this->put() 에서 처리.
     * 
     * @return object       (work content data)
     */
    function post(){
        $this->db->insert('works', array(
            'work_id' => NULL, // 자동 생성
            'user_id' => USER_ID
        ));
        $work_id = $this->db->insert_id();
        return $work_id;
    }

    /**
     * post user data when update.
     *
     * @param  array  $data (depend by field in table `works`)
     * @return object       (status return object. status=[done|fail])
     */
    function put($input=array()){
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
     * delete user record.
     * cannot undo after run this code, so you must be careful to use.
     *
     * @param  array  $data (depend by field in table `users` but only use `users.id`)
     * @return object       (status return object. status=[done|fail])
     */
    function delete($data=array()){
        // null > return fail
        if($data == array()){
            $data = (object)array(
                'status' => 'fail',
                'message' => 'no_input_data'
            );

            return $data;
        }

        $user_id = @$data['user_id'];

        if($this->nf->admin_is_elevated()){ // 관리자는 전지전능하심. 
            $can_delete = true;
        }
        else { // 본인것인지 여부에 따라 message다르게 하기
            $user = $this->db->where('users.id', $user_id)->get('users')->row();
            $can_delete = ($user->id == USER_ID)?true:false; 
        }

        if($can_delete){
            $this->db->flush_cache(); //clear active record

            $this->db->trans_start();
            $this->users->delete_user($user_id); // 사용자 레코드 삭제.
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

/* End of file user_model.php */
/* Location: ./application/models/user_model.php */