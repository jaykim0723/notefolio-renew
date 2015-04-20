<?php
/**
 * Notefolio Hook for Access Log
 *
 *
 * @author Yoon, Seongsu(soplel@snooey.net)
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class AccessLogHook {
    
    var $last_error = '';
    //protected $user_data = array();
    
    function __construct($config=null) {
        $this->ci =& get_instance();
        $this->ci->load->library('accesslog');
    }
    
    /*
     * do process
     * 
     * @param string $data
     * 
     * @return bool
     */
    function post($data=null)
    {
        $this->ci =& get_instance();
        $this->ci->load->library('accesslog');
        return $this->ci->accesslog->post($data);
    }
    
}