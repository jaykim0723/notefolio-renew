<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class activity_model extends CI_Model {


    public function __construct()
    {
        parent::__construct();
        
    }

    function post($params=array()){
    	$params = (object)$params;
    	$default_params = (object)array(
            'regdate' => date('Y-m-d'),
            'ref_id' => 0,
            'user_id' => 0,
            'area' => 'work',
            'act' => 'create',
            'type' => '',
            'point_get' => 0,
            'point_status' => 0,
            'data' => '',
            'remote_addr' => 'console'
    	);
    	foreach($default_params as $key => $value){
    		if(!isset($params->{$key}))
    			$params->{$key} = $value;
    	}
/*
INSERT INTO `notefolio-renew`.`log_activity`
(`id`,
`regdate`,
`ref_id`,
`user_id`,
`area`,
`act`,
`type`,
`point_get`,
`point_status`,
`data`,
`remote_addr`)
VALUES
(<{id: }>,
<{regdate: CURRENT_TIMESTAMP}>,
<{ref_id: 0}>,
<{user_id: 0}>,
<{area: W}>,
<{act: C}>,
<{type: }>,
<{point_get: 0}>,
<{point_status: 0}>,
<{data: }>,
<{remote_addr: console}>);
*/

        try{
            $this->db->insert('uploads', $params);
            $activity_id = $this->db->insert_id();
        }
        catch(Exception $e){
            return (object)array(
                'status' => 'fail',
                'message' => 'db_error'
            );
        }
        
        return (object)array(
            'status' => 'done',
            'activity_id' => $activity_id
        );
        
    }

}

/* End of file work_model.php */
/* Location: ./application/models/work_model.php */