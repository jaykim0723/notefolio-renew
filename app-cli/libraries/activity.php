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
        $this->ci->load->database();
        $this->ci->load->model('user_model');
        $this->ci->load->model('work_model');
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
    
    function make_param($workType, $resource=array())
    {   
        //-- go by work type
        $workType = strtolower($workType);
        try{
            $resource['workType'] = $workType;
            $output =  $this->{'make_param_'.$workType}($resource);
        }
        catch(Exception $e){
            $this->last_error = @json_encode(array('status'=>'fail', 'message'=>'no_have_work_type'));
            return array();
        }

        return $output;
    }

    /**
     * make activity parameter for user. (create)
     * 
     * @param array $resource
     * 
     * @return array
     */
    
    function make_param_create($params=array())
    {
        $data = array();
        $user_A = $this->ci->user_model->get_info(array('id'=>$params->data['user_A']))->row;
        $data['user_A'] = array(
            'id'=>$user_A->id,
            'username'=>$user_A->username,
            'realname'=>$user_A->realname
            );

        switch($params['area']){
            case "user":
                if(in_array($params['type'], array('follow'))){
                    $user_B = $this->ci->user_model->get_info(array('id'=>$params->data['user_B']))->row;
                    $data['user_B'] = array(
                        'id'=>$user_B->id,
                        'username'=>$user_B->username,
                        'realname'=>$user_B->realname
                        );
                }
            break;
            case "work":
                if(in_array($params['type'], array('work'))){
                    $work = $this->ci->work_model->get_info(array('work_id'=>$work_id))->row;
                    $params->data['user_B'] = $work->user->id;
                    $data['work'] = array(
                        'work_id' => $work->work_id,
                        'title' => $work->title
                        );

                    $user_B = $this->ci->user_model->get_info(array('id'=>$params->data['user_B']))->row;
                    $data['user_B'] = array(
                        'id'=>$user_B->id,
                        'username'=>$user_B->username,
                        'realname'=>$user_B->realname
                        );
                }
            break;
            default:
                $this->last_error = @json_encode(array('status'=>'fail', 'message'=>'no_have_area'));
                return array();
            break;
        }

        return $data;
    }
    
    /**
     * do after post process for user.
     * 
     * @param string $area, string $type, array $resource
     * 
     * @return bool
     */
    
    function after_post($area, $type, $resource=array())
    {
    }
}