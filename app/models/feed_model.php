<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class feed_model extends CI_Model {


    public function __construct()
    {
        parent::__construct();
        
    }

    function get_list($params=array()){
    	$params = (object)$params;
    	$default_params = (object)array(
    		'page' => 1
    	);
    	foreach($default_params as $key => $value){
    		if(!isset($params->{$key}))
    			$params->{$key} = $value;
    	}
        

    }


    function get_activity_list($params=array()){
    	$params = (object)$params;
    	$default_params = (object)array(
    		'page' => 1
    	);
    	foreach($default_params as $key => $value){
    		if(!isset($params->{$key}))
    			$params->{$key} = $value;
    	}
    }



}

/* End of file work_model.php */
/* Location: ./application/models/work_model.php */