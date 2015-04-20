<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class user_model extends CI_Model {


    public function __construct()
    {
        parent::__construct();

        if(!defined('CONSOLE')){
            $this->load->model('tank_auth/users');
            $this->load->helper('acp');
        }
    }

    /**
     * 미리 설정하는 환경설정.
     * @param object $params
     */
    function get_list_prep($params){
        switch($params->from){
            case 'day':
                $from = date('Y-m-d');
                break;
            case 'week':
                $from = date('Y-m-d', strtotime('-1 week'));
                break;
            case 'month':
                $from = date('Y-m-d', strtotime('-1 month'));
                break;
            case 'month3':
                $from = date('Y-m-d', strtotime('-3 month'));
                break;
            case 'all':
            default:
                $params->from = 'all';
                break;
        }
        if($params->from!='all'){
            $this->db->where("(users.created >= ".$this->db->escape($from).")", NULL, FALSE); // 모든 기준이 create로 하기 때문에
        }

        if($params->get_profile && count($params->keywords)>0){
            $this->db->where('(user_profiles.keywords like "%'.implode('%" or user_profiles.keywords like "%', $params->keywords).'%" )', NULL, FALSE);
        }

        if(!empty($params->q)){
            $this->db->where('(users.username like \'%'.$this->db->escape_str($params->q).'%\' or users.realname like \'%'.$this->db->escape_str($params->q).'%\' )', NULL, FALSE);
        }

        if(!empty($params->user_id))
            $this->db->where('user_id', $params->user_id);
        if(!empty($params->id_before)   &&$params->id_before!=0)
            $this->db->where('users.id <', $params->id_before);

        if(!empty($params->id_after)    &&$params->id_after!=0)
            $this->db->where('users.id >', $params->id_after);
        
        switch($params->order_by){
            case "idlarger":
                $this->db->order_by('users.id', 'desc');
            break;
            case "idsmaller":
                $this->db->order_by('users.id', 'asc');
            break;
            case "newest":
                $this->db->order_by('users.created', 'desc');
            break;
            case "oldest":
                $this->db->order_by('users.created', 'asc');
            break;
            default:
                if(is_array($params->order_by))
                    $this->db->order_by($params->order_by);
            break;
        }
        
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
            'keywords'  => '', // 
            'get_profile' => false,
            'from'  => 'all', // 조회기간
            'q'  => '', 
    	);
    	foreach($default_params as $key => $value){
    		if(!isset($params->{$key}))
    			$params->{$key} = $value;
    	}

        $this->db->start_cache();
        $this->get_list_prep($params);

        $this->db
            ->select('count(users.id) as count, ceil(count(users.id)/'.$params->delimiter.') as page')
            ->from('users'); //set

        if($params->get_profile){
            $this->db->join('user_profiles', 'users.id=user_profiles.user_id', 'left');
        }

        $this->db->stop_cache();
        $count_data = $this->db->get()->row();
        $this->db->flush_cache(); //clear active record

        $all_count = $count_data->count;
        $all_page = $count_data->page;


        $this->db->start_cache();
        $this->get_list_prep($params);

        $this->db->select('users.*');

        if($params->get_profile){
            $table = "user_profiles";
            $fields = array('user_id', 'keywords', 'website',
                            'facebook_id','twitter_id','pinterest_id','tumblr_id','vimeo_id',
                            'gender', 'birth', 'description', 'mailing',
                            'following_cnt', 'follower_cnt', 'face_color');
            foreach($fields as $field){
                $this->db->select($table.'.'.$field);
            }
            $this->db->join($table, 'users.id='.$table.'.user_id', 'left');
            unset($table, $fields, $field);
        }

    	$this->db
    		->from('users')
    		->limit($params->delimiter, ((($params->page)-1)*$params->delimiter)); //set
        
        $this->db->stop_cache();
        $users = $this->db->get();
        $this->db->flush_cache();

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
            $fields = array('user_id', 'keywords', 'website',
                            'facebook_id','twitter_id','pinterest_id','tumblr_id','vimeo_id',
                            'gender', 'birth', 'description', 'mailing',
                            'following_cnt', 'follower_cnt', 'face_color');
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
            if($params->get_profile){
                $user->user_keywords = $user->keywords;
                $user->sns = (object)array(
                    'website' => $user->website,
                    'facebook' => $user->facebook_id,
                    'twitter' => $user->twitter_id,
                    'pinterest' => $user->pinterest_id,
                    'tumblr' => $user->tumblr_id,
                    'vimeo' => $user->vimeo_id
                );
            }

            $followed = $this->db
                ->where(array(
                    'follower_id'=>USER_ID,
                    'follow_id'=>$user->id
                    ))
                ->get('user_follows');

            $user->is_follow = ($followed->num_rows()>0) ? 'y' : 'n';

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
            'username'  => '',
            'email'     => '',
            'password'  => '',
        );
        foreach($default_params as $key => $value){
            if(!isset($params->{$key}))
                $params->{$key} = $value;
        }

        // default tank auth
        $result = $this->tank_auth->create_user(
            $params->username, $params->email, $params->password, $this->config->item('email_activation', 'tank_auth')
        ); //($username, $email, $password, $email_activation)

        if($result){
            $data = (object)array(
                'status' => 'done',
                'row' => (object)array(
                    'id' => $result['user_id'],
                    'username' => $params->username
                    )
            );
        } else {
            $data = (object)array(
                'status' => 'fail',
                'message' => 'error'
            );
        }

        return $data;
    }

    /**
     * post user data when update.
     *
     * @param  array  $params (depend by field in table `users`)
     * @return object       (status return object. status=[done|fail])
     */
    function put($input=array(), $force=false){
        $input = (object)$input;
        //-- id is not for update
        $id = isset($input->id)?$input->id:USER_ID;
        unset($input->id);

        $input = (object)$input;
        $input_profiles = new stdClass(); //create new Object;
        
        //-- exclude not allowed field
        $allowed_key            = array('username','realname',/*'email'*/);
        $allowed_key_profiles   = array('gender', 'birth', 'mailing');
        foreach($input as $key => $val){
            if(in_array($key, $allowed_key)){
                continue;
            }
            else if(in_array($key, $allowed_key_profiles)){
                $input_profiles->{$key} = $val;
                unset($input->{$key});
            }
            else{
                unset($input->{$key});
            }
        }
        if(empty($input_profiles->mailing))
            $input_profiles->mailing = 0;

        $input->modified = date('Y-m-d H:i:s'); // 무조건 수정이 발생하게 하기 위하여 현재 타임스탬프로 임의로 찍어준다.
        $input_profiles->moddate = date('Y-m-d H:i:s'); // 무조건 수정이 발생하게 하기 위하여 현재 타임스탬프로 임의로 찍어준다.

        if($this->nf->admin_is_elevated()){ // 관리자는 전지전능하심. 
            $can_put_in = true;
        }
        else if($force) { // 강제기록
            $can_put_in = true; 
        }
        else { // 본인것인지 여부에 따라 message다르게 하기
            $user = $this->db->where('users.id', $id)->get('users')->row();
            $can_put_in = ($user->id == USER_ID)?true:false; 
        }

        if($can_put_in){
            $this->db->flush_cache(); //clear active record

            $this->db->trans_start();
            if(!empty($id)){
                if(count($input)>0)
                    $this->db->where('id', $id)->update('users', $input); // 사용자 레코드 수정.
                if(count($input_profiles)>0)
                    $this->db->where('user_id', $id)->update('user_profiles', $input_profiles);
            }
            $this->db->trans_complete();

            if($this->db->trans_status()){
                $data = (object)array(
                    'status' => 'done',
                    'input' => $input
                );
            } else {
                $data = (object)array(
                    'status' => 'fail',
                    'message' => 'cannot_run_put_sel'
                );
            }
        } else {
            $data = (object)array(
                'status' => 'fail',
                'message' => 'no_permission_to_put'
            );
        }

        return $data;
    }

    /**
     * post timestamp to user info when update.
     *
     * @param  array  $input (depend by field in table `users`)
     * @return object       (status return object. status=[done|fail])
     */
    function put_timestamp($input=array()){
        $input = (object)$input;

        //-- id is not for update
        $id = isset($input->id)?$input->id:USER_ID;
        unset($input->id);

        $input = (object)$input;
        
        $input->modified = date('Y-m-d H:i:s'); // 무조건 수정이 발생하게 하기 위하여 현재 타임스탬프로 임의로 찍어준다.

        //-- exclude not allowed field
        $allowed_key            = array('modified',);
        foreach($input as $key => $val){
            if(in_array($key, $allowed_key)){
                continue;
            }
            else{
                unset($input->{$key});
            }
        }
        if($this->nf->admin_is_elevated()){ // 관리자는 전지전능하심. 
            $can_delete = true;
        }
        else { // 본인것인지 여부에 따라 message다르게 하기
            $user = $this->db->where('users.id', $id)->get('users')->row();
            $can_delete = ($user->id == USER_ID)?true:false; 
        }

        if($can_delete){
            $this->db->flush_cache(); //clear active record

            $this->db->trans_start();
            if(!empty($id)){
                $this->db->where('id', $id)->update('users', $input); // 사용자 레코드 수정.\
            }
            $this->db->trans_complete();

            if($this->db->trans_status()){
                $data = (object)array(
                    'status' => 'done'
                );
            } else {
                $data = (object)array(
                    'status' => 'fail',
                    'message' => 'cannot_run_put_sel'
                );
            }
        } else {
            $data = (object)array(
                'status' => 'fail',
                'message' => 'no_permission_to_put'
            );
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
            if(!empty($user_id))
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
     * 사용자의 자세한 정보를 불러들인다. (facebook)
     * @param  array $params
     * @return object          상태와 데이터값을 반환한다
     */
    function get_info_sns_fb($params=array()){
        $params = (object)$params;
        $default_params = (object)array(
            'id' => USER_ID,
            'sns_fb_num_id' => '',
        );
        foreach($default_params as $key => $value){
            if(!isset($params->{$key}))
                $params->{$key} = $value;
        }

        $this->db->select('user_sns_fb.*');

        if(!empty($params->sns_fb_num_id))
            $this->db->where('fb_num_id', $params->sns_fb_num_id);
        if(!empty($params->id))
            $this->db->where('id', $params->id);

        try{
            $info = $this->db->get('user_sns_fb')->row();
        }
        catch(Exception $e){
            error_log($e);

            $data = (object)array(
                'status' => 'fail'
            );

            return $data;
        }

        if(count($info)<1){
            $data = (object)array(
                'status' => 'fail',
                'message' => 'no_data'
            );
        }
        else {
            $data = (object)array(
                'status' => 'done',
                'row' => $info
            );
        }

        return $data;
    }
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
    function put_sns_fb($input=array()){
        // null > return fail
        if($input == array()){
            $data = (object)array(
                'status' => 'fail',
                'message' => 'no_input_data'
            );

            return $data;
        }

        $input = (object)$input;

        $params = (object)array(
            'moddate' => date('Y-m-d H:i:s'),
        );

        $this->load->library('fbsdk');

        $user_id = $input->user_id;
        $fb_num_id = $input->fb_num_id;

        if(empty($user_id)){
            $data = (object)array(
                'status' => 'fail',
                'message'=> 'no_input_user_id'
            );
            
            return $data;
        }

        if(empty($fb_num_id))
            $params->fb_num_id = $this->fbsdk->getUser();// get the facebook user
        else
            $params->fb_num_id = $fb_num_id;

        $params->access_token = $this->fbsdk->getAccessToken();

        $this->db->trans_start();
        //-- set
        if(empty($input->post_note))
            $this->db->set('post_note', 'N');
        else
            $this->db->set('post_note', $input->post_note);
        if(empty($input->post_comment))
            $this->db->set('post_comment', 'N');
        else
            $this->db->set('post_comment', $input->post_comment);
        if(empty($input->post_work))
            $this->db->set('post_work', 'N');
        else
            $this->db->set('post_work', $input->post_work);
        //-- where
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
                'status' => 'fail',
                'message'=> 'cannot_write'
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

    /**
     * Send email message of given type (activate, forgot_password, etc.)
     *
     * @param   string
     * @param   string
     * @param   array
     * @return  void
     */
    function _send_email($type, $email, &$data)
    {
        $this->load->library('email');
        $this->email->from($this->config->item('webmaster_email', 'tank_auth'), $this->config->item('website_name', 'tank_auth'));
        $this->email->reply_to($this->config->item('webmaster_email', 'tank_auth'), $this->config->item('website_name', 'tank_auth'));
        $this->email->to($email);
        $this->email->subject(sprintf($this->lang->line('auth_subject_'.$type), $this->config->item('website_name', 'tank_auth')));
        $this->email->message($this->load->view('email/'.$type.'-html', $data, TRUE));
        $this->email->set_alt_message($this->load->view('email/'.$type.'-txt', $data, TRUE));
        $this->email->send();
        
    }

}

/* End of file user_model.php */
/* Location: ./application/models/user_model.php */