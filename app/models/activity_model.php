<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class activity_model extends CI_Model {


    public function __construct()
    {
        parent::__construct();
        $this->load->model('api/work_model');
        $this->load->model('tank_auth/users');
        
    }

    function post($params=array()){
    	$params = (object)$params;
    	$default_params = (object)array(

    	);
    	foreach($default_params as $key => $value){
    		if(!isset($params->{$key}))
    			$params->{$key} = $value;
    	}
        

    }
    
    /**
     * make activity parameter for user.
     * 
     * @param string $area
     * @param string $type
     * @param array $resource
     * 
     * @return bool
     */

    private function _build_param($area, $type, &$resource=array()){
        $data = array(
            'user_id'=>$resource['user_id'],
            'username'=>$resource['username'],
            'realname'=>$resource['realname']
            );

        $param = array(
            'user_id'=>$resource['user_id']
            );

        if(in_array($area, array('user', 'work',/* 'forum', 'webzine'*/)))
            $param = $this->_build_param_{$area}($param, $resource);
        else
            return json_encode(array('code'=>'error', 'message'=>"'$area' is not allowed area."));
        

        if(in_array($type, $param['type_allowed']))
            $param = $this->_build_param_{$type}($param, $resource);
        else
            return json_encode(array('code'=>'error', 'message'=>"'$type' is not allowed type."));

        unset($param['type_allowed'])

        return $param;

    }

    //-- area

    private function _build_param_user($param=array(), &$resource=array()) {
        $param['area']='U';
        
        if($resource['profile_user_id']){
            $user_data = $this->ci->users->get_user_by_id($resource['profile_user_id'], 1);
            $data['profile_user_id'] = $user_data->user_id;
            $data['profile_username'] = $user_data->username;
            $data['profile_realname'] = $user_data->realname;
            $resource['profile_user_id'] = $user_data->user_id;
            unset($user_data);
        }

        $param['type_allowed'] = array('view', 'follow', 'unfollow');

        return $param;
    }
                break;

    private function _build_param_work($param=array(), &$resource=array()) {
        $param['area']='W';

        $work_data = $this->ci->work_model->get_work($resource['work_id']);
        $data['work_id'] = $work_data['work_id'];
        $data['work_user_id'] = $work_data['user']['user_id'];
        $data['work_username'] = $work_data['user']['username'];
        $data['work_realname'] = $work_data['user']['realname'];
        $data['title'] = $work_data['title'];
        $resource['work_user_id'] = $work_data['user']['user_id'];

        $param['type_allowed'] = array('add_comment', 'del_comment',
                                       'add_collect', 'del_collect',
                                       'add_note', 'del_note',
                                        'follow');

        return $param;
    }
/*
    private function _build_param_forum($param=array(), &$resource=array()) {
        $param['area']='F';

        $data['forum_id'] = $resource['forum_id'];
        $data['title'] = $resource['title'];

        break;
    
        return $param;
    }

    private function _build_param_webzine($param=array(), &$resource=array()) {
        $param['area']='Z';

        break;
    
        return $param;
    }
*/
    /**
     * make activity parameter for user.
     * 
     * @param string $area
     * @param string $type
     * @param array $resource
     * 
     * @return bool
     */

    private function _build_type_param($area, $type, &$resource=array()){
        $data = array('user_id'=>$resource['user_id'], 'username'=>$resource['username'], 'realname'=>$resource['realname']);
        $param = array('user_id'=>$resource['user_id']);
        
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
                return array("error" => json_encode(array('code'=>'error', 'message'=>"'$type' is not allowed type.")));
        }

        $param['data'] = json_encode($data);
        
        return $param;
    }


}

/* End of file work_model.php */
/* Location: ./application/models/work_model.php */