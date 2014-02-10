<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Auth extends CI_Controller
{
    var $go_to = '/';

	function __construct()
	{
		parent::__construct();

		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		$this->load->library('security');
		$this->lang->load('tank_auth');
		$this->load->library('fbsdk');
		$this->load->library('user_agent');
        $this->nf->_member_check(array('setting','change_password','change_email','unregister'));
        $this->load->model('user_model');

        $this->go_to = $this->input->post('go_to')?$this->input->post('go_to'):'/'; # get url to go after login
	}

	function index()
	{
		if ($message = $this->session->flashdata('message')) {
            $this->layout->set_view('auth/general_message', array('message' => $message))->render(); 
		} else {
			redirect('/auth/login/');
		}
	}

    /**
     * Login user on the site
     *
     * @return function
     */
    function login()
    {
        $is_ajax = $this->input->is_ajax_request();
        $go_to = $this->input->post('go_to')?$this->input->post('go_to'):'/'; # get url to go after login
 

        if ($this->_login_check($go_to)) {
            $data['login_by_username'] = ($this->config->item('login_by_username', 'tank_auth') AND
                    $this->config->item('use_username', 'tank_auth'));
            $data['login_by_email'] = $this->config->item('login_by_email', 'tank_auth');

            $this->form_validation->set_rules('login', 'Login', 'trim|required|xss_clean');
            $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');
            $this->form_validation->set_rules('remember', 'Remember me', 'integer');
            $this->form_validation->set_error_delimiters('<span class="error">', '</span>');

            // Get login for counting attempts to login
            if ($this->config->item('login_count_attempts', 'tank_auth') AND
                    ($login = $this->input->post('login'))) {
                $login = $this->security->xss_clean($login);
            } else {
                $login = '';
            }

            $data['use_recaptcha'] = $this->config->item('use_recaptcha', 'tank_auth');
            if ($this->tank_auth->is_max_login_attempts_exceeded($login)) {
                if ($data['use_recaptcha'])
                    $this->form_validation->set_rules('recaptcha_response_field', 'Confirmation Code', 'trim|xss_clean|required|callback__check_recaptcha');
                else
                    $this->form_validation->set_rules('captcha', 'Confirmation Code', 'trim|xss_clean|required|callback__check_captcha');
            }
            $data['errors'] = array();

            if ($this->form_validation->run()) {                                // validation ok
                $this->_login_after(
                        $this->tank_auth->login(
                        $this->form_validation->set_value('login'),
                        $this->form_validation->set_value('password'),
                        $this->form_validation->set_value('remember'),
                        $data['login_by_username'],
                        $data['login_by_email']),
                        $go_to, $data
                        );
            }else{
                // form validation failed
                $data['errors'] = $this->form_validation->error_array();
            }
            
            $data['go_to'] = $go_to;

            return $this->_login_form($data, $login);
        }
    }

    /**
     * check user login
     *
     * @return bool
     */
    function _login_check($go_to='/')
    {
        $is_ajax = $this->input->is_ajax_request();

        if ($this->tank_auth->is_logged_in()) {                                         
        // logged in
            $is_ajax?
                die(json_encode(array('status'=>'error', 'type'=>'already_logged_in')))
                :redirect($go_to);

        } elseif ($this->tank_auth->is_logged_in(FALSE)) {                       
        // logged in, not activated
            $is_ajax?
                die(json_encode(array('status'=>'error', 'type'=>'not_activated')))
                :redirect('/auth/send_again/');

        } else {
            return true;
        }

        return false;
    }

    /**
     * login form
     *
     * @return void
     */
    function _login_form($data=null, $login=null){
        
        $data['show_captcha'] = FALSE;
        if ($this->tank_auth->is_max_login_attempts_exceeded($login)) {
            $data['show_captcha'] = TRUE;
            if ($data['use_recaptcha']) {
                $data['recaptcha_html'] = $this->_create_recaptcha();
            } else {
                $data['captcha_html'] = $this->_create_captcha();
            }
        }

        $data['fb'] = $this->fbsdk;

        $this->layout->set_view('auth/login_form', $data)->render();

    }

    /**
     * after user login
     *
     * @return bool
     */
    function _login_after($is_success, $go_to, $data)
    {
        $is_ajax = $this->input->is_ajax_request();

        if ($is_success) {
                // go_to에 따라 가야할 곳을 지정함.
                $is_ajax?
                    die(json_encode(array('status'=>'success', 'type'=>'logged_in')))
                    :redirect($go_to);

        } else {
            $errors = $this->tank_auth->get_error_message();
            if (isset($errors['banned'])) {                             
            // banned user
                $is_ajax?
                    die(json_encode(array('status'=>'error', 'type'=>'banned')))
                    :$this->_show_message($this->lang->line('auth_message_banned').' '.$errors['banned']);

            } elseif (isset($errors['not_activated'])) {               
            // not activated user
                $is_ajax?
                    die(json_encode(array('status'=>'error', 'type'=>'not_activated')))
                    :redirect('/auth/send_again/');

            } elseif ($is_ajax) {                                       
            // fail for ajax
                foreach ($errors as $k => $v)   $data['errors'][$k] = $this->lang->line($v);
                die(json_encode(array('status'=>'error', 'type'=>'post_data_error', 'errors'=>$data['errors'])));

            } else {                                                    
            // fail
                foreach ($errors as $k => $v)   $data['errors'][$k] = '<span class="error">'.$this->lang->line($v).'</span>';
            }
        }
    }

    /**
     * elevate user for admin permission
     *
     * @return void
     */
    function elevate()
    {
        $is_ajax = $this->input->is_ajax_request();
        $go_to = $this->input->post('go_to')?$this->input->post('go_to'):'/acp'; 
        # get url to go after login

        if ($this->_elevate_check($go_to)) {
            $this->data['login_by_username'] = ($this->config->item('login_by_username', 'tank_auth') AND
                    $this->config->item('use_username', 'tank_auth'));
            $this->data['login_by_email'] = $this->config->item('login_by_email', 'tank_auth');
            
            $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');
            
            $this->form_validation->set_error_delimiters('<div class="alert alert-error">','</div>');

            // Get login for counting attempts to login
            if ($this->config->item('login_count_attempts', 'tank_auth') AND
                    ($login = $this->input->post('login'))) {
                $login = $this->security->xss_clean($login);
            } else {
                $login = '';
            }

            $data['use_recaptcha'] = $this->config->item('use_recaptcha', 'tank_auth');
            if ($this->tank_auth->is_max_login_attempts_exceeded($login)) {
                if ($data['use_recaptcha'])
                    $this->form_validation->set_rules('recaptcha_response_field', 'Confirmation Code', 'trim|xss_clean|required|callback__check_recaptcha');
                else
                    $this->form_validation->set_rules('captcha', 'Confirmation Code', 'trim|xss_clean|required|callback__check_captcha');
            }
            $data['errors'] = array();

            if ($this->form_validation->run()) {                                // validation ok
                $this->_elevate_after($this->tank_auth->login(
                        $this->tank_auth->get_username(),
                        $this->form_validation->set_value('password'),
                        '',
                        $this->data['login_by_username'],
                        $this->data['login_by_email']),
                        $go_to, $data
                    );
            }

            $data['go_to'] = $go_to;

            return $this->_elevate_form($data, $login);
        }
    }

    /**
     * check user can elevate admin permission
     *
     * @return bool
     */
    function _elevate_check($go_to)
    {
        $is_ajax = $this->input->is_ajax_request();

        if ($this->nf->admin_is_elevated()) {                                          // elevated
            $is_ajax?
                die(json_encode(array('status'=>'error', 'type'=>'already_elevated')))
                :redirect($go_to);

        } elseif (!$this->tank_auth->is_logged_in()) {                           // login, first!
            $is_ajax?
                die(json_encode(array('status'=>'error', 'type'=>'please_log_in')))
                :redirect('/auth/login?go_to='.urlencode('/auth/elevate'.(($this->input->get_post('go_to'))?'?go_to='.$this->input->get_post('go_to'):'')));

        } elseif (!$this->nf->admin_check_can_elevate()) {                       // logged in, not activated
            $is_ajax?
                die(json_encode(array('status'=>'error', 'type'=>'you_cannot_elevate')))
                :redirect('/auth/restrict');

        } elseif ($this->tank_auth->is_logged_in(FALSE)) {                       // logged in, not activated
            $is_ajax?
                die(json_encode(array('status'=>'error', 'type'=>'not_activated')))
                :redirect('/auth/send_again/');

        } else {
            return true;
        }
        return false;
    }

    /**
     * login form
     *
     * @return void
     */
    function _elevate_form($data=null, $login=null){
    
        $data['show_captcha'] = FALSE;
        if ($this->tank_auth->is_max_login_attempts_exceeded($login)) {
            $data['show_captcha'] = TRUE;
            if ($data['use_recaptcha']) {
                $data['recaptcha_html'] = $this->_create_recaptcha();
            } else {
                $data['captcha_html'] = $this->_create_captcha();
            }
        }
        
        $data['fb'] = $this->fbsdk;

        $data['admin'] = array('user_id' => $this->tank_auth->get_user_id(),
                               'username' => $this->tank_auth->get_username(),
                               'realname' => $this->tank_auth->get_realname());

        $this->layout->set_view('auth/login_elevate_form', $data)->render();

    }

    /**
     * after elevate admin permission
     *
     * @return bool
     */
    function _elevate_after($is_success, $go_to, $data)
    {
        $is_ajax = $this->input->is_ajax_request();
        
        if ($this->tank_auth->login(
                $this->tank_auth->get_username(),
                $this->form_validation->set_value('password'),
                '',
                $this->data['login_by_username'],
                $this->data['login_by_email'])) {                             
        // success
            if($this->nf->admin_elevate())
            {
                // go_to에 따라 가야할 곳을 지정함.
                $is_ajax?
                    die(json_encode(array('status'=>'success', 'type'=>'elevated')))
                    :redirect(!empty($go_to)?$go_to:'/acp');
            }
            else {
                // go_to에 따라 가야할 곳을 지정함.
                $is_ajax?
                    die(json_encode(array('status'=>'error', 'type'=>'not_elevated')))
                    :redirect('/acp/redirect');
            }

        } else {
            $errors = $this->tank_auth->get_error_message();
            if (isset($errors['banned'])) {                             
            // banned user
                $is_ajax?
                    die(json_encode(array('status'=>'error', 'type'=>'banned')))
                    :$this->_show_message($this->lang->line('auth_message_banned').' '.$errors['banned']);

            } elseif (isset($errors['not_activated'])) {               
            // not activated user
                $is_ajax?
                    die(json_encode(array('status'=>'error', 'type'=>'not_activated')))
                    :redirect('/auth/send_again/');

            } elseif ($is_ajax) {                                       
            // fail for ajax
                foreach ($errors as $k => $v)   $data['errors'][$k] = $this->lang->line($v);
                die(json_encode(array('status'=>'error', 'type'=>'post_data_error', 'errors'=>$data['errors'])));

            } else {                                                    
            // fail
                foreach ($errors as $k => $v)   $data['errors'][$k] = '<span class="error">'.$this->lang->line($v).'</span>';
            }
        }
    }

    /*
     * @brief return unelevate page
     * 
     * @param void
     * 
     * @return void
     */
    function unelevate()
    {
        $this->nf->admin_unelevate();

        if($this->input->is_ajax_request()){
            die(json_encode(array('status'=>'success', 'type'=>'logged_out')));
        } else {
            redirect($this->agent->referrer());
        }
    }

	/**
	 * Logout user
	 *
	 * @return void
	 */
	function logout()
	{
        $this->nf->admin_unelevate();
		$this->tank_auth->logout();
        $this->fbsdk->destroySession(); // destory fb session

		if($this->input->is_ajax_request()){
            die(json_encode(array('status'=>'success', 'type'=>'logged_out')));
		} else {
			redirect($this->agent->referrer());
		}
	}
	
	function recommend(){
		$this->load->model('oldmodel/following_model');
		$result = $this->following_model->get_recommend($this->input->get('categories'), 12);
		$this->load->view('profile/setting/recommend_block_view', array('row' => $result));
	}
	
	function recommend_new(){
		$this->load->model('oldmodel/following_model');
		$data = $this->following_model->get_recommend_new($this->input->get('categories'), 24);
		$this->load->view('profile/setting/recommend_block_view', array('row' => $data));
	}


    /**
     * login form
     *
     * @return function
     */
	function setting()
	{
        $data = array();

        if($this->input->post('submitting')){
            $param = $this->input->post();
            if(empty($param['id'])) $param['id'] = USER_ID;
            $data['form'] = $param;
            $data = $this->_setting_put($data);
        }

        return $this->_setting_form($data);
		//$this->_form('setting');
	}

    /**
     * setting put
     *
     * @return array
     */
    function _setting_put($data=array()){
        
        $allowed_user_key = array(
                'id',
                //'username',
                'realname',
                //'email',
                'gender',
                //'birth',
                'year',
                'month',
                'day',
                'mailing',
                'fb_num_id',
            );
        $param = array();

        foreach($data['form'] as $key=>$val){
            if(in_array($key, $allowed_user_key))
                $param[$key] = $val;
        }

        if(!empty($param['year'])&&!empty($param['month'])&&!empty($param['day'])){
            $param['birth'] = implode('-', array($param['year'],$param['month'],$param['day']));
            unset($param['year'],$param['month'],$param['day']);
        }

        $this->user_model->put($param);

        
        $allowed_user_key = array(
                'fb_num_id',
                'fb_post_work',
                'fb_post_comment',
                'fb_post_note',
            );
        $param = array('user_id'=>$data['form']['id']);

        foreach($data['form'] as $key=>$val){
            if(in_array($key, $allowed_user_key))
                $param[($key=='fb_num_id')?$key:str_replace('fb_', '', $key)] = $val;
            
        }
        $this->user_model->put_sns_fb($param);
        
        return $data;
    }

    /**
     * setting form
     *
     * @return void
     */
    function _setting_form($data=array()){
        $user = $this->user_model->get_info(array(
                'id'=> USER_ID,
                'get_profile'=> true,
                'get_sns_fb'=> true
            ));

        $allowed_user_key = array(
                'id',
                //'username',
                'realname',
                'email',
                'gender',
                'birth',
                'mailing',
                'fb_num_id',
            );
        $allowed_user_key_fb = array(
                'post_work',
                'post_comment',
                'post_note',
            );

        foreach($user->row as $key=>$val){
            if(in_array($key, $allowed_user_key))
                $data[$key] = $val;
            else if(in_array($key, $allowed_user_key_fb))
                $data['fb_'.$key] = $val;
        }

        $this->layout->set_view('auth/setting_form_view', $data)->render(); 
    }

	/**
	 * next version of register
	 */
	function register()
	{
		$data = array();
		if($this->input->post('submit_uuid')!=false){
            $data = $this->_register_do_process($data);
		}
		else {
			$data = $this->_register_create_hash($data);
            $data = $this->_register_get_facebook_info($data);
		}

        return $this->_register_form($data);
	}

    /**
     * register do process
     *
     * @return void
     */
    function _register_do_process($data=array()){
        if($this->input->post('submit_uuid')==$this->session->userdata('submit_uuid')) {
            $this->load->library(array('form_validation'));
            $this->load->model('user_model');

            $this->form_validation->set_error_delimiters('↑ ', '');
            //$this->form_validation->set_error_delimiters('<p class="alert alert-danger">', '</p>');

            //-- form validation
            function set_value_to_data(){
                $ci =& get_instance();
                
                $row = array(
                    'email'         => $ci->form_validation->set_value('email'),
                    'password'      => $ci->form_validation->set_value('password'),
                    'confirm_password' => $ci->form_validation->set_value('confirm_password'),
                    'gender'        => $ci->form_validation->set_value('gender'),
                    'birth'         => implode("-", array(
                        $ci->form_validation->set_value('year'),
                        $ci->form_validation->set_value('month'),
                        $ci->form_validation->set_value('day'))),
                    'username'      => $ci->form_validation->set_value('username'),
                    'mailing'       => ($ci->form_validation->set_value('mailing')=='1')?1:0,
//                    'term'          => $ci->form_validation->set_value('term'),
                    
                );
                
                if($ci->input->post('fb_num_id')!=false){
                    //-- facebook
                    $row['fb_num_id'] = $ci->input->post('fb_num_id');
                    //-- facebook - end
                }
                
                return $row;   
            }

            $this->form_validation
                ->set_rules('email', '이메일', 'trim|required|valid_email|max_length[100]|is_unique[users.email]|is_unique[users.new_email]')
                ->set_rules('password', '비밀번호', 'trim|required|xss_clean|min_length['.$this->config->item('password_min_length', 'tank_auth').']|max_length['.$this->config->item('password_max_length', 'tank_auth').']')
                ->set_rules('confirm_password', '비밀번호 확인', 'trim|required|matches[password]')
                ->set_rules('gender', '성별', 'trim|required')
                ->set_rules('year', '생년', 'trim|numeric')
                ->set_rules('month', '생월', 'trim|numeric')
                ->set_rules('day', '생일', 'trim|numeric')
                ->set_rules('username', '개인url', 'trim|required|alpha_dash|check_username_available|xss_clean|is_unique[users.username]|min_length['.$this->config->item('username_min_length','tank_auth').']|max_length['.$this->config->item('username_max_length','tank_auth').']')
                ->set_rules('mailing', '메일링 동의', 'trim')
//                ->set_rules('term', '약관 동의', 'trim|required')
                ->set_rules('fb_num_id', '페이스북 아이디', 'trim')
                ;

            //-- end

            if($this->form_validation->run() !== FALSE){
                $data = set_value_to_data();
                log_message('debug','Data Send: '.json_encode($data));
                // 성공한 경우..
                
                //-- 회원가입 처리. with tank_auth
                $result = $this->user_model->post($data);
                
                if($result->status=="done"){ // 회원가입이 정상처리
                    $this->session->unset_userdata('submit_uuid'); // 끝났으면 쓰레기통에 꾸겨 버린다.
                    $id = $result->row->id;
                    //define('USER_ID', $id);

                    if(isset($data['fb_num_id']) && $data['fb_num_id']>0) { // facebook으로 가입시
                        $this->user_model->post_sns_fb(array('id'=>$id, 'fb_num_id'=>$data['fb_num_id'])); // facebook 등록 처리
                        
                        $this->load->library('fbsdk');
                        $fb_num_id = $this->fbsdk->getUser();

                        if($fb_num_id==$data['fb_num_id']){
                            $fbme = $this->fbsdk->api('/me');
                            $data['realname'] =  $fbme['name'];

                            $this->fbsdk->get_face($data['username']);
                            $this->fbsdk->get_bg($data['username']);
                        }
                    } else{
                        $data['realname'] = $data['username'];
                    }

                    $params = array('id'=>$id);
                    if(isset($data['realname'])) $params['realname'] = $data['realname'];
                    if(isset($data['gender']))   $params['gender']   = $data['gender'];
                    if(isset($data['birth']))    $params['birth']    = $data['birth'];
                    $params['mailing'] = ($data['mailing']==1)?1:0;

                    $result = $this->user_model->put($params, true);
                    //-- after process
                    // 이메일을 보낸다.
                    $data['site_name'] = $this->config->item('website_name', 'tank_auth');
                    if ($this->config->item('email_account_details', 'tank_auth')) {    // send "welcome" email
                        $this->user_model->_send_email('welcome', $data['email'], $data);
                    }
                    
                    // 로그인 처리
                    $this->load->library('tank_auth');
                    if ($this->tank_auth->login(
                            $data['email'],
                            $data['password'],
                            '',
                            TRUE,
                            TRUE)) {                                // success

                        $this->session->set_flashdata('welcome_newmember',true); // 가입환영용
                        $this->session->set_userdata('tutorial', '(profile)(create)'); // 튜토리얼
                        
                        redirect('/'.$data['username']);
                    }
                    //-- end   
                }       
            }else{
                // 실패한 경우.
                if ($this->form_validation->error_string()!='') {

                    $data['error'] = var_export($this->form_validation->error_string(), true);
                    $errors = $this->tank_auth->get_error_message();
                    foreach ($errors as $k => $v)   $data['errors'][$k] = $this->lang->line($v);
                    //exit(json_encode(array_merge(array('status'=>'error', 'goStep'=>$error_stage), $error_data)));
                }


                //$data = set_value_to_data($method);
            
                $data = $this->_register_create_hash($data);
            }

            //exit(json_encode($this->input->post()));
            return $data;
        }
        else{
            $data = $this->_register_create_hash($data);

            $data['submit_error'] = '올바르지 않은 접근입니다';
            //exit(json_encode(array('status'=>'error','errmsg'=>'올바르지 않은 접근입니다')));
            return $data;
        }
    }

    /**
     * register create hash
     *
     * @return void
     */
    function _register_create_hash($data=array()){
        //-- make subit uuid and save to session
        $submit_uuid = hash_init('sha1');
        hash_update($submit_uuid, time());
        hash_update($submit_uuid, 'notefolio');
        hash_update($submit_uuid, microtime(true));
        $data['submit_uuid'] = hash_final($submit_uuid);
        $this->session->set_userdata('submit_uuid', $data['submit_uuid']);
        //-- end

        return $data;
    }

    /**
     * register get facebook info
     *
     * @return void
     */
    function _register_get_facebook_info($data=array()){

        //-- join with facebook
        $fb_info = json_decode($this->session->flashdata('register_fb_info'));
        if($fb_info){
            $data['fb_info']=$fb_info;
            $data['fb_num_id']=$fb_info->id;
        }
        //-- end

        return $data;
    }

    /**
     * register form
     *
     * @return void
     */
    function _register_form($data=array()){

        //-- prevent error
        $use_username               = $this->config->item('use_username', 'tank_auth');
        $captcha_registration       = $this->config->item('captcha_registration', 'tank_auth');
        $use_recaptcha              = $this->config->item('use_recaptcha', 'tank_auth');
        $data['use_username']       = $use_username;
        $data['captcha_registration'] = $captcha_registration;
        $data['use_recaptcha']      = $use_recaptcha;
        //-- end

        $this->layout->set_view('auth/register_form', $data)->render();
    }

    /*
     * @brief check if username is available / for callback
     * 
     * @param string $username
     * @return bool
     */
	function check_username_available ($username='') {
		$this->load->model('api/auth_model');
        
		//echo 'y';
        
        //-- get from /app/config/user_restrict.php
        $this->config->load('user_restrict', TRUE, TRUE);
        
        //-- get config data
        $restrict_username  =$this->config->item('restrict_username', 'user_restrict');
        
        $return = true;
        if($restrict_username!=false){
            $restrict_username     =explode(',', $restrict_username);
            foreach ($restrict_username as $rst){
                $return = !preg_match('#'.$rst.'#', $username!='' ? $username : $this->input->get('username'));
                if($return!=true) break;
            }
        }
        
        if($return){
            $return = $this->users->is_username_available($username);
        }
        
	    if ($return){
	       $this->form_validation->set_message('check_username_available', '<b>'.$username.'</b>은(는) 사용할 수 없습니다.');
	        
	    } else {
	        
	    }
        
        return $return;
	}
	
	function check_email_available ($email='') {
        //-- get from /app/config/user_restrict.php
        $this->config->load('user_restrict', TRUE, TRUE);
        
        //-- get config data
        
        $restrict_email     =$this->config->item('restrict_email',    'user_restrict');
        
        $return = true;
        if($restrict_email!=false){
            $restrict_email     =explode(',', $restrict_email);
            foreach ($restrict_email as $rst){
                $return = !preg_match('#'.$rst.'#', $email!='' ? $email : $this->input->get('email'));
                if($return!=true) break;
            }
        }
        
        if($return){
            $return = $this->ci->users->is_email_available($email);
        }
        
        if ($return){
           $this->form_validation->set_message('check_email_available', '<b>'.$email.'</b>은(는) 사용할 수 없습니다.');
            
        } else {
            
        }
        
		return $email;
	}	

	/**
	 * Send activation email again, to the same or new email address
	 *
	 * @return void
	 */
	function send_again()
	{
		if (!$this->tank_auth->is_logged_in(FALSE)) {							// not logged in or activated
			redirect('/auth/login/');

		} else {
			$this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|valid_email');

			$data['errors'] = array();

			if ($this->form_validation->run()) {								// validation ok
				if (!is_null($data = $this->tank_auth->change_email(
						$this->form_validation->set_value('email')))) {			// success

					$data['site_name']	= $this->config->item('website_name', 'tank_auth');
					$data['activation_period'] = $this->config->item('email_activation_expire', 'tank_auth') / 3600;

					$this->_send_email('activate', $data['email'], $data);

					$this->_show_message(sprintf($this->lang->line('auth_message_activation_email_sent'), $data['email']));

				} else {
					$errors = $this->tank_auth->get_error_message();
					foreach ($errors as $k => $v)	$data['errors'][$k] = $this->lang->line($v);
				}
			}
			$this->load->view('auth/send_again_form', $data);
		}
	}

	/**
	 * Activate user account.
	 * User is verified by user_id and authentication code in the URL.
	 * Can be called by clicking on link in mail.
	 *
	 * @return void
	 */
	function activate()
	{
		$user_id		= $this->uri->segment(3);
		$new_email_key	= $this->uri->segment(4);

		// Activate user
		if ($this->tank_auth->activate_user($user_id, $new_email_key)) {		// success
			$this->tank_auth->logout();
			$this->_show_message($this->lang->line('auth_message_activation_completed').' '.anchor('/auth/login/', 'Login'));

		} else {																// fail
			$this->_show_message($this->lang->line('auth_message_activation_failed'));
		}
	}

	/**
	 * Generate reset code (to change password) and send it to user
	 *
	 * @return void
	 */
	function forgot_password()
	{
		if ($this->tank_auth->is_logged_in()) {									// logged in
			redirect('');

		} elseif ($this->tank_auth->is_logged_in(FALSE)) {						// logged in, not activated
			redirect('/auth/send_again/');

		} else {
			$this->form_validation->set_rules('login', 'Email or login', 'trim|required|xss_clean');

			$data['errors'] = array();

			if ($this->form_validation->run()) {								// validation ok
				if (!is_null($data = $this->tank_auth->forgot_password(
						$this->form_validation->set_value('login')))) {

					$data['site_name'] = $this->config->item('website_name', 'tank_auth');

					// Send email with password activation link
					$this->_send_email('forgot_password', $data['email'], $data);

					$this->_show_message($this->lang->line('auth_message_new_password_sent'));

				} else {
					$errors = $this->tank_auth->get_error_message();
					foreach ($errors as $k => $v)	$data['errors'][$k] = $this->lang->line($v);
				}
            }else{
                // form validation failed
                $data['errors'] = $this->form_validation->error_array();
            }
            $this->layout->set_view('auth/forgot_password_form', $data)->render();
		}
	}

	/**
	 * Replace user password (forgotten) with a new one (set by user).
	 * User is verified by user_id and authentication code in the URL.
	 * Can be called by clicking on link in mail.
	 *
	 * @return void
	 */
	function reset_password()
	{
		$user_id		= $this->uri->segment(3);
		$new_pass_key	= $this->uri->segment(4);

		$this->form_validation->set_rules('new_password', 'New Password', 'trim|required|xss_clean|min_length['.$this->config->item('password_min_length', 'tank_auth').']|max_length['.$this->config->item('password_max_length', 'tank_auth').']|alpha_dash');
		$this->form_validation->set_rules('confirm_new_password', 'Confirm new Password', 'trim|required|xss_clean|matches[new_password]');

		$data['errors'] = array();

		if ($this->form_validation->run()) {								// validation ok
			if (!is_null($data = $this->tank_auth->reset_password(
					$user_id, $new_pass_key,
					$this->form_validation->set_value('new_password')))) {	// success

				$data['site_name'] = $this->config->item('website_name', 'tank_auth');

				// Send email with new password
				$this->_send_email('reset_password', $data['email'], $data);

				$this->_show_message($this->lang->line('auth_message_new_password_activated').' '.anchor('/auth/login/', 'Login'));

			} else {														// fail
				$this->_show_message($this->lang->line('auth_message_new_password_failed'));
			}
		} else {
			// Try to activate user by password key (if not activated yet)
			if ($this->config->item('email_activation', 'tank_auth')) {
				$this->tank_auth->activate_user($user_id, $new_pass_key, FALSE);
			}

			if (!$this->tank_auth->can_reset_password($user_id, $new_pass_key)) {
				$this->_show_message($this->lang->line('auth_message_new_password_failed'));
			}
		}
		$this->load->view('auth/reset_password_form', $data);
	}

	/**
	 * Change user password
	 *
	 * @return void
	 */
	function change_password()
	{
		if (!$this->tank_auth->is_logged_in()) {								// not logged in or not activated
			redirect('/auth/login/');

		} else {

            $data = array(
                'errors' => array()
            );

            if($this->input->post('submitting')){
    			$this->form_validation->set_rules('old_password', 'Old Password', 'trim|required|xss_clean');
    			$this->form_validation->set_rules('new_password', 'New Password', 'trim|required|xss_clean|min_length['.$this->config->item('password_min_length', 'tank_auth').']|max_length['.$this->config->item('password_max_length', 'tank_auth').']|alpha_dash');
    			$this->form_validation->set_rules('confirm_new_password', 'Confirm new Password', 'trim|required|xss_clean|matches[new_password]');

    			if ($this->form_validation->run()) {								// validation ok
    				if ($this->tank_auth->change_password(
    						$this->form_validation->set_value('old_password'),
    						$this->form_validation->set_value('new_password'))) {	// success
    					$this->_show_message($this->lang->line('auth_message_password_changed'));

    				} else {														// fail
    					$errors = $this->tank_auth->get_error_message();
    					foreach ($errors as $k => $v)	$data['errors'][$k] = $this->lang->line($v);
    				}
                }else{
                    // form validation failed
                    $data['errors'] = $this->form_validation->error_array();
                }
            }
            $this->layout->set_view('auth/change_password_form', $data)->render(); 
		}
	}

	/**
	 * Change user email
	 *
	 * @return void
	 */
	function change_email()
	{
		if (!$this->tank_auth->is_logged_in()) {								// not logged in or not activated
			redirect('/auth/login/');

		} else {
            $data = array(
                'errors' => array()
            );

            if($this->input->post('submitting')){
    			$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');
    			$this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|valid_email');

    			if ($this->form_validation->run()) {								// validation ok
    				if (!is_null($data = $this->tank_auth->set_new_email(
    						$this->form_validation->set_value('email'),
    						$this->form_validation->set_value('password')))) {			// success

    					$data['site_name'] = $this->config->item('website_name', 'tank_auth');

    					// Send email with new email address and its activation link
    					$this->_send_email('change_email', $data['new_email'], $data);

    					$this->_show_message(sprintf($this->lang->line('auth_message_new_email_sent'), $data['new_email']));

    				} else {
    					$errors = $this->tank_auth->get_error_message();
    					foreach ($errors as $k => $v)	$data['errors'][$k] = $this->lang->line($v);
    				}
    			}else{
                    // form validation failed
                    $data['errors'] = $this->form_validation->error_array();
                }
            }
            $this->layout->set_view('auth/change_email_form', $data)->render(); 
		}
	}

	/**
	 * Replace user email with a new one.
	 * User is verified by user_id and authentication code in the URL.
	 * Can be called by clicking on link in mail.
	 *
	 * @return void
	 */
	function reset_email()
	{
		$user_id		= $this->uri->segment(3);
		$new_email_key	= $this->uri->segment(4);

		// Reset email
		if ($this->tank_auth->activate_new_email($user_id, $new_email_key)) {
        	// success
			$this->tank_auth->logout();
            $this->layout->set_view('auth/change_email_complete', array('is_success'=>true))->render(); 
			//$this->_show_message($this->lang->line('auth_message_new_email_activated').' '.anchor('/auth/login/', 'Login'));

		} else {
            // fail
            $this->layout->set_view('auth/change_email_complete', array('is_success'=>false))->render();
			//$this->_show_message($this->lang->line('auth_message_new_email_failed'));
		}
	}

	/**
	 * Delete user from the site (only when user is logged in)
	 *
	 * @return void
	 */
	function unregister()
	{
		if (!$this->tank_auth->is_logged_in()) {								// not logged in or not activated
			redirect('/auth/login/');

		} else {
			$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');

			$data['errors'] = array();

			if ($this->form_validation->run()) {								// validation ok
				if ($this->tank_auth->delete_user(
						$this->form_validation->set_value('password'))) {		// success
					$this->_show_message($this->lang->line('auth_message_unregistered'));

				} else {														// fail
					$errors = $this->tank_auth->get_error_message();
					foreach ($errors as $k => $v)	$data['errors'][$k] = $this->lang->line($v);
				}
			}
			$this->load->view('auth/unregister_form', $data);
		}
	}

    /**
     * print restrict page
     *
     * @return void
     */
    function restrict()
    {
        $this->layout->set_view('auth/restrict')->render();
    }

	/**
	 * Show info message
	 *
	 * @param	string
	 * @return	void
	 */
	function _show_message($message)
	{
		$this->session->set_flashdata('message', $message);
		redirect('/auth/');
	}

	/**
	 * Send email message of given type (activate, forgot_password, etc.)
	 *
	 * @param	string
	 * @param	string
	 * @param	array
	 * @return	void
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

	/**
	 * Create CAPTCHA image to verify user as a human
	 *
	 * @return	string
	 */
	function _create_captcha()
	{
		$this->load->helper('captcha');

		$cap = create_captcha(array(
			'img_path'		=> './'.$this->config->item('captcha_path', 'tank_auth'),
			'img_url'		=> base_url().$this->config->item('captcha_path', 'tank_auth'),
			'font_path'		=> './'.$this->config->item('captcha_fonts_path', 'tank_auth'),
			'font_size'		=> $this->config->item('captcha_font_size', 'tank_auth'),
			'img_width'		=> $this->config->item('captcha_width', 'tank_auth'),
			'img_height'	=> $this->config->item('captcha_height', 'tank_auth'),
			'show_grid'		=> $this->config->item('captcha_grid', 'tank_auth'),
			'expiration'	=> $this->config->item('captcha_expire', 'tank_auth'),
		));

		// Save captcha params in session
		$this->session->set_flashdata(array(
				'captcha_word' => $cap['word'],
				'captcha_time' => $cap['time'],
		));

		return $cap['image'];
	}

	/**
	 * Callback function. Check if CAPTCHA test is passed.
	 *
	 * @param	string
	 * @return	bool
	 */
	function _check_captcha($code)
	{
		$time = $this->session->flashdata('captcha_time');
		$word = $this->session->flashdata('captcha_word');

		list($usec, $sec) = explode(" ", microtime());
		$now = ((float)$usec + (float)$sec);

		if ($now - $time > $this->config->item('captcha_expire', 'tank_auth')) {
			$this->form_validation->set_message('_check_captcha', $this->lang->line('auth_captcha_expired'));
			return FALSE;

		} elseif (($this->config->item('captcha_case_sensitive', 'tank_auth') AND
				$code != $word) OR
				strtolower($code) != strtolower($word)) {
			$this->form_validation->set_message('_check_captcha', $this->lang->line('auth_incorrect_captcha'));
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * Create reCAPTCHA JS and non-JS HTML to verify user as a human
	 *
	 * @return	string
	 */
	function _create_recaptcha()
	{
		$this->load->helper('recaptcha');

		// Add custom theme so we can get only image
		$options = "<script>var RecaptchaOptions = {theme: 'custom', custom_theme_widget: 'recaptcha_widget'};</script>\n";

		// Get reCAPTCHA JS and non-JS HTML
		$html = recaptcha_get_html($this->config->item('recaptcha_public_key', 'tank_auth'));

		return $options.$html;
	}

	/**
	 * Callback function. Check if reCAPTCHA test is passed.
	 *
	 * @return	bool
	 */
	function _check_recaptcha()
	{
		$this->load->helper('recaptcha');

		$resp = recaptcha_check_answer($this->config->item('recaptcha_private_key', 'tank_auth'),
				$_SERVER['REMOTE_ADDR'],
				$_POST['recaptcha_challenge_field'],
				$_POST['recaptcha_response_field']);

		if (!$resp->is_valid) {
			$this->form_validation->set_message('_check_recaptcha', $this->lang->line('auth_incorrect_captcha'));
			return FALSE;
		}
		return TRUE;
	}
	
	
	function email_tester($email='kngsph@gmail.com', $type='welcome'){

		$data = array(
			'email' => $email,
			'realname' => '아무개'
		);
		
		// 이메일을 보낸다.
		$data['site_name'] = $this->config->item('website_name', 'tank_auth');
		$this->_send_email($type, $data['email'], $data);
		
		echo 'email : '.$data['email'].'<br/>status : success';
	
	}

}

/* End of file auth.php */
/* Location: ./application/controllers/auth.php */