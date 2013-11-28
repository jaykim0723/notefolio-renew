<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class alarm_model extends CI_Model {


    public function __construct()
    {
        parent::__construct();
        
    }

    function get_list($params=array()){
    	$params = (object)$params;
    	$default_params = (object)array(
    		'page' => 1,
            'user_id' => ''
    	);
    	foreach($default_params as $key => $value){
    		if(!isset($params->{$key}))
    			$params->{$key} = $value;
    	}

        // DB 호출하
        // 출력값 조정하고
        // do stuff by 성수씨
        
        $data = (object)array(
            'status' => 'done',
            'page'   => $params->page,
            'rows'   => $rows
        );
        if(sizeof($rows)==0){
            $data->status = 'fail';
            return $data;
        }
        return $data;


    }


}

/* End of file work_model.php */
/* Location: ./application/models/work_model.php */