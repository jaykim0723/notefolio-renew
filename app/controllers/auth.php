<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Auth extends CI_Controller
{
	function __construct()
	{
		parent::__construct();

		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->load->library('security');
		$this->load->library('tank_auth');
		$this->lang->load('tank_auth');
		$this->load->library('fbsdk');
		$this->load->library('user_agent');
	}

	function index()
	{
		if ($message = $this->session->flashdata('message')) {
			$this->load->view('auth/general_message', array('message' => $message));
		} else {
			redirect('/auth/login/');
		}
	}

	/**
	 * Login user on the site
	 *
	 * @return void
	 */
	function login()
	{
		$is_ajax = $this->input->is_ajax_request();
	    $go_to = $this->input->post('go_to')?$this->input->post('go_to'):'/'; # get url to go after login
        $data['go_to'] = $go_to;

		if ($this->tank_auth->is_logged_in()) {											// logged in
			$is_ajax?
			    die(json_encode(array('status'=>'error', 'type'=>'already_logged_in')))
                :redirect($go_to);

		} elseif ($this->tank_auth->is_logged_in(FALSE)) {	                     // logged in, not activated
            $is_ajax?
                die(json_encode(array('status'=>'error', 'type'=>'not_activated')))
                :redirect('/auth/send_again/');

		} else {
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

			if ($this->form_validation->run()) {								// validation ok
				if ($this->tank_auth->login(
						$this->form_validation->set_value('login'),
						$this->form_validation->set_value('password'),
						$this->form_validation->set_value('remember'),
						$data['login_by_username'],
						$data['login_by_email'])) {			
						
					// go_to에 따라 가야할 곳을 지정함.
					$is_ajax?
                        die(json_encode(array('status'=>'success', 'type'=>'logged_in')))
                        :redirect($go_to);

				} else {
					$errors = $this->tank_auth->get_error_message();
					if (isset($errors['banned'])) {								// banned user
						$is_ajax?
                            die(json_encode(array('status'=>'error', 'type'=>'banned')))
                            :$this->_show_message($this->lang->line('auth_message_banned').' '.$errors['banned']);

					} elseif (isset($errors['not_activated'])) {               // not activated user
                        $is_ajax?
                            die(json_encode(array('status'=>'error', 'type'=>'not_activated')))
                            :redirect('/auth/send_again/');

                    } elseif ($is_ajax) {                                       // fail for ajax
                        foreach ($errors as $k => $v)   $data['errors'][$k] = $this->lang->line($v);
                        die(json_encode(array('status'=>'error', 'type'=>'post_data_error', 'errors'=>$data['errors'])));

                    } else {													// fail
						foreach ($errors as $k => $v)	$data['errors'][$k] = '<span class="error">'.$this->lang->line($v).'</span>';
					}
				}
			}
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
			// $this->load->view('auth/login_form', $data);
		}
	}

    /**
     * make process for facebook
     *
     * @return void
     */
    function fb($method)
    {
        $this->load->library('fbsdk');
    	parse_str( $_SERVER['QUERY_STRING'], $_REQUEST ); // for prevent $fb_num_id == 0
		
        // load facebook library
        //$this->load->library('fbsdk'); // this has been loaded in autoload.php
        switch($method) {
            case "link":
                header('Content-Type: text/html; charset=UTF-8');
                echo("<p>처리 중입니다... 잠시만 기다려 주세요...</p>");
                
                $fb_num_id = $this->fbsdk->getUser();// get the facebook user and save in the session
                
                if(!empty($fb_num_id))
                {
                    try {
                        $fbme = $this->fbsdk->api('/me');
                    } catch (FacebookApiException $e) {
                        error_log($e);
                        $fb_num_id = null;
                        $this->fbsdk->destroySession(); 

                        exit("
                        <script>
                            <!--
                            window.location.reload();
                            -->
                        </script>
                        ");
                    }
                    
                    if($this->tank_auth->get_user_id() && ($this->uri->segment(4)==false)){
                        $this->load->model('oldmodel/auth_model');
                        
                        $this->auth_model->delete_user_fb_info($this->tank_auth->get_user_id());
                        $this->auth_model->post_user_fb_info($this->tank_auth->get_user_id(), $fb_num_id);
                          
                        $script = "
                            window.opener.location.reload();
                        ";
                        
                        exit("
                        <script>
                            <!--
                            $script
                            window.close();
                            -->
                        </script>
                        ");
                    } else if($this->uri->segment(4)=='for-register') { //for register
                        $this->load->model('oldmodel/auth_model');
                        
                        $user = $this->auth_model->get_user_info_by_fbid($fb_num_id);
                        $user_info_by_email = $this->auth_model->get_user_info("","",$fbme['email']); //-- 이메일 받아오기.
                        
			            if($user['user_id']!=0){ //-- fb 가입자
                            $this->_login_by_fb($user);
                            
                            $script = "
                                window.opener.location.reload();
                            ";                            
                        } else if($user_info_by_email['user_id']!=0){ //-- fb 가입자는 아니지만 이메일이 이미 가입된 회원
                            $this->auth_model->post_user_fb_info($user_info_by_email['user_id'], $fb_num_id);
                                
                            $this->_login_by_fb($user_info_by_email);
                            
                            $script = "
                                window.opener.location.reload();
                            ";
                        } else {
                        	//-- register 변수들 대입
                            $this->session->set_flashdata('register_fb', json_encode($fbme));
                            $go_to = "/auth/register";
                            $script = "
                                window.opener.location.href='$go_to';
                            ";
                        }
                        
                        exit("
                        <script>
                            <!--
                            $script
                            window.close();
                            -->
                        </script>
                        ");
                        
                    } else if($this->uri->segment(4)=='for-login') { //for login
                        $this->load->model('oldmodel/auth_model');

			            $user = $this->auth_model->get_user_info_by_fbid($fb_num_id);
			            if($user['user_id']==0) //-- fb 가입자가 아님
			            {
			                $user_info_by_email = $this->auth_model->get_user_info("","",$fbme['email']); //-- 이메일 받아오기.

                            if($user_info_by_email['user_id']!=0){ //-- 이메일이 이미 가입된 회원
			                    $this->auth_model->post_user_fb_info($user_info_by_email['user_id'], $fb_num_id);
                                
                                $this->_login_by_fb($user_info_by_email);
                                
			                    //=- end code
                                $script = "
                                    var $ = window.opener.$;
                                    var f = $('#login-form', window.opener.document);
                                    window.opener.location.href=$('input[name=go_to]', f).val();
                                ";
			                    
			                }else {
			                    //=- end code
                                $this->session->set_flashdata('register_fb', json_encode($fb_user_info));
                                $go_to = "/auth/register";
                                $script = "
                                    window.opener.location.href='$go_to';
                                ";
			                }
			            }
			            else
			            {
                            $this->_login_by_fb($user);
			                //=- end code
                            $script = "
                                var $ = window.opener.$;
                                var f = $('#login-form', window.opener.document);
                                window.opener.location.href=$('input[name=go_to]', f).val();
                            ";
			            }
                        
                        exit("
                        <script>
                            <!--
                            $script
                            window.close();
                            -->
                        </script>
                        ");
                        
                    } else if($this->uri->segment(4)=='for-ajax') { //for ajax
                        $this->load->model('oldmodel/auth_model');
                        
			            $user = $this->auth_model->get_user_info_by_fbid($fb_num_id);
			            if($user['user_id']==0) //-- fb 가입자가 아님
			            {
			                $user_info_by_email = $this->auth_model->get_user_info("","",$fbme['email']); //-- 이메일 받아오기.
			                if($user_info_by_email['user_id']!=0){ //-- 이메일이 이미 가입된 회원
			                    $this->auth_model->post_user_fb_info($user_info_by_email['user_id'], $fb_num_id);
                                
                                $this->_login_by_fb($user_info_by_email);
                                
			                    //=- end code
                                $script = "
                                    window.opener.auth.afterLogin();
                                ";
			                    
			                }else {
			                    //=- end code
                                $this->session->set_flashdata('register_fb', json_encode($fbme));
                                $go_to = "/auth/register";
                                $script = "
                                    window.opener.location.href='$go_to';
                                ";
			                }
			            }
			            else
			            {
                            $this->_login_by_fb($user);
			                //=- end code
                            $script = "
                                    window.opener.auth.afterLogin();
                            ";
			            }
                        
                        exit("
                        <script>
                            <!--
                            $script
                            window.close();
                            -->
                        </script>
                        ");
                        
                    } else if($this->uri->segment(4)=='for-debug') { //for debug
		                $fb_user_info = $this->fbsdk->api('/me');
		                exit(var_export($fb_user_info, true));
                        
                        
                    } else $go_to = "/auth/login?go_to=/auth/setting";

                    exit("
                    <script>
                        <!--
                        window.opener.location.href='$go_to';
                        window.close();
                        -->
                    </script>
                    ");
                    
                }
                else 
                { 
                    header('Content-Type: text/html; charset=UTF-8');
                    echo("<p>Facebook으로 연결하는 중입니다... 잠시만 기다려 주세요...</p>");
                    
                    if ($this->input->get('error_reason')=="user_denied"){
                        echo("
                        <script>
                            <!--
                            window.opener.msg.error('에러가 발생하였습니다.<br/>페이스북에서 앱 승인을 하지 않으면 연동할 수 없습니다.');
                            window.close();
                            -->
                        </script>
                        ");
                    }
                    else {
                        // Login or logout url will be needed depending on current user state.
                        if($fb_num_id) {
                            $fb_num_id = null;
                            $this->fbsdk->destroySession();

                            exit("
                            <script>
                                <!--
                                window.location.reload();
                                -->
                            </script>
                            ");
                        } else {
                            $link = $this->fbsdk->getLoginUrl(array(
                                'scope'         =>'email,user_likes,user_photos,user_birthday,offline_access,publish_stream,publish_actions',
                                'redirect_uri'  =>$this->config->item('base_url').'auth/fb/link/'.(($this->uri->segment(4)!=false)?$this->uri->segment(4)."/":'').'status:complete/',
                                'display'       =>'popup'
                            ));
                            //var_export($link);
                            //exit();
                            redirect($link);
                        }
                    } 
                }
                break;
            case "set_action":
                $result = $this->fbsdk->set_data($this->tank_auth->get_user_id(), $this->input->post());
                echo($result?'TRUE':'FALSE');
                return $result;
                break;
        }
        
        echo("$method");
        return FALSE;
    }

    function _login_by_fb($user){
        // simulate what happens in the tank auth
        $this->session->set_userdata(array( 
								'user_id'	=> $user['user_id'],
								'username'	=> $user['username'],
								'status'	=> ($user['activated'] == 1) ? STATUS_ACTIVATED : STATUS_NOT_ACTIVATED,
                                'realname'  => $user['realname'],  // realname을 위해서.
                                'p_i'       => (file_exists(APPPATH.'../www/profiles/'.$user['user_id']))?time():0,// 아이콘 출력을 위해서.
                                'level'     => $user['level'],  // magazine-level
                            ));
        //$this->tank_auth->clear_login_attempts($user[0]->email); can't run this when doing FB
        $this->users->update_login_info( $user['user_id'], $this->config->item('login_record_ip', 'tank_auth'), 
                                         $this->config->item('login_record_time', 'tank_auth'));
        
        return $user['user_id'];
    }

	/**
	 * Logout user
	 *
	 * @return void
	 */
	function logout()
	{
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
	function setting()
	{
		$this->_form('setting');
	}

	/**
	 * next version of register
	 */
	function register()
	{
		$this->nf->_member_check(FALSE); //member check
		if(MY_ID > 0)
			redirect('/');
            
        $data = array();
		if($this->input->post('submit_uuid')!=false){
            if($this->input->post('submit_uuid')==$this->session->userdata('submit_uuid')) {
                $this->load->library(array('form_validation'));
                $this->load->model('oldmodel/auth_model');
                
                //-- form validation
                function set_value_to_data(){
                    $ci =& get_instance();
                    
                    $row = array(
                        //-- stage 0
                        'email'         => $ci->form_validation->set_value('email'),
                        //'confirm_email' => $ci->form_validation->set_value('confirm_email'),
                        'password'      => $ci->form_validation->set_value('password'),
                        'confirm_password' => $ci->form_validation->set_value('confirm_password'),
                        
                        //-- stage 1
                        'realname'      => $ci->form_validation->set_value('realname'),
                        'gender'        => $ci->form_validation->set_value('gender'),
                        'birth'         => implode("-", array($ci->input->post('year'),$ci->input->post('month'),$ci->input->post('day'))),
                        'username'      => $ci->form_validation->set_value('username'),
                        'mailing'       => ($ci->form_validation->set_value('mailing')=='1')?1:0,
                        'term'          => $ci->form_validation->set_value('term'),
                        
                        //-- stage 2
                        'categories'    => $ci->form_validation->set_value('categories'),
                        
                        //-- stage 3
                        'recommend'    => $ci->form_validation->set_value('recommend'),
                    );
                    
                    if($ci->input->post('fb_num_id')!=false){
                        //-- facebook
                        $row['fb_num_id'] = $ci->input->post('fb_num_id');
                        //-- facebook - end
                    }
                    
                    return $row;   
                }
                
                $this->form_validation
                    //-- stage 0
                    ->set_rules('email', '이메일', 'trim|required|valid_email|max_length[100]|is_unique[users.email]')
                    //->set_rules('confirm_email', '이메일확인', 'trim|required|matches[email]')
                    ->set_rules('password', '비밀번호', 'trim|required|xss_clean|min_length['.$this->config->item('password_min_length', 'tank_auth').']|max_length['.$this->config->item('password_max_length', 'tank_auth').']')
                    ->set_rules('confirm_password', '비밀번호 확인', 'trim|required|matches[password]')
                    
                    //-- stage 1
                    ->set_rules('realname', '이름', 'trim|required')
                    ->set_rules('gender', '성별', 'trim|required')
                    ->set_rules('username', '개인url', 'trim|required|alpha_dash|check_username_available|xss_clean')
                    ->set_rules('mailing', '메일링 동의', 'trim')
                    ->set_rules('term', '약관 동의', 'trim|required')
                    
                    //-- stage 2
                    ->set_rules('categories', '키워드', 'trim|required')
                    
                    //-- stage 3
                    ->set_rules('recommend', '팔로우 추천', 'trim');
                
                //-- end
                
                if($this->form_validation->run() !== FALSE){
                    $data = set_value_to_data();
                    log_message('debug','Data Send: '.json_encode($data));
                    // 성공한 경우..
                    
                    //-- 회원가입 처리. tank_auth
                    $result = $this->auth_model->post_user_info_new($data);
                    
                    if($result){ // 회원가입이 정상처리
                        $this->session->unset_userdata('submit_uuid'); // 끝났으면 쓰레기통에 꾸겨 버린다.
                        $result = $this->auth_model->data;
                        $id = $result['user_id'];
                        //-- after process
                        // 이메일을 보낸다.
                        $data['site_name'] = $this->config->item('website_name', 'tank_auth');
                        if ($this->config->item('email_account_details', 'tank_auth')) {    // send "welcome" email
                            $this->auth_model->_send_email('welcome', $data['email'], $data);
                        }
                        
                        // 로그인 처리
                        $this->load->library('tank_auth');
                        if ($this->tank_auth->login(
                                $data['email'],
                                $data['password'],
                                '',
                                TRUE,
                                TRUE)) {								// success
                            if($data['fb_num_id']) // facebook 등록 처리 (facebook으로 가입시)
                                $this->auth_model->post_user_fb_info($id, $data['fb_num_id']);
                            
                            $this->session->set_flashdata('welcome_newmember',true); // 가입환영용
                            
                            exit(json_encode(array('status'=>'success', 'username'=>$result['username'])));
                        }
                        //-- end   
                    }	   	
                }else{
                    // 실패한 경우.
                    if ($this->form_validation->error_string()!='') {
                        
                        //-- 검증 check 용
                        function set_error_data($stage){
                            $ci =& get_instance();
                            
                            foreach ($stage as $v){
                                if($ci->form_validation->error($v)!=''){
                                    return array("name"=>$v, "errmsg"=>$ci->form_validation->error($v));
                                }
                            }
                            
                            return array();
                        }
                        
                        //-- stage 0
                        $error_data = set_error_data(array(
                        'email',
                        //'confirm_email',
                        'password',
                        'confirm_password'
                        ));
                        $error_stage = 0;
                        
                        //-- stage 1
                        if(count($error_data)<1){
                            $error_data = set_error_data(array(
                                'realname',
                                'gender',
                                'username',
                                'mailing',
                                'term'
                            ));
                            $error_stage++;
                        }
                        
                        //-- stage 2
                        if(count($error_data)<1){
                            $error_data = set_error_data(array(
                                'categories'
                            ));
                            $error_stage++;
                        }
                        
                        //-- stage 3
                        if(count($error_data)<1){
                            $error_data = set_error_data(array(
                                'recommend'
                            ));
                            $error_stage++;
                        }
                        
                        exit(json_encode(array_merge(array('status'=>'error', 'goStep'=>$error_stage), $error_data)));
                    }
                   
                    $data = set_value_to_data($method);
                }
                
                exit(json_encode($this->input->post()));
            }
            else{
                exit(json_encode(array('status'=>'error','errmsg'=>'올바르지 않은 접근입니다')));
            }
		}
		else {
			//-- make subit uuid and save to session
			$submit_uuid = hash_init('sha1');
			hash_update($submit_uuid, time());
			hash_update($submit_uuid, 'notefolio');
			hash_update($submit_uuid, microtime(true));
			$data['submit_uuid'] = hash_final($submit_uuid);
            $this->session->set_userdata('submit_uuid', $data['submit_uuid']);
			//-- end
			
			//-- join with facebook
            $fb_info = json_decode($this->session->flashdata('register_fb'));
            if($fb_info!=false){
                $data['fb_info']=$fb_info;
                $data['fb_num_id']=$fb_info->id;
            }
            //-- end
		}
        $this->layout->set_view('auth/register_form_view', $data)->render();
	}
	/**
	 *	가입 및 수정에서 공통으로 쓰이는 폼
	 */
	function _form($method='setting')
	{
		$this->notefolio->_member_check($method == 'setting' ? TRUE : FALSE);

		if($method=='register' && MY_ID>0)
			redirect('/'); // 회원인 상태에서 register로 접근하는 사람은 초기화면으로 강제 이동시킴

		$this->load->library(array('form_validation'));
		$this->load->model('oldmodel/auth_model');
		
		$data = array();

		if(!$this->input->post('submitting')){
		  // first time
		  if($method=='register'){ // 아직 회원이 아니다. 가입폼을 위함.
			   // create;
			   // 기본값을 셋팅해준다.
			   $data = array(
			   		'email' => '',
			   		'confirm_email' => '',
			   		'password' => '',
			   		'confirm_password' => '',
			   		'gender' => 'f',
			   		'realname' => '',
			   		'birth' => '1990-08-08',
			   		'username' => '',
			   		'categories' => '',
			   		'description' => '자신의 소개를 입력해주세요.',
			   		'homepage' => '',
			   		'facebook_url' => '',
			   		'twitter_screen_name' => '',
			   		'profile_image' => '/images/profile_img',
                    
			   		'invite_code' => $this->input->post('invite_code')
			   );
               
               //-- for facebook signup
               if($this->input->post('register_type')=='facebook'){
                    $fb_info = $this->fbsdk->api('/me');
                    //echo "<!-- ".var_export($fb_info, true)." -->\n";
                    
                    if(isset($fb_info['email'])) {
                        $data['email'] = $fb_info['email'];
                        $data['confirm_email'] = $fb_info['email'];
                    }
                    if(isset($fb_info['gender'])) {
                        $data['gender'] = substr(strtolower($fb_info['gender']), 0, 1);
                    }
                    if(isset($fb_info['username'])) {
                        $data['facebook_url'] = $fb_info['username'];
                        $data['username'] = $fb_info['username'];
                    }
                    if(isset($fb_info['name'])) {
                        $data['realname'] = $fb_info['name'];
                    }
                    if(isset($fb_info['bio'])) {
                        $data['description'] = $fb_info['bio'];
                    }
                    if(isset($fb_info['birthday'])) {
                        $data['birth'] = date("Y-m-d", strtotime($fb_info['birthday']));;
                    }
                    
                    $data['fb_num_id'] = $fb_info['id'];
               }
		  }else{
			   // update
			   $data = $this->auth_model->get_user_info(MY_ID);
			   log_message('debug','-------------'.json_encode($data));
		  }
		}else{
			// action
			// 폼을 검증한다.
			function set_value_to_data($method){
			    $ci =& get_instance();
                
			   	$row = array(
			   		'gender'                => $ci->form_validation->set_value('gender'),
			   		'realname'              => $ci->form_validation->set_value('realname'),
			   		'username'              => $ci->form_validation->set_value('username'),
			   		'categories'            => $ci->form_validation->set_value('categories'),
			   		'homepage'              => $ci->form_validation->set_value('homepage'),
			   		'facebook_url'          => $ci->form_validation->set_value('facebook_url'),
			   		'twitter_screen_name'   => $ci->form_validation->set_value('twitter_screen_name'),
                    'mailing'               => ($ci->form_validation->set_value('mailing')=='1')?1:0,
			   		'birth'                 => implode("-", array($ci->input->post('year'),$ci->input->post('month'),$ci->input->post('day'))),
			   	);
                if($method=='register'){
                    $row['email']           = $ci->form_validation->set_value('email');
                    $row['confirm_email']   = $ci->form_validation->set_value('confirm_email');
                    $row['password']        = $ci->form_validation->set_value('password');
                    $row['confirm_password'] = $ci->form_validation->set_value('confirm_password');
                    $row['term']            = $ci->form_validation->set_value('term');
                    $row['privacy']         = $ci->form_validation->set_value('privacy');
                    $row['invite_code']     = $ci->form_validation->set_value('invite_code');
                }else if($method=='setting'){
                    //-- facebook
                    $row['fb_num_id'] = $ci->input->post('fb_num_id');
                    $row['fb_post_work'] = $ci->input->post('fb_post_work');
                    $row['fb_post_comment'] = $ci->input->post('fb_post_comment');
                    $row['fb_post_note'] = $ci->input->post('fb_post_note');
                    //-- facebook - end
                }
                
				return $row;    
			}
			
			$this->load->library('form_validation');
			
			// 입력값의 검사조건 설정(이 함수는 수정할 때에도 동일하게 쓰임)
			if($method=='register'){
				// 회원가입시에만
				// setting에서는 링크를 이용하여 auth로 이동(이메일 변경 및 비밀번호 변경)
				$this->form_validation
					->set_rules('email', '이메일', 'trim|required|valid_email|max_length[100]|is_unique[users.email]')
					->set_rules('confirm_email', '이메일확인', 'trim|required|matches[email]')
					->set_rules('password', '비밀번호', 'trim|required|xss_clean|min_length['.$this->config->item('password_min_length', 'tank_auth').']|max_length['.$this->config->item('password_max_length', 'tank_auth').']')
					->set_rules('confirm_password', '비밀번호 확인', 'trim|required|matches[password]')
					->set_rules('term', '약관 동의', 'trim|required')
					->set_rules('privacy', '개인정보보호정책 동의', 'trim|required')
					->set_rules('invite_code', '초대장번호', 'trim|required|coupon_check');
			}else{
				// 수정시에만
			}
			// 공통
			$this->form_validation
				->set_rules('gender', '성별', 'trim|required')
				->set_rules('realname', '이름', 'trim|required')
				->set_rules('username', '개인url', 'trim|required|alpha_dash|check_username_available|xss_clean')
				->set_rules('categories', '키워드', 'trim|required')
				->set_rules('homepage', '웹사이트', 'trim')
				->set_rules('facebook_url', '페이스북', 'trim')
				->set_rules('twitter_screen_name', '트위터', 'trim')
                ->set_rules('mailing', '메일링 동의', 'trim');
			
			if($this->form_validation->run() !== FALSE){
			   	$data = set_value_to_data($method);
                log_message('debug','Data Send: '.json_encode($data));
			   	// 성공한 경우..
			   	if($method=='register'){ // 회원가입 처리. tank_auth
			   		$data['recommend'] = $this->input->post('recommend');
log_message('debug', ' -------- data ----------'.json_encode($data));			   		
			   		$result = $this->auth_model->post_user_info($data);
log_message('debug', ' -------- resurt ----------'.json_encode($result));			   		
			   		if($result=='invite_error'){
			   			;// 초대장이 에러난 경우이다.
			   		}
			   	}else{ // 수정처리
			   		$result = $this->auth_model->put_user_info($data);
			   	}
				if(
					($method=='register' && is_array($result))
					OR
					($method=='setting' && $result !== FALSE)
				){ // 회원가입이 정상처리되어 username
					$id = ($method=='register' ? $result['user_id'] : MY_ID);
					if(strpos($this->input->post('thumbnail_url'), 'temp')){
						// 임시로 들어온 파일이다.
						@unlink(APPPATH.'../www/profiles/'.$id);
						rename(APPPATH.'../www'.$this->input->post('thumbnail_url'), APPPATH.'../www/profiles/'.$id);
						$this->session->set_userdata('p_i', time());
					}
				   	if($method=='register'){ // 회원가입 처리. tank_auth
				   	
				   		// 이메일을 보낸다.
				   		$data['site_name'] = $this->config->item('website_name', 'tank_auth');
				   		$this->_send_email('welcome', $data['email'], $data);
				   		
				   		// 로그인 처리
				   		$this->load->library('tank_auth');
						if ($this->tank_auth->login(
								$data['email'],
								$data['password'],
								'',
								TRUE,
								TRUE)) {								// success
								
				   		    if($this->input->post('fb_num_id')) // facebook 등록 처리 (facebook으로 가입시)
				   		        $this->auth_model->post_user_fb_info($result['user_id'], $this->input->post('fb_num_id'));
                            
							exit(json_encode(array('code'=>'success', 'username'=>$result['username'])));
						}
					}else
                    	/*redirect($result); // 해당 유저의 프로필 페이지로 이동        */
                    	redirect('/auth/setting'); // setting으로 다시 이동
				}	   	
			}else{
			   // 실패한 경우.
			   // 다시 폼이 열리도록..(근데 아마 ajax로 값을 받아서 자바스크립트에서 처리할 듯)
			   if ($method=='register' && validation_errors()!='') {
			       exit(json_encode(array('code'=>'error', 'message'=>validation_errors())));
			   }
			   
			   $data = set_value_to_data($method);
			}
		  
		}
        $this->layout->set_view('auth/'.$method.'_form_view', $data)->render();		
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
            $return = $this->auth_model->check_username_available($username!='' ? $username : $this->input->get('username'));
        }
        
	    if ($return){
	       $this->form_validation->set_message('_check_username_available', '<b>'.$username.'</b>은(는) 사용할 수 없습니다.');
	        
	    } else {
	        
	    }
	    
	    if($username==''){ // ajax check
	    	echo $return ? 'y' : 'n';
	    }else{ // function check
	    
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
            //$this->load->model('tank_auth/users');
            //$return = (strlen($email) > 0) AND $this->users->is_email_available($email);
            $this->load->model('oldmodel/auth_model');
            $return = $this->auth_model->check_email_available($email!='' ? $email : $this->input->get('email'));
        }
        
		echo ($return) ? 'y' : 'n';		
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
			$this->form_validation->set_rules('old_password', 'Old Password', 'trim|required|xss_clean');
			$this->form_validation->set_rules('new_password', 'New Password', 'trim|required|xss_clean|min_length['.$this->config->item('password_min_length', 'tank_auth').']|max_length['.$this->config->item('password_max_length', 'tank_auth').']|alpha_dash');
			$this->form_validation->set_rules('confirm_new_password', 'Confirm new Password', 'trim|required|xss_clean|matches[new_password]');

			$data['errors'] = array();

			if ($this->form_validation->run()) {								// validation ok
				if ($this->tank_auth->change_password(
						$this->form_validation->set_value('old_password'),
						$this->form_validation->set_value('new_password'))) {	// success
					$this->_show_message($this->lang->line('auth_message_password_changed'));

				} else {														// fail
					$errors = $this->tank_auth->get_error_message();
					foreach ($errors as $k => $v)	$data['errors'][$k] = $this->lang->line($v);
				}
			}
			$this->load->view('auth/change_password_form', $data);
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
			$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');
			$this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|valid_email');

			$data['errors'] = array();

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
			}
			$this->load->view('auth/change_email_form', $data);
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
		if ($this->tank_auth->activate_new_email($user_id, $new_email_key)) {	// success
			$this->tank_auth->logout();
			$this->_show_message($this->lang->line('auth_message_new_email_activated').' '.anchor('/auth/login/', 'Login'));

		} else {																// fail
			$this->_show_message($this->lang->line('auth_message_new_email_failed'));
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