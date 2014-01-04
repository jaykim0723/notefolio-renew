<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Facebook extends CI_Controller
{
	function __construct()
	{
		parent::__construct();

        $this->load->library('fbsdk');
	}

	function index()
	{
		if ($message = $this->session->flashdata('message')) {
			$this->load->view('auth/general_message', array('message' => $message));
		} else {
			redirect('/facebook/login/');
		}
	}

    /**
     * print prepare message
     *
     * @return void
     */
    function _prepare(){
        header('Content-Type: text/html; charset=UTF-8');
        echo("<p>처리 중입니다... 잠시만 기다려 주세요...</p>");
    }

    /**
     * post action
     *
     * @return bool
     */
    function _post(){
        $result = $this->fbsdk->set_data($this->tank_auth->get_user_id(), $this->input->post());
        echo($result?'TRUE':'FALSE');
        return $result;
    }

    /**
     * error message
     *
     * @param string $type
     *
     * @return void
     */
    function _error($type){
        switch($type){
            case "user_denied"):
                $message = "에러가 발생하였습니다.<br/>페이스북에서 앱 승인을 하지 않으면 연동할 수 없습니다.");
            break;
        }

        $this->make_error_alert($message);
    }

    /**
     * print error alert
     *
     * @param string $type
     *
     * @return void
     */
    function _make_error_alert($text){
        $tpl = "
        <script>
             window.opener.msg.error('{$text}');
        </script>
        ");

        exit($tpl);
    }

    /**
     * window reload js
     *
     * @return void
     */
    function _window_reload(){
        $tpl = "
        <script>
            <!--
            $script
            window.location.reload();
            -->
        </script>
        ");

        exit($tpl);
    }

    /**
     * window close js
     *
     * @return void
     */
    function _window_close(){
        $tpl = "
        <script>
            <!--
            $script
            window.close();
            -->
        </script>
        ");

        exit($tpl);
    }

    /**
     * check if ok to connect
     *
     * @return object
     */
    function _check_fb_connection(){
        try {
            $fbme = $this->fbsdk->api('/me');
        } catch (FacebookApiException $e) {
            error_log($e);
            $this->fbsdk->destroySession();
            $fbme = 0;
        }

        return $fbme;
    }
    

    /**
     * check and return fb info
     *
     * @return void
     */
    function _check(){
        parse_str( $_SERVER['QUERY_STRING'], $_REQUEST ); // for prevent $fb_num_id == 0
                
        $fb_num_id = $this->fbsdk->getUser();// get the facebook user and save in the session
        
        if(!empty($fb_num_id))
        {
            $fb_myinfo = $this->_check_fb_connection();
            if($fbme==0){
                $fb_num_id = null;
                $this->_go_fb_app('login');
            }
        }
        else 
        {
            if ($this->input->get('error_reason')){
                return $this->_error($this->input->get('error_reason'));
            }
            else {
                $this->_go_fb_app('login');
            } 
        }

        return $fb_myinfo;
        
    }
    

    /**
     * go to facebook app - redirect
     *
     * @param string $type
     *
     * @return void
     */
    function _go_fb_app($type){
    
        $link = $this->fbsdk->getLoginUrl(array(
            'scope'         =>'email,user_likes,user_photos,user_birthday,offline_access,publish_stream,publish_actions',
            'redirect_uri'  =>$this->config->item('base_url').'facebook/'.$type.'/status:complete/',
            'display'       =>'popup'
        ));
        //var_export($link);
        //exit();
        redirect($link);
    }

    /**
     * login for notefolio
     *
     *
     */
    function login(){
        $this->_prepare();
        $fbme = $this->_check();

        $this->load->model('user_model');

        $user = $this->user_model->get_info(array('sns_fb_num_id'=>$fb_num_id, 'get_sns_fb'=>true));
        
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


    }



    function _login_by_fb($user){
        // simulate what happens in the tank auth
        $this->session->set_userdata(array( 
                                'user_id'   => $user['user_id'],
                                'username'  => $user['username'],
                                'status'    => ($user['activated'] == 1) ? STATUS_ACTIVATED : STATUS_NOT_ACTIVATED,
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
     * make process for facebook
     *
     * @return void
     */
    function fb($method)
    {
    	parse_str( $_SERVER['QUERY_STRING'], $_REQUEST ); // for prevent $fb_num_id == 0
		
        // load facebook library
        //$this->load->library('fbsdk'); // this has been loaded in autoload.php
        switch($method) {
            case "link":
                
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
                            window.close();
                            -->
                             window.opener.msg.error('에러가 발생하였습니다.<br/>페이스북에서 앱 승인을 하지 않으면 연동할 수 없습니다.');
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
                break;
        }
        
        echo("$method");
        return FALSE;
    }

}

/* End of file facebook.php */
/* Location: ./application/controllers/facebook.php */