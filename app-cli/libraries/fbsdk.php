<?php
/**
 * @brief Facebook SDK Connect Library
 *
 * @author Yoon, Seongsu(soplel@snooey.net)
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once (APPPATH.'../app/libraries/fb-sdk/facebook.php');

class Fbsdk extends Facebook
{
    var $last_response = "";
    
    function __construct($config=null) {
        $this->ci =& get_instance();
        
        if($config==null) {
            $config = array(
                  'appId'  => $this->ci->config->item('facebook_app_id'),
                  'secret' => $this->ci->config->item('facebook_app_secret'),
                  'sharedSession' => FALSE,
                  'trustForwarded' => true,
            );
        }
        parent::__construct($config);
    }
    
    /*
     * set data for facebook
     * 
     * @param int $user_id, array $data
     * 
     * @return bool
     */
    function set_data($user_id=0, $data=array())
    {
 /*       if($user_id==0) return false;
        $this->ci->load->model('api/auth_model');
        if(count($data)>0&&isset($data['action'])){
            switch($data['action']){
                case "unlink":
                    return $this->ci->auth_model->delete_user_fb_info($user_id);
                    break;
            }
        }
*/        
        return FALSE;
    }
    
    /*
     * post data to facebook
     * 
     * @param int $user_id, array $data
     * 
     * @return int
     */
    function post_data($user_id=0, $data=array())
    {
        if($user_id==0) return false;
        $this->ci->load->model('user_model');
        echo('user_id: '.$user_id.PHP_EOL);
        $fb_info = $this->ci->user_model->get_info_sns_fb(array(
                'id'=> $user_id
            ));
        
        if(isset($fb_info->row)&&$fb_info->row->{$data['type']}=='Y') {
            if(count($data)>0&&isset($data['type'])){
                $post['access_token'] = $fb_info->row->access_token;
                switch($data['type']){
                    case "post_work":
                        $target = ":upload";
                        $post['work'] = $data['base_url'].$data['work_uploader']."/".$data['work_id'];
                        break;
                    case "post_comment":
                        $target = ":comment";
                        $post['work'] = $data['base_url'].$data['work_uploader']."/".$data['work_id'];
                        break;
                    case "post_note":
                        $target = ":note";
                        $post['work'] = $data['base_url'].$data['work_uploader']."/".$data['work_id'];
                        break;
                    case "post_test":
                        $target = ":test";
                        $post['work'] = $data['base_url'].$data['work_uploader']."/".$data['work_id'];
                        break;
                }
                try {
                    $this->last_response = $this->api('/me/notefolio'.$target, 'POST', $post);
                } catch (FacebookApiException $e) {
                    error_log($e);
                    error_log($this->last_response);
                    return FALSE;
                }
            }
        } else {
            $this->last_response=json_encode(array("error"=>array("code"=>0, "message"=>"Cannot Send because something wrong.", "user_id"=>$user_id, "data"=>$data, "fb_info"=>$fb_info)));
            return FALSE;
        }
        
        return TRUE;
    }
}

?>