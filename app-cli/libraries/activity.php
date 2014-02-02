<?php
/**
 * Notefolio Activity Management Library
 *
 *
 * @author Yoon, Seongsu(soplel@snooey.net)
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Activity {
    
    var $last_error = '';
    
    function __construct($config=null) {
        $this->ci =& get_instance();
        $this->ci->load->model('user_model');
        $this->ci->load->model('work_model');
        $this->ci->load->model('tank_auth/users');
    }
    
    /**
     * post activity for user.
     * 
     * @param string $area, $string $type, array $data
     * 
     * @return bool
     */
    function post($area='', $type='', $data=array())
    {

        return false;
    }

    /**
     * make activity parameter for user.
     * 
     * @param string $workType
     * @param array $resource
     * 
     * @return array
     */
    private function make_param($workType, $resource=array())
    {
        //-- make work type
        $workType = strtolower($workType);
        $type_array = array('create','read','update','delete',);
        if (array_key_exists($workType, $type_array)) {
            $resource['workType'] = $workType;
            return $this->make_param_{$workType};
        }
        else {
            $this->last_error = @json_encode(array('status'=>'fail', 'message'=>'no_have_work_type'));
            return array();
        }


    }
    
    /**
     * do after post process for user.
     * 
     * @param string $area, string $type, array $resource
     * 
     * @return bool
     */
    private function after_post($area, $type, &$resource=array())
    {
    }
}