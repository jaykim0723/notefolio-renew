<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class user_model extends CI_Model {


    public function __construct()
    {
        parent::__construct();

        $this->load->model('tank_auth/users');
        $this->load->helper('acp');
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
            'get_profile' => false,
    	);
    	foreach($default_params as $key => $value){
    		if(!isset($params->{$key}))
    			$params->{$key} = $value;
    	}
        
        $this->db
            ->select('count(id) as count, ceil(count(id)/'.$params->delimiter.') as page')
            ->from('users'); //set

        if($params->get_profile){
            $this->db->join('user_profiles', 'users.id=user_profiles.user_id', 'left');
        }

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

        $count_data = $this->db->get()->row();
        $all_count = $count_data->count;
        $all_page = $count_data->page;

        $this->db->flush_cache(); //clear active record

        $this->db->select('users.*');

        if($params->get_profile){
            $table = "user_profiles";
            $fields = array('user_id', 'keywords', 'website', 'facebook_id',
                                    'twitter_id', 'gender', 'birth',
                                    'description', 'mailing',
                                    'following_cnt', 'follower_cnt');
            foreach($fields as $field){
                $this->db->select($table.'.'.$field);
            }
            $this->db->join($table, 'users.id='.$table.'.user_id', 'left');
            unset($table, $fields, $field);
        }
        //TODO: keywords : [‘파인아트’,’동영상’]

    	$this->db
    		->from('users')
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
            unset($row->password);
            unset($row->new_password_key);
            unset($row->new_password_requested);
            unset($row->new_email_key);
            unset($row->new_email);
		    $rows[] = $row;
		}
        $data = (object)array(
            'status' => 'done',
            'all_count'   => $all_count,
            'all_page'   => $all_page,
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
     * 사용자의 자세한 정보를 불러들인다.
     * @param  array $params
     * @return object          상태와 데이터값을 반환한다
     */
    function get_info($params=array()){
        $params = (object)$params;
        $default_params = (object)array(
            'id' => '',
            'username' => '',
            'email' => '',
            'sns_fb_num_id' => '',
            'get_profile' => false,
            'get_sns_fb' => false,
        );
        foreach($default_params as $key => $value){
            if(!isset($params->{$key}))
                $params->{$key} = $value;
        }

        $this->db->select('users.*');

        if($params->get_profile){
            $table = "user_profiles";
            $fields = array('user_id', 'keywords', 'website', 'facebook_id',
                                    'twitter_id', 'gender', 'birth',
                                    'description', 'mailing',
                                    'following_cnt', 'follower_cnt');
            foreach($fields as $field){
                $this->db->select($table.'.'.$field);
            }
            $this->db->join($table, 'users.id='.$table.'.user_id', 'left');
            unset($table, $fields, $field);
        }
        //TODO: keywords : [‘파인아트’,’동영상’]
        if($params->get_sns_fb){
            $table = "user_sns_fb";
            $fields = array('fb_num_id', 'access_token',
                 'post_work', 'post_comment', 'post_note', 'regdate as fb_regdate');
            foreach($fields as $field){
                $this->db->select($table.'.'.$field);
            }
            $this->db->join($table, 'users.id='.$table.'.id', 'left');
            unset($table, $fields, $field);
        }

    	$this->db
    		->from('users')
    		->limit(1); //set

        if($params->get_sns_fb && !empty($params->sns_fb_num_id))
            $this->db->where('fb_num_id', $params->sns_fb_num_id);
        else if(!empty($params->username))
            $this->db->where('users.username', $params->username);
        else if(!empty($params->email))
            $this->db->where('users.email', $params->email);
        else
            $this->db->where('users.id', $params->id);

        try{
            $user = $this->db->get()->row();
        }
        catch(Exception $e){
            error_log($e);

            $data = (object)array(
                'status' => 'fail'
            );

            return $data;
        }

        if(count($user)<1){
            $data = (object)array(
                'status' => 'fail',
                'message' => 'no_data'
            );
        }
        else {
            # 성수씨 
            $user->followings = 234;
            $user->followers = 29;
            $user->keywords = array( // temporary
                '파인아트', '모션그래픽', '동영상'
            );
            $user->sns = (object)array( // temporary
                'facebook' => 'maxzidell',
                'twitter' => 'maxzidell'
            );

            unset($user->password);
            unset($user->new_password_key);
            unset($user->new_password_requested);
            unset($user->new_email_key);
            unset($user->new_email);
            $data = (object)array(
                'status' => 'done',
                'row' => $user
            );
        }

    	return $data;
    }


    /**
     * create user => 기본 tank_auth 이용. 실제 일부 데이터만 저장하므로 나머지는 $this->put() 에서 처리.
     * 
     * @return object       (work content data)
     */
    function post($params=array()){
        $params = (object)$params;
        $default_params = (object)array(
            'id' => '',
            'set_profile' => false,
            'set_sns_fb' => false,
        );
        foreach($default_params as $key => $value){
            if(!isset($params->{$key}))
                $params->{$key} = $value;
        }

        // default tank auth
        $this->tank_auth->create_user();

        $user_id = $this->db->insert_id();
        return $user_id;
    }

    /**
     * post user data when update.
     *
     * @param  array  $params (depend by field in table `users`)
     * @return object       (status return object. status=[done|fail])
     */
    function put($params=array()){
        $params = (object)$params;
        $default_params = (object)array(
            'id' => '',
            'set_profile' => false,
            'set_sns_fb' => false,
        );
        foreach($default_params as $key => $value){
            if(!isset($params->{$key}))
                $params->{$key} = $value;
        }
        // 값을 정규식으로 검사한다.
        
        $input->moddate = date('Y-m-d H:i:s'); // 무조건 수정이 발생하게 하기 위하여 현재 타임스탬프로 임의로 찍어준다.
        
        //-- work id is not for update
        $work_id = $input->work_id;
        unset($input->work_id);

        unset($input->set_profile);
        unset($input->set_sns_fb);

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

    /**
     * facebook
     */

    /**
     * post user's facebook info
     * 
     * @return object       (work content data)
     */
    function post_sns_fb($params=array()){
        $params = (object)$params;
        $default_params = (object)array(
            'id'            => '',
            'fb_num_id'     => '',
            'post_work'     => 'Y',
            'post_comment'  => 'Y',
            'post_note'     => 'Y',
            'regdate'       => date('Y-m-d H:i:s'),
            'moddate'       => date('Y-m-d H:i:s'),
        );
        foreach($default_params as $key => $value){
            if(!isset($params->{$key}))
                $params->{$key} = $value;
        }

        $this->load->library('fbsdk');

        if(empty($params->fb_num_id))
            $params->fb_num_id = $this->fbsdk->getUser();// get the facebook user

        $params->access_token = $this->fbsdk->getAccessToken();

        $this->db->trans_start();
        $this->db->insert('user_sns_fb', $params);
        $this->db->trans_complete();

        if($this->db->trans_status()){
            $data = (object)array(
                'status' => 'done'
            );
        } else {
            $data = (object)array(
                'status' => 'fail'
            );
        }

        return $data;
    }

    /**
     * put user's facebook info
     *
     * @param  array  $data (depend by field in table `user_sns_fb`)
     * @return object       (status return object. status=[done|fail])
     */
    function put_sns_fb($data=array()){
        // null > return fail
        if($data == array()){
            $data = (object)array(
                'status' => 'fail',
                'message' => 'no_input_data'
            );

            return $data;
        }

        $params = (object)array(
            'moddate' => date('Y-m-d H:i:s'),
        );

        $this->load->library('fbsdk');

        $user_id = @$data['id'];
        $fb_num_id = @$data['fb_num_id'];

        if(empty($user_id)){

        }

        if(empty($fb_num_id))
            $params->fb_num_id = $this->fbsdk->getUser();// get the facebook user
        else
            $params->fb_num_id = $fb_num_id;

        $params->access_token = $this->fbsdk->getAccessToken();

        $this->db->trans_start();
        if(!empty($fb_num_id))
            $this->db->where('fb_num_id', $fb_num_id);
        if(!empty($user_id))
            $this->db->where('id', $user_id);
        if((!empty($fb_num_id)||!empty($user_id)))
            $this->db->update('user_sns_fb', $params);
        $this->db->trans_complete();

        if($this->db->trans_status()){
            $data = (object)array(
                'status' => 'done'
            );
        } else {
            $data = (object)array(
                'status' => 'fail'
            );
        }

        return $data;
    }

    /**
     * delete user's facebook info
     * cannot undo after run this code, so you must be careful to use.
     *
     * @param  array  $data (depend by field in table `user_sns_fb`)
     * @return object       (status return object. status=[done|fail])
     */
    function delete_sns_fb($data=array()){
        // null > return fail
        if($data == array()){
            $data = (object)array(
                'status' => 'fail',
                'message' => 'no_input_data'
            );

            return $data;
        }

        $user_id = @$data['id'];
        $fb_num_id = @$data['fb_num_id'];

        if($this->nf->admin_is_elevated()){ // 관리자는 전지전능하심. 
            $can_delete = true;
        }
        else { // 본인것인지 여부에 따라 message다르게 하기
            if(!empty($fb_num_id))
                $this->db->where('fb_num_id', $fb_num_id);
            if(!empty($user_id))
                $this->db->where('id', $user_id);
            $info = $this->db->get('user_sns_fb')->row();
            $can_delete = ($info->id == USER_ID)?true:false; 
        }

        if($can_delete){
            $this->db->flush_cache(); //clear active record

            $this->db->trans_start();
            if(!empty($fb_num_id))
                $this->db->where('fb_num_id', $fb_num_id);
            if(!empty($user_id))
                $this->db->where('id', $user_id);
            if((!empty($fb_num_id)||!empty($user_id)))
                $this->db->delete('user_sns_fb');
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