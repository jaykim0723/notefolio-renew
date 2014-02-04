<?php
/**
 * Notefolio Activity Management Library
 *
 *
 * @author Yoon, Seongsu(soplel@snooey.net)
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Activity {
    
    function __construct($config=null) {
        $this->ci =& get_instance();
    }
    
    /*
     * post activity for user.
     * 
     * @param array $data
     * 
     * @return noop
     */
    function post($data=array())
    {

        $default_cmd = 'php '.$this->input->server('DOCUMENT_ROOT').'../app-cli/cli.php activity';
        $errmsg = 'eAccelerator: Unable to change cache directory /var/cache/eaccelerator permissions';
        
        $cmd = $default_cmd.' user_list';

    }
    
}