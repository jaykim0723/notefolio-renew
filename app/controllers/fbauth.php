<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class fbauth extends CI_Controller
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
			redirect('/'.$this->uri->segment(1).'/login/');
		}
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
            $fbme = $this->_check_fb_connection();
            if($fbme==0){
                $fb_num_id = null;
                $this->_go_fb_app();
            }
        }
        else 
        {
            if ($this->input->get('error_reason')){
                return $this->_error($this->input->get('error_reason'));
            }
            else {
                $this->_go_fb_app();
            } 
        }

        return $fbme;
        
    }
    

    /**
     * check and return fb info
     *
     * @return void
     */
    function get_face($w=0, $h=0, $save_to=''){                
        $fb_num_id = $this->fbsdk->getUser();// get the facebook user and save in the session
        
        if(!empty($fb_num_id))
        {   
            $result = $this->fbsdk->get_face('fb_'.$fb_num_id);
            var_export($result);
            exit();
        }

        return true;
    }
    

    /**
     * check and return fb info
     *
     * @return void
     */
    function get_cover($w=0, $h=0, $save_to=''){                
        $fb_num_id = $this->fbsdk->getUser();// get the facebook user and save in the session
        
        if(!empty($fb_num_id))
        {
            $result = $this->fbsdk->get_cover('fb_'.$fb_num_id);
            var_export($result);
            exit();
        }

        return true;
    }
    

    /**
     * go to facebook app - redirect
     *
     * @return void
     */
    function _go_fb_app($type){
    
        $link = $this->fbsdk->getLoginUrl(array(
            'scope'         =>'email,user_likes,user_photos,user_birthday,offline_access,publish_stream,publish_actions',
            'redirect_uri'  =>$this->config->item('base_url').$this->uri->segment(1).'/'.$this->uri->segment(2).'/'.(($this->uri->segment(3))?$this->uri->segment(3)."/":'regular/').'status:complete/',
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
    function login($type='regular'){
        $this->_prepare();
        $fbme = $this->_check();

        $this->load->model('user_model');

        $user = $this->user_model->get_info(array('sns_fb_num_id'=>$fbme['id'], 'get_sns_fb'=>true));
        
        if($user->status=='fail'||count($user->row)<1) //-- fb 가입자가 아님
        {
            $user_by_email = $this->user_model->get_info(array('email'=>$fbme['email'])); //-- 이메일 받아오기.

            if($user_by_email->status=='done'&&count($user_by_email->row)>0){ //-- 이메일이 이미 가입된 회원
                $this->user_model->post_sns_fb(array('id'=>$user_by_email->row->id, 'fb_num_id'=>$fbme['id']));
                
                $this->_login_by_fb($user->row);            
            }else {
                return $this->register();
            }
        }
        else
        {
            $this->_login_by_fb($user->row);
        }

        if($type=='ajax')
            $this->_window_opener_ajax();
        else if($type=='externel')
            $this->_window_opener_reload();  
        else
            $this->_window_opener_move();    

        return $this->_window_close();
    }

    function _login_by_fb($user){
        // simulate what happens in the tank auth
        $this->session->set_userdata(array( 
                                'user_id'   => $user->id,
                                'username'  => $user->username,
                                'status'    => ($user->activated == 1) ? STATUS_ACTIVATED : STATUS_NOT_ACTIVATED,
                                'realname'  => $user->realname,  // realname을 위해서.
                                'p_i'       => (file_exists(APPPATH.'../www/profiles/'.$user->id))?time():0,// 아이콘 출력을 위해서.
                                'level'     => $user->level,  // magazine-level
                            ));
        //$this->tank_auth->clear_login_attempts($user[0]->email); can't run this when doing FB
        $this->users->update_login_info( $user->id, $this->config->item('login_record_ip', 'tank_auth'), 
                                         $this->config->item('login_record_time', 'tank_auth'));
        
        return $user->id;
    }

    /**
     * register for notefolio
     *
     *
     */
    function register($type='regular'){
        $this->_prepare();
        $fbme = $this->_check();

        $this->load->model('user_model');

        $user = $this->user_model->get_info(array('sns_fb_num_id'=>$fbme['id'], 'get_sns_fb'=>true));
        $user_by_email = $this->user_model->get_info(array('email'=>$fbme['email'])); //-- 이메일 받아오기.
        
        if($user->status=='done'&&count($user->row)>0){ //-- fb 가입자
            $this->_login_by_fb($user->row);
            $this->user_model->put_sns_fb(array('id'=>$user->row->id, 'fb_num_id'=>$fbme['id']));
            
            $this->_window_opener_move('/');
        } else if($user_by_email->status=='done'&&count($user_by_email->row)>0){ //-- fb 가입자는 아니지만 이메일이 이미 가입된 회원
            $this->user_model->post_sns_fb(array('id'=>$user_by_email->row->id, 'fb_num_id'=>$fbme['id']));
            $this->_login_by_fb($user_by_email->row);
            
            $this->_window_opener_move('/');
        } else {
            //-- register 변수들 대입
            $this->session->set_flashdata('register_fb_info', json_encode($fbme));

            $this->_window_opener_move("/auth/register");
        }

        return $this->_window_close();
    }

    /**
     * register for notefolio
     *
     *
     */
    function link($mode='regular'){
        $this->_prepare();
        if(USER_ID==0){
            $this->_error('require_login');

            return $this->_window_close();
        }

        $fbme = $this->_check();

        $this->load->model('user_model');

        if($mode=='force'){
            $this->user_model->delete_sns_fb(array('id'=>USER_ID, 'fb_num_id'=>$fbme['id']));
            $this->user_model->post_sns_fb(array('id'=>USER_ID, 'fb_num_id'=>$fbme['id']));
        }
        else {
            $user = $this->user_model->get_info(array('sns_fb_num_id'=>$fbme['id'], 'get_sns_fb'=>true));
            if($user->status=='done'&&count($user->row)>0)
                $this->_error('already_linked');
            else
                $this->user_model->post_sns_fb(array('id'=>USER_ID, 'fb_num_id'=>$fbme['id']));


        }

        $this->_window_opener_reload();

        return $this->_window_close();
    }

    /**
     * register for notefolio
     *
     *
     */
    function unlink($mode='regular'){
        $this->_prepare();
        if(USER_ID==0){
            $this->_error('require_login');

            return $this->_window_close();
        }

        $fbme = $this->_check();

        $this->load->model('user_model');

        if($mode=='force'){
            $this->user_model->delete_sns_fb(array('id'=>USER_ID, 'fb_num_id'=>$fbme['id']));
        }
        else {
            $user = $this->user_model->get_info(array('sns_fb_num_id'=>$fbme['id'], 'get_sns_fb'=>true));
            if($user->status=='done'&&count($user->row)>0)
                $this->user_model->delete_sns_fb(array('id'=>USER_ID, 'fb_num_id'=>$fbme['id']));
            else
                $this->_error('not_found');
        }
        
        $this->_window_opener_reload();

        return $this->_window_close();

    }

    /**
     * debug info
     *
     *
     */
    function debug(){
        $this->_prepare();
        $fbme = $this->_check();

        exit(var_export($fbme, true));
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
     * @return function
     */
    function _error($type){
        switch($type){
            case "user_denied":
                $message = "에러가 발생하였습니다.<br/>페이스북에서 앱 승인을 하지 않으면 연동할 수 없습니다.";
            break;
            case "already_linked":
                $message = "에러가 발생하였습니다.<br/>페이스북과 이미 연동하고 있습니다.";
            break;
            case "not_found":
                $message = "에러가 발생하였습니다.<br/>페이스북 연동 정보가 없습니다.";
            break;
            case "require_login":
                $message = "에러가 발생하였습니다.<br/>먼저 로그인을 해주세요.";
            break;
        }

        return $this->_make_error_alert($message);
    }

    /**
     *
     * print functions
     *
     */

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
            <!--
            window.opener.msg.error('{$text}');
            -->
        </script>
        ";

        echo($tpl);
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
            window.location.reload();
            -->
        </script>
        ";

        exit($tpl);
    }

    /**
     * window opener reload js
     *
     * @return void
     */
    function _window_opener_reload(){
        $tpl = "
        <script>
            <!--
            window.opener.location.reload();
            -->
        </script>
        ";

        echo($tpl);
    }

    /**
     * window opener move js
     *
     * @return void
     */
    function _window_opener_move($go_to=null){
        $tpl = "
        <script>
            <!--
        ";

        if(empty($go_to)){
            $tpl .= "
                var $ = window.opener.$;
                var f = $('#login-form', window.opener.document);
                window.opener.location.href=$('input[name=go_to]', f).val();
            ";
        }
        else {
            $tpl .= "
                window.opener.location.href='$go_to';
            ";
        }

        $tpl .= "
            -->
        </script>
        ";

        echo($tpl);
    }

    /**
     * window opener ajax js
     *
     * @return void
     */
    function _window_opener_ajax(){
        $tpl = "
        <script>
            <!--
                window.opener.auth.afterLogin();
            -->
        </script>
        ";

        echo($tpl);
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
            window.close();
            -->
        </script>
        ";

        exit($tpl);
    }

}

/* End of file facebook.php */
/* Location: ./application/controllers/facebook.php */