<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class activity_model extends CI_Model {


    public function __construct()
    {
        parent::__construct();
        
    }

    function post($params=array()){
        $params = (object)$params;
        $default_params = (object)array(
            'regdate' => date('Y-m-d H:i:s'),
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
            $this->db->insert('log_activity', $params);
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

    function post_alarm($params=array()){
        $params = (object)$params;
        $default_params = (object)array(
            'regdate' => date('Y-m-d H:i:s'),
            'crud' => '',
            'area' => '',
            'type' => '',
            'ref_id' => 0,
            'user_A_id' => 0,
            'user_B_id' => 0,
            'parent_id' => 0,
            'activity_id' => 0,
        );
        foreach($default_params as $key => $value){
            if(!isset($params->{$key}))
                $params->{$key} = $value;
        }

        try{
            if(   ($params->crud=="create")
                &&($params->area=="work")
                &&($params->type=="comment")   ){

                if($params->parent_id>0){
                    $sql = "INSERT INTO `user_alarms` 
                        (`user_id`,`ref_id`,`regdate`)
                        (
                            SELECT `user_id`, ".$this->db->escape($params->activity_id)." as ref_id, CURRENT_TIMESTAMP as regdate
                                FROM `work_comments`
                                WHERE `work_id`=".$this->db->escape($params->ref_id)."
                                    AND `user_id` != ".$this->db->escape($params->user_A_id)."
                                    AND `user_id` != ".$this->db->escape($params->user_B_id)."
                                    AND
                                    (
                                        ( `parent_id`=".$this->db->escape($params->parent_id).")
                                        OR
                                        ( `parent_id`=0
                                        AND `id` = ".$this->db->escape($params->parent_id).")
                                    )
                                GROUP BY `user_id`
                        );
                    ";
                    $this->db->query($sql);
                }
                else {
                    $sql = "INSERT INTO `user_alarms` (`user_id`,`ref_id`,`regdate`)
                        (
                            SELECT `user_id`, ".$this->db->escape($params->activity_id)." as ref_id, CURRENT_TIMESTAMP as regdate
                                FROM `work_comments`
                                WHERE `work_id`=".$this->db->escape($params->ref_id)."
                                    AND `parent_id`=0
                                    AND `user_id` != ".$this->db->escape($params->user_A_id)."
                                    AND `user_id` != ".$this->db->escape($params->user_B_id)."
                                GROUP BY `user_id`
                        );
                    ";
                    $this->db->query($sql);
                }

                $sql = "INSERT INTO `user_alarms` (`user_id`,`ref_id`,`regdate`)
                    (
                        SELECT * 
                        FROM ( 
                            SELECT ".$this->db->escape($params->user_B_id)." as user_id, ".$this->db->escape($params->activity_id)." as ref_id, CURRENT_TIMESTAMP as regdate 
                            ) a
                            WHERE a.user_id != ".$this->db->escape($params->user_A_id)."
                    );
                ";
                $this->db->query($sql);
            }
            else if(   ($params->crud=="create")
                &&($params->area=="work")
                &&(($params->type=="note")||($params->type=="collect"))   ){
                /*
                 * 
                $sql = "INSERT INTO `user_alarms` (`user_id`,`ref_id`,`regdate`)
                    (
                        SELECT `user_id`,".$this->db->escape($params->activity_id)." as ref_id, CURRENT_TIMESTAMP as regdate
                            FROM `works`
                            WHERE `id`= ".$this->db->escape($params->ref_id)." 
                                AND `user_id` != ".$this->db->escape($params->user_A_id)." 
                            GROUP BY `user_id`
                    )
                    ; 
                ";
                $this->db->query($sql);
                 * 
                 */
                $sql = "INSERT INTO `user_alarms` (`user_id`,`ref_id`,`regdate`)
                    (
                        SELECT * 
                        FROM ( SELECT ".$this->db->escape($params->user_B_id)." as user_id,".$this->db->escape($params->activity_id)." as ref_id, CURRENT_TIMESTAMP as regdate ) a
                            WHERE a.user_id != ".$this->db->escape($params->user_A_id)."
                    )
                    ; 
                ";
                $this->db->query($sql);
            }
            else if(   ($params->crud=="create")
                &&($params->area=="user")
                &&($params->type=="follow")   ){
                $sql = "INSERT INTO `user_alarms` (`user_id`,`ref_id`,`regdate`)
                    (
                        SELECT * 
                        FROM ( SELECT ".$this->db->escape($params->user_B_id)." as user_id,".$this->db->escape($params->activity_id)." as ref_id, CURRENT_TIMESTAMP as regdate ) a
                            WHERE a.user_id != ".$this->db->escape($params->user_A_id)."
                    )
                    ; 
                ";
                $this->db->query($sql);
            }
        }
        catch(Exception $e){
            return (object)array(
                'status' => 'fail',
                'message' => 'db_error'
            );
        }
        
        return (object)array(
            'status' => 'done',
        );
        
    }

    function post_feed($params=array()){
    	$params = (object)$params;
    	$default_params = (object)array(
            'regdate' => date('Y-m-d H:i:s'),
            'crud' => '',
            'area' => '',
            'type' => '',
            'ref_id' => 0,
            'user_A_id' => 0,
            'user_B_id' => 0,
            'parent_id' => 0,
            'activity_id' => 0,
    	);
    	foreach($default_params as $key => $value){
    		if(!isset($params->{$key}))
    			$params->{$key} = $value;
    	}

        try{
            if(   ($params->crud=="update")
                &&($params->area=="work")
                &&($params->type=="enable")   ){
                $sql = "INSERT INTO `user_feeds` (`user_id`,`ref_id`,`regdate`)
                    (
                        SELECT 
                            a.user_id,        
                            ".$this->db->escape($params->activity_id)." as ref_id,
                            CURRENT_TIMESTAMP as regdate
                        FROM ( 
                            SELECT follow_id as user_id
                                from user_follows
                                where follower_id = ".$this->db->escape($params->user_B_id)."
                                order by user_id asc
                            ) a
                    )
                    ; 
                ";
                $this->db->query($sql);
            }
            else if(   ($params->crud=="create")
                &&($params->area=="work")
                &&(($params->type=="note")||($params->type=="comment")||($params->type=="collect"))   ){
                $sql = "INSERT INTO `user_feeds` (`user_id`,`ref_id`,`regdate`)
                    (
                        SELECT 
                            a.user_id,        
                            ".$this->db->escape($params->activity_id)." as ref_id,
                            CURRENT_TIMESTAMP as regdate
                        FROM ( 
                            SELECT follow_id as user_id
                                from user_follows
                                where follower_id = ".$this->db->escape($params->user_B_id)."
                                order by user_id asc
                            ) a
                    )
                    ; 
                ";
                $this->db->query($sql);
            }
        }
        catch(Exception $e){
            return (object)array(
                'status' => 'fail',
                'message' => 'db_error'
            );
        }
        
        return (object)array(
            'status' => 'done',
        );
        
    }

}

/* End of file work_model.php */
/* Location: ./application/models/work_model.php */