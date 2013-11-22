<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Nf
{
	protected	$ci;
	public function __construct()
	{
        $this->ci =& get_instance();
	}


    function _member_check($member_only=array(), $go_to='')
    {
        $this->ci =& get_instance();
        if ($this->ci->tank_auth->is_logged_in()) {
        	if(!defined('USER_ID'))
	            define('USER_ID', $this->ci->tank_auth->get_user_id());
        } else {
        	if(!defined('USER_ID'))
	            define('USER_ID', 0);
            if(in_array($this->ci->router->fetch_method(), $member_only)) {
            	if($this->ci->input->is_ajax_request()){
            		exit('login');
            	}else{
	                redirect('/auth/login?go_to='.($go_to=='' ? $this->ci->uri->uri_string() : $go_to));
	            }
            }
        }
    }


	

}
