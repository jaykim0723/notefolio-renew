<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Nf
{
	protected	$ci;
	public function __construct()
	{
        $this->ci =& get_instance();

        if ($this->ci->tank_auth->is_logged_in()) {
            if(!defined('USER_ID'))
                define('USER_ID', $this->ci->tank_auth->get_user_id());
        } else {
            if(!defined('USER_ID'))
                define('USER_ID', 0);
        }
	}


    function _member_check($member_only=array(), $go_to='')
    {
        $this->ci =& get_instance();
        if (!$this->ci->tank_auth->is_logged_in()) {
            if(in_array($this->ci->router->fetch_method(), $member_only)) {
            	if($this->ci->input->is_ajax_request()){
            		exit('login');
            	}else{
	                redirect('/auth/login?go_to='.($go_to=='' ? $this->ci->uri->uri_string() : $go_to));
	            }
            }
        }
    }

    function print_time($ymdhis){
        $gap = time() - strtotime($ymdhis);
        $msg = '';
        if($gap < 60) // 1분 이내
            $msg = '방금 전';
        elseif($gap < 3600){ // 1시간 이내
            $gap = floor($gap / 60);
            $msg = $gap.'분 전';
        }elseif($gap < 86400){ // 1일 이내
            $gap = floor($gap / 3600);
            $msg = $gap.'시간 전';
        }elseif($gap < 604800){ // 1주일 이내
            $gap = floor($gap / 86400);
            $msg = $gap.'일 전';
        }elseif($gap < 2678400){ // 4주(1달) 이내
            $gap = floor($gap / 604800);
            $msg = $gap.'주 전';
        }elseif($gap < 31536000){ // 1년 이내
            $gap = floor($gap / 2678400);
            $msg = $gap.'개월 전';
        }else{ // 1년 이후
            $gap = floor($gap / 31536000);
            $msg = $gap.'년 전';
        }
        return $msg;
    }

    //-- admin


    /**
     * for admin
     *
     * @return  bool
     */

    function admin_check()
    {
        if(!$this->admin_is_elevated()){
            redirect('/auth/elevate?go_to='.($go_to=='' ? '/'.$this->ci->uri->uri_string() : $go_to));
        }
    }

    /**
     * elevate user to administrator level [require: tank-auth]
     *
     * @return  bool
     */
    function admin_elevate()
    {
        if (USER_ID > 0) {

            $this->ci->load->config('tank_auth', TRUE);
            $this->ci->load->model('tank_auth/users');

            $user = $this->ci->users->get_user_by_id(USER_ID, true);

            if ($user->level > 6) { // 7,8,9 = 관리자 레벨
                $this->ci->session->set_userdata(array(
                        'admin_user_id'   => $user->id,
                        'admin_user_level'  => $user->level,
                ));
                return TRUE;
            } else {                // fail - level is low
                $this->error = array('login' => 'level_is_low');
            }
        }
        return FALSE;
    }

    /**
     * unelevate administrator level
     *
     * @return  void
     */
    function admin_unelevate()
    {
        $this->ci->session->unset_userdata(array('admin_user_id' => '', 'admin_user_level' => ''));
    }

    /**
     * if user can be admin?
     *
     * @param int $user_id
     * @return  int
     */
    function admin_check_can_elevate($user_id=null)
    {
        if (empty($user_id))
            $user_id = @USER_ID;
        if ($user_id > 0) {

            $this->ci->load->config('tank_auth', TRUE);
            $this->ci->load->model('tank_auth/users');

            $user = $this->ci->users->get_user_by_id($user_id, true);

            if ($user->level > 6) { // 7,8,9 = 관리자 레벨
                return TRUE;
            }
            
        }
        return FALSE;
    }

    /**
     * Check if user is now elevated.
     * 
     * @return  bool
     */
    function admin_is_elevated()
    {
        if(USER_ID != $this->ci->session->userdata('admin_user_id')) {
            $this->admin_unelevate();
            return false;
        }
        
        if($this->ci->session->userdata('admin_user_level')){
            return true;
        }

        return false;
    }


	

}
