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
    //protected $user_data = array();
    
    function __construct($config=null) {
        $this->ci =& get_instance();
        $this->ci->load->model('db/log_db');
        $this->ci->load->model('db/user_db');
        $this->ci->load->model('api/work_model');
        $this->ci->load->model('tank_auth/users');
    }
    
    /*
     * post activity for user.
     * 
     * @param string $area, $string $type, array $data
     * 
     * @return bool
     */
    function post($area='', $type='', $data=array())
    {
        if ($area!=''&&$type!='') { 
            if(isset($data['user_id'])){
                $user_data = $this->ci->users->get_user_by_id($data['user_id'], 1);
                $data['user_id']= $user_data->user_id;
                $data['username']= $user_data->username;
                $data['realname']= $user_data->realname;
                unset($user_data);
            } else {
                $data['user_id']= $this->ci->tank_auth->get_user_id();
                $data['username']= $this->ci->tank_auth->get_username();
                $data['realname']= $this->ci->tank_auth->get_realname();
            }      
            
            if(!in_array($area, array("user","work","forum","webzine"))) {
                $this->last_error = json_encode(array('code'=>'error', 'message'=>"'$area' is not allowed area."));
                return FALSE;
            } else if(!in_array($type, array("new_upload","add_comment","add_note","add_collect","add_coworker","add_follow"))) {
                $this->last_error = json_encode(array('code'=>'error', 'message'=>"'$type' is not allowed type."));
                return FALSE;
            }
            
            $param = $this->make_param($area, $type, $data);
            
            $this->ci->db->trans_start();
            $this->ci->log_db->_insert('activity', $param);
            
            $data['activity_id'] = $this->ci->db->insert_id();
            $this->after_post($area, $type, $data);
            
            $this->ci->db->trans_complete(); 
            
            return $this->ci->db->trans_status();
        }
        
        $this->last_error = json_encode(array('code'=>'error', 'message'=>"'area' and 'type' has no data."));
        return FALSE;
    }
    
    /*
     * make activity parameter for user.
     * 
     * @param string $area, string $type, array $resource
     * 
     * @return bool
     */
    private function make_param($area, $type, &$resource=array())
    {
        $data = array('user_id'=>$resource['user_id'], 'username'=>$resource['username'], 'realname'=>$resource['realname']);
        $param = array('user_id'=>$resource['user_id']);

        switch($area){
            case "user":
                $param['area']='U';
                if($resource['profile_user_id']){
                    $user_data = $this->ci->users->get_user_by_id($resource['profile_user_id'], 1);
                    $data['profile_user_id'] = $user_data->user_id;
                    $data['profile_username'] = $user_data->username;
                    $data['profile_realname'] = $user_data->realname;
                    $resource['profile_user_id'] = $user_data->user_id;
                    unset($user_data);
                }
                break;
            case "work":
                $work_data = $this->ci->work_model->get_work($resource['work_id']);
                $data['work_id'] = $work_data['work_id'];
                $data['work_user_id'] = $work_data['user']['user_id'];
                $data['work_username'] = $work_data['user']['username'];
                $data['work_realname'] = $work_data['user']['realname'];
                $data['title'] = $work_data['title'];
                $param['area']='W';
                $resource['work_user_id'] = $work_data['user']['user_id'];
                break;
            case "forum":
                $data['forum_id'] = $resource['forum_id'];
                $data['title'] = $resource['title'];
                $param['area']='F';
                break;
            case "webzine":
                $param['area']='Z';
                break;
            default:
                $this->last_error = json_encode(array('code'=>'error', 'message'=>"'$area' is not allowed area."));
                return array();
        }
        
        $param['type'] = $type;
        
        switch($type){
            case "new_upload":

                break;
            case "add_comment":
                $user_data = $this->ci->users->get_user_by_id($resource['comment_user_id'], 1);
                $data['comment_user_id'] = $user_data->user_id;
                $data['comment_username'] = $user_data->username;
                $data['comment_realname'] = $user_data->realname;
                $data['comment_comment'] = $resource['comment'];
                if(isset($resource['comment_parent_id'])){
                    $data['comment_parent_id'] = $resource['comment_parent_id'];
                }
                $resource['comment_user_id'] = $user_data->user_id;
                unset($user_data);

                break;
            case "add_note":

                break;
            case "add_collect":
                $user_data = $this->ci->users->get_user_by_id($resource['collect_user_id'], 1);
                $data['collect_user_id'] = $user_data->user_id;
                $data['collect_username'] = $user_data->username;
                $data['collect_realname'] = $user_data->realname;
                $data['collect_comment'] = $resource['comment'];
                $resource['collect_user_id'] = $user_data->user_id;
                unset($user_data);

                break;
            case "add_coworker":
                $user_data = $this->ci->users->get_user_by_id($resource['coworker_user_id'], 1);
                $data['coworker_user_id'] = $user_data->user_id;
                $data['coworker_username'] = $user_data->username;
                $data['coworker_realname'] = $user_data->realname;
                $resource['coworker_user_id'] = $user_data->user_id;
                unset($user_data);
                
                break;
            case "add_follow":
                $user_data = $this->ci->users->get_user_by_id($resource['follow_user_id'], 1);
                $data['follow_user_id'] = $user_data->user_id;
                $data['follow_username'] = $user_data->username;
                $data['follow_realname'] = $user_data->realname;
                $resource['follow_user_id'] = $user_data->user_id;
                unset($user_data);

                break;
            default:
                $this->last_error = json_encode(array('code'=>'error', 'message'=>"'$type' is not allowed type."));
                return array();
        }

        $param['data'] = json_encode($data);
        
        return $param;
    }
    
    /*
     * do after post process for user.
     * 
     * @param string $area, string $type, array $resource
     * 
     * @return bool
     */
    private function after_post($area, $type, &$resource=array())
    {
        if(isset($resource['activity_id'])){
            if($type=="add_comment"&& in_array($area, array("work","forum","webzine"))){
                if(isset($resource['comment_parent_id'])){
                    $this->ci->db->query('
                        INSERT INTO `user_alarms` (`user_id`,`ref_id`,`regdate`)
                        (
                            SELECT `user_id`,"'.$resource['activity_id'].'" as ref_id, CURRENT_TIMESTAMP as regdate
                                FROM `'.$area.'_comments`
                                WHERE `'.$area.'_id`="'.$resource[$area.'_id'].'"
                                    AND `user_id` != '.$resource['user_id'].'
                                    AND `user_id` != '.$resource['user_id'].'
                                    AND `user_id` != '.$resource[$area.'_user_id'].'
                                    AND
                                    (
                                        ( `parent_id`="'.$resource['comment_parent_id'].'")
                                        OR
                                        ( `parent_id`=0
                                        AND `id` = '.$resource['comment_parent_id'].')
                                    )
                                GROUP BY `user_id`
                        )
                        ; 
                    ');
                }
                else {
                    $this->ci->db->query('
                        INSERT INTO `user_alarms` (`user_id`,`ref_id`,`regdate`)
                        (
                            SELECT `user_id`,"'.$resource['activity_id'].'" as ref_id, CURRENT_TIMESTAMP as regdate
                                FROM `'.$area.'_comments`
                                WHERE `'.$area.'_id`="'.$resource[$area.'_id'].'"
                                    AND `parent_id`=0
                                    AND `user_id` != '.$resource['user_id'].'
                                    AND `user_id` != '.$resource[$area.'_user_id'].'
                                GROUP BY `user_id`
                        )
                        ; 
                    ');
                }
                $this->ci->db->query('
                    INSERT INTO `user_alarms` (`user_id`,`ref_id`,`regdate`)
                    (
                        SELECT * 
                        FROM ( SELECT "'.$resource[$area.'_user_id'].'" as user_id,"'.$resource['activity_id'].'" as ref_id, CURRENT_TIMESTAMP as regdate ) a
                            WHERE a.user_id != '.$resource['user_id'].'
                    )
                    ; 
                ');
            } else if ($type=="add_comment"&&$area=="user"){
                if(isset($resource['comment_parent_id'])){
                    $this->ci->db->query('
                        INSERT INTO `user_alarms` (`user_id`,`ref_id`,`regdate`)
                        (
                            SELECT `user_id`,"'.$resource['activity_id'].'" as ref_id, CURRENT_TIMESTAMP as regdate
                                FROM `user_profile_comments`
                                WHERE `user_profile_id`="'.$resource['profile_user_id'].'"
                                    AND `user_id` != '.$resource['user_id'].'
                                    AND `user_id` != '.$resource['user_id'].'
                                    AND `user_id` != '.$resource['profile_user_id'].'
                                    AND
                                    (
                                        ( `parent_id`="'.$resource['comment_parent_id'].'")
                                        OR
                                        ( `parent_id`=0
                                        AND `id` = '.$resource['comment_parent_id'].')
                                    )
                                GROUP BY `user_id`
                        )
                        ; 
                    ');
                }
                else {
                    
                }
                $this->ci->db->query('
                    INSERT INTO `user_alarms` (`user_id`,`ref_id`,`regdate`)
                    (
                        SELECT * 
                        FROM ( SELECT "'.$resource['profile_user_id'].'" as user_id,"'.$resource['activity_id'].'" as ref_id, CURRENT_TIMESTAMP as regdate ) a
                            WHERE a.user_id != '.$resource['user_id'].'
                    )
                    ; 
                ');
                
            } else if(($type=="add_note"&&$area=="work")||
                      ($type=="add_collect"&&$area=="work")){
                /*
                 * 
                 $this->ci->db->query('
                    INSERT INTO `user_alarms` (`user_id`,`ref_id`,`regdate`)
                    (
                        SELECT `user_id`,"'.$resource['activity_id'].'" as ref_id, CURRENT_TIMESTAMP as regdate
                            FROM `works`
                            WHERE `id`="'.$resource['work_id'].'"
                                AND `user_id` != '.$resource['user_id'].'
                            GROUP BY `user_id`
                    )
                    ; 
                ');
                 * 
                 */
                $this->ci->db->query('
                    INSERT INTO `user_alarms` (`user_id`,`ref_id`,`regdate`)
                    (
                        SELECT * 
                        FROM ( SELECT "'.$resource['work_user_id'].'" as user_id,"'.$resource['activity_id'].'" as ref_id, CURRENT_TIMESTAMP as regdate ) a
                            WHERE a.user_id != '.$resource['user_id'].'
                    )
                    ; 
                ');    
            } else if ($type=="add_coworker"&&$area=="work"){
                $this->ci->db->query('
                    INSERT INTO `user_alarms` (`user_id`,`ref_id`,`regdate`)
                    (
                        SELECT * 
                        FROM ( SELECT "'.$resource['coworker_user_id'].'" as user_id,"'.$resource['activity_id'].'" as ref_id, CURRENT_TIMESTAMP as regdate ) a
                            WHERE a.user_id != '.$resource['user_id'].'
                    )
                    ; 
                ');
            } else if ($type=="add_follow"&&$area=="user"){
                $this->ci->db->query('
                    INSERT INTO `user_alarms` (`user_id`,`ref_id`,`regdate`)
                    (
                        SELECT * 
                        FROM ( SELECT "'.$resource['follow_user_id'].'" as user_id,"'.$resource['activity_id'].'" as ref_id, CURRENT_TIMESTAMP as regdate ) a
                            WHERE a.user_id != '.$resource['user_id'].'
                    )
                    ; 
                ');
            }
            
            return TRUE;
        }
        
        $this->last_error = json_encode(array('code'=>'error', 'message'=>"where is 'activity id'?"));
        return FALSE;
    }
    
    /*
     * return recent activity for user.
     * 
     * @param int $user_id, array $opt
     * 
     * @return array
     */
    function recent_activity_list($user_id=0, $opt=array())
    {
        // get list
        $list = $this->ci->log_db->_get_list('activity', array('user_id'=>$user_id,
                                                           'where_query'=>"((`area`='W' and `type` in ('new_upload', 'add_note', 'add_comment', 'add_collect')) or (`area`='U' and `type`='add_follow'))" ),
                                                                    array(), array(1,5));
        for($i=0;$i<count($list);$i++){
            $list[$i] = $this->make_msg('recent_activity', $list[$i]);
        }
        
        return $list;
    }
    
    /*
     * return feed activity for user.
     * 
     * @param int $user_id, array $opt
     * 
     * @return array
     */
    function feed_list($user_id=0, $opt=array())
    {
        $query = array('follower_id'=>$user_id,
                       'follow_join'=>
                            array('table'=>'(select follow_id, follower_id from user_follow) uf', 'on'=>'log_activity.user_id = uf.follow_id', 'type'=>'left' ),
                       'where_query'=>"((`area`='W' and `type` in ('new_upload', 'add_note', 'add_comment', 'add_collect')))" );
        if(!empty($opt['last_no'])){
            $query['id <'] = $opt['last_no'];
        }
        if(empty($opt['page'])) $opt['page'] = 1;
        if(empty($opt['delimiter'])) $opt['delimiter'] = 16;
        
        
        // get list
        $list = $this->ci->log_db->_get_list('activity', $query, array(), array($opt['page'],$opt['delimiter']));
        $work_list = array();
        $work = array();
        
        for($i=0;$i<count($list);$i++){
            $list[$i] = $this->make_msg('feed', $list[$i]);
            
            $work_list[$i] = $list[$i]['work_id'];
        }
        
        if(count($work_list)>0){
            $work_list = $this->ci->work_model->get_work_list('', '', '', 1, 16, 'newest', '', 0, array('id_in'=>$work_list));
            
            for($j=0;$j<count($work_list);$j++){
                if(!empty($work_list[$j])){
                    $work[$work_list[$j]['work_id']] = array(
                                                        'work_id'=>$work_list[$j]['work_id'],
                                                        'realname'=>$work_list[$j]['user']['realname'],
                                                        'title'=>$work_list[$j]['title'],
                                                        'keyword'=>$work_list[$j]['categories']
                                                        );
                }
            }
        }
        
        return array('feed_list'=>$list, 'work'=>$work, 'last_no'=>$list[$i-1]['id']);
    }

	function feed_new_list_upload($user_id=0, $opt=array())
    {
        $query = array('follower_id'=>$user_id,
                       'follow_join'=>
                            array('table'=>'(select follow_id, follower_id from user_follow) uf', 'on'=>'log_activity.user_id = uf.follow_id', 'type'=>'left' ),
                       'where_query'=>"((`area`='W' and `type` in ('new_upload')))" );
        if(!empty($opt['last_no'])){
            $query['id <'] = $opt['last_no'];
        }
        if(empty($opt['page'])) $opt['page'] = 1;
        if(empty($opt['delimiter'])) $opt['delimiter'] = 12;
        
        
        // get list
        $list = $this->ci->log_db->_get_list('activity', $query, array(), array($opt['page'],$opt['delimiter']));
        $work_list = array();
        $work = array();
        
        for($i=0;$i<count($list);$i++){
            $list[$i] = $this->make_msg('feed', $list[$i]);
            
            $work_list[$i] = $list[$i]['work_id'];
        }
        
        if(count($work_list)>0){
            $work_list = $this->ci->work_model->get_work_list('', '', '', 1, 12, 'newest', '', 0, array('id_in'=>$work_list));
            
            for($j=0;$j<count($work_list);$j++){
                if(!empty($work_list[$j])){
                    $work[$work_list[$j]['work_id']] = array(
                                                        'work_id'=>$work_list[$j]['work_id'],
                                                        'realname'=>$work_list[$j]['user']['realname'],
                                                        'title'=>$work_list[$j]['title'],
                                                        'keyword'=>$work_list[$j]['categories']
                                                        );
                }
            }
        }
        
        return array('feed_list'=>$list, 'work'=>$work, 'last_no'=>$list[$i-1]['id']);
    }

	function feed_new_list_other($user_id=0, $opt=array())
    {
        $query = array('follower_id'=>$user_id,
                       'follow_join'=>
                            array('table'=>'(select follow_id, follower_id from user_follow) uf', 'on'=>'log_activity.user_id = uf.follow_id', 'type'=>'left' ),
                       'where_query'=>"((`area`='W' and `type` in ('add_note', 'add_comment', 'add_collect')))" );
        if(!empty($opt['last_no'])){
            $query['id <'] = $opt['last_no'];
        }
        if(empty($opt['page'])) $opt['page'] = 1;
        if(empty($opt['delimiter'])) $opt['delimiter'] = 14;
        
        
        // get list
        $list = $this->ci->log_db->_get_list('activity', $query, array(), array($opt['page'],$opt['delimiter']));
        $work_list = array();
        $work = array();
        
        for($i=0;$i<count($list);$i++){
            $list[$i] = $this->make_msg('feed', $list[$i]);
            
            $work_list[$i] = $list[$i]['work_id'];
        }
        
        if(count($work_list)>0){
            $work_list = $this->ci->work_model->get_work_list('', '', '', 1, 14, 'newest', '', 0, array('id_in'=>$work_list));
            
            for($j=0;$j<count($work_list);$j++){
                if(!empty($work_list[$j])){
                    $work[$work_list[$j]['work_id']] = array(
                                                        'work_id'=>$work_list[$j]['work_id'],
                                                        'realname'=>$work_list[$j]['user']['realname'],
                                                        'title'=>$work_list[$j]['title'],
                                                        'keyword'=>$work_list[$j]['categories']
                                                        );
                }
            }
        }
        
        return array('feed_list'=>$list, 'work'=>$work, 'last_no'=>$list[$i-1]['id']);
    }

    /*
     * return alarm activity count for user.
     * 
     * @param int $user_id, array $opt
     * 
     * @return array
     */
    function alarm_count($user_id=0, $opt=array())
    {
        $query = array('user_id'=>$user_id,
                       'log_join'=>
                            array('table'=>'(select id as log_id, area, type, data from log_activity) log', 'on'=>'user_alarms.ref_id = log.log_id', 'type'=>'left'),
                       'where_query'=>"((`area`='W' and `type` in ('add_note', 'add_comment', 'add_collect', 'add_coworker')) or (`area`='U' and `type` in ('add_follow', 'add_comment')))" );
        
        $count = $this->ci->user_db->_get_user_alarm_list($query, array('count(*) as count'), array());
        
        $query['readdate'] = null;
        $unread_count = $this->ci->user_db->_get_user_alarm_list($query, array('count(*) as count'), array());
        
        return array('all'=>$count[0]['count'], 'unread'=>$unread_count[0]['count']);
    }
    
    /*
     * return alarm activity for user.
     * 
     * @param int $user_id, array $opt
     * 
     * @return array
     */
    function alarm_list($user_id=0, $opt=array())
    {
        $query = array('user_id'=>$user_id,
                       'log_join'=>
                            array('table'=>'(select id as log_id, area, type, data from log_activity) log', 'on'=>'user_alarms.ref_id = log.log_id', 'type'=>'left'),
                       'where_query'=>"((`area`='W' and `type` in ('add_note', 'add_comment', 'add_collect', 'add_coworker')) or (`area`='U' and `type` in ('add_follow', 'add_comment')))" );
        
        $count = $this->alarm_count($user_id);
        
        if(!empty($opt['first_alarm'])){
            $query['id >'] = $opt['first_alarm'];
        }
        if(!empty($opt['last_alarm'])){
            $query['id <'] = $opt['last_alarm'];
        }
        if(empty($opt['page'])) $opt['page'] = 1;
        if(empty($opt['delimiter'])) $opt['delimiter'] = 20;
        
        
        // get list
        //$list = $this->ci->log_db->_get_list('activity', $query, array(), array($opt['page'],$opt['delimiter']));
        
        $list = $this->ci->user_db->_get_user_alarm_list($query, array(), array($opt['page'], $opt['delimiter']));
        
        $read_list = array();
                                                            
        for($i=0;$i<count($list);$i++){
            $list[$i] = $this->make_msg('alarm', $list[$i]);
            if($list[$i]['readdate']==null)
                $read_list[$i] = $list[$i]['id'];
        }
        
        if(count($read_list)>0){
            $query = array('set_readdate'=>date("Y-m-d H:i:s"), 'id <='=>$list[0]['id']);
            
            $this->ci->db->trans_start();
            $this->ci->user_db->_update_user_alarm($query);
            $this->ci->db->trans_complete();  
        }
        
        return array('count'=>$count['all'], 'unread_count'=>$count['unread'], 'alarm_list' => $list, 'first_alarm'=>$list[0]['id'], 'last_alarm'=>$list[$i-1]['id']);
    }
    
    /*
     * make activity message for user.
     * 
     * @param string $area, string $type, array $resource
     * 
     * @return bool
     */
    private function make_msg($type, $resource)
    {
        $data = json_decode($resource['data']);
        $output=array('id'=>$resource['id']);
        
        if (isset($data->user_id) && $data->user_id == $this->ci->tank_auth->get_user_id()){
            $data->realname = "회원";
        } else $data->realname .= ' ';
        if (isset($data->profile_user_id) && $data->profile_user_id == $this->ci->tank_auth->get_user_id()){
            $data->profile_realname = "회원";
        } else $data->work_realname .= ' ';
        if (isset($data->work_user_id) && $data->work_user_id == $this->ci->tank_auth->get_user_id()){
            $data->work_realname = "회원";
        } else $data->work_realname .= ' ';
        if (isset($data->follow_user_id) && $data->follow_user_id == $this->ci->tank_auth->get_user_id()){
            $data->follow_realname = "회원";
        } else $data->follow_realname .= ' ';
        if (isset($data->collect_user_id) && $data->collect_user_id == $this->ci->tank_auth->get_user_id()){
            $data->collect_realname = "회원";
        } else $data->collect_realname .= ' ';
        if (isset($data->coworker_user_id) && $data->coworker_user_id == $this->ci->tank_auth->get_user_id()){
            $data->coworker_realname = "회원";
        } else $data->coworker_realname .= ' ';

        switch($type){
            case "alarm":
                $output['area']=$resource['area'];
                $output['type']=$resource['type'];
                $output['user_id']=$data->user_id;
                
                if($resource['area']=='U') { //area:user
                    
                    if($resource['type']=='add_follow'){
                        $output['username_A']=$data->username;
                        $output['realname_A']=$data->realname;
                        $output['username_B']=$data->follow_username;
                        $output['realname_B']=$data->follow_realname;
                    } else if($resource['type']=='add_comment'){
                        $output['username_A']=$data->username;
                        $output['realname_A']=$data->realname;
                        $output['username_B']=$data->profile_username;
                        $output['realname_B']=$data->profile_realname;
                        $output['comment']=$data->comment_comment;
                        if(isset($data->comment_parent_id))
                            $output['comment_parent_id']=$data->comment_parent_id;
                    }
                } else if($resource['area']=='W') { //area:work
                    $output['user_id']=$data->user_id;
                    $output['work_id']=$data->work_id;
                    $output['work_title']=$data->title;
                    $output['work_user_id']=$data->work_user_id;
                    $output['area']=$resource['area'];
                    $output['type']=$resource['type'];
                    
                    if($resource['type']=='add_note'){
                        $output['username_A']=$data->username;
                        $output['realname_A']=$data->realname;
                        $output['username_B']=$data->work_username;
                        $output['realname_B']=$data->work_realname;
                    } else if($resource['type']=='add_comment'){
                        $output['username_A']=$data->username;
                        $output['realname_A']=$data->realname;
                        $output['username_B']=$data->work_username;
                        $output['realname_B']=$data->work_realname;
                        $output['comment']=$data->comment_comment;
                    } else if($resource['type']=='add_collect'){
                        $output['username_A']=$data->username;
                        $output['realname_A']=$data->realname;
                        $output['username_B']=$data->work_username;
                        $output['realname_B']=$data->work_realname;
                        $output['comment']=$data->collect_comment;
                    } else if($resource['type']=='add_coworker'){
                        $output['username_A']=$data->username;
                        $output['realname_A']=$data->realname;
                        $output['username_B']=$data->coworker_username;
                        $output['realname_B']=$data->coworker_realname;
                    }
                }

                $output['readdate'] = $resource['readdate'];
                break;
            case "feed":
                $output['area']=$resource['area'];
                $output['type']=$resource['type'];
                if($resource['area']=='W') { //area:work
                    $output['user_id']=$data->user_id;
                    $output['work_id']=$data->work_id;
                    
                    if($resource['type']=='new_upload'){
                        $output['username']=$data->username;
                        $output['realname']=$data->realname;
                    } else if($resource['type']=='add_note'){
                        $output['username_A']=$data->username;
                        $output['realname_A']=$data->realname;
                        $output['username_B']=$data->work_username;
                        $output['realname_B']=$data->work_realname;
                    } else if($resource['type']=='add_comment'){
                        $output['username_A']=$data->username;
                        $output['realname_A']=$data->realname;
                        $output['username_B']=$data->work_username;
                        $output['realname_B']=$data->work_realname;
                        $output['comment']=$data->comment_comment;
                    } else if($resource['type']=='add_collect'){
                        $output['username_A']=$data->username;
                        $output['realname_A']=$data->realname;
                        $output['username_B']=$data->work_username;
                        $output['realname_B']=$data->work_realname;
                        $output['comment']=$data->collect_comment;
                    }
                }
                break;
            case "recent_activity":
                if($resource['area']=='U') { //area:user
                    if($resource['type']=='add_follow'){
                        $output['link']=$this->ci->config->item('base_url').
                                        $data->follow_username;
                        $output['text']="{$data->follow_realname}님을 팔로우합니다.";
                    }
                } else if ($resource['area']=='W') { //area:work
                    if($resource['type']=='new_upload'){
                        $output['link']=$this->ci->config->item('base_url').
                                        'gallery/'.$data->work_id;
                        $output['text']="새로운 작품을 공개하였습니다.";
                    } else if($resource['type']=='add_note'){
                        $output['link']=$this->ci->config->item('base_url').
                                        'gallery/'.$data->work_id;
                        $output['text']="{$data->work_realname}님의 작품을 NOTE IT 하였습니다.";
                    } else if($resource['type']=='add_comment'){
                        $output['link']=$this->ci->config->item('base_url').
                                        'gallery/'.$data->work_id;
                        $output['text']="{$data->work_realname}님의 작품에 댓글을 남겼습니다.";
                    } else if($resource['type']=='add_collect'){
                        $output['link']=$this->ci->config->item('base_url').
                                        $data->username.'/collection';
                        $output['text']="{$data->work_realname}님의 작품을 콜렉션에 담았습니다.";
                    }
                    
                }
                break;
            default:
                $this->last_error = json_encode(array('code'=>'error', 'message'=>"'type' is not allowed area."));
                return array();
        }
        $output['regdate'] = $this->ci->notefolio->current_time(strtotime($resource['regdate'])+$this->ci->config->item('timezone_calc'));
        
        return $output;
    }
    
}