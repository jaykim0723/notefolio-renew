<?php
/**
 * @brief Facebook SDK Connect Library
 *
 * @author Yoon, Seongsu(soplel@snooey.net)
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once (APPPATH.'libraries/fb-sdk/facebook.php');

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
        if($user_id==0) $user_id = $this->ci->tank_auth->get_user_id();
        $this->ci->load->model('api/auth_model');
        if(count($data)>0&&isset($data['action'])){
            switch($data['action']){
                case "unlink":
                    return $this->ci->auth_model->delete_user_fb_info($user_id);
                    break;
            }
        }
        
        return FALSE;
    }

    /**
     * 프로필사진을 선정하고 크롭까지 지정하고 넘어온다.
     * @return [type] [description]
     */
    function get_face($username=''){
        $this->ci->load->config('upload', TRUE);
        $this->ci->load->model('upload_model');
        $this->ci->load->library('file_save');

        if(empty($username)){
            $username = $this->ci->tank_auth->get_username();
        }

        $filename = 'facebook_face_'.$username.'.jpg';
        var_export($filename);
        exit();
        $image = $this->api('/me/picture/?redirect=false&width=1600');
        if(!empty($image['data']['url'])){
            $resource = $this->ci->file_save->save_from_url($image['data']['url'], $filename);
        }
        if($resource){
            $upload = $this->upload_model->post(array(
                'work_id' => $this->input->get_post('work_id'),
                'type' => 'fb',
                'filename' => $resource['original'],
                'org_filename' => $filename,
                'filesize' => 0,
                'comment' => 'facebook face image'
            ));
        }
        else{
            return false;
        }

        $filename = preg_replace(
                        '/^(..)(..)([^\.]+)(\.[a-zA-Z]+)/', 
                        '$1/$2/$1$2$3$4', 
                        $resource['original']
                        );

        $result = $this->ci->file_save->make_thumbnail(
            $this->ci->config->item('img_upload_path', 'upload').$filename,
            $this->ci->config->item('profile_upload_path', 'upload').$username.'_face.jpg',
            'profile_face', 
            array('crop_to'=>$to_crop, 'spanning'=>true)
            );

        if($result=='done'){
            $this->ci->user_model->put_timestamp(array('id'=>USER_ID));
        }

        return $result;
    }

    /**
     * 프로필의 배경사진을 바꾸는 것
     * @return [type] [description]
     */
    function get_bg($username=''){
        $this->load->config('upload', TRUE);
        $this->load->model('upload_model');
        $this->load->library('file_save');

        if(empty($username)){
            $username = $this->ci->tank_auth->get_username();
        }

        $filename = 'facebook_cover_'.$username.'.jpg';
        $image = $this->api('/me?fields=cover&width=710&height=710&redirect=false');

        if(!empty($image['cover']['source'])){
            $resource = $this->ci->file_save->save_from_url($image['cover']['source'], $filename);
        }
        if($resource){
            $upload = $this->upload_model->post(array(
                'work_id' => $this->input->get_post('work_id'),
                'type' => 'fb',
                'filename' => $resource['original'],
                'org_filename' => $filename,
                'filesize' => 0,
                'comment' => 'facebook cover image'
            ));
        }
        else{
            return false;
        }

        $filename = preg_replace(
                        '/^(..)(..)([^\.]+)(\.[a-zA-Z]+)/', 
                        '$1/$2/$1$2$3$4', 
                        $resource['original']
                        );

        $result = $this->file_save->make_thumbnail(
            $this->config->item('img_upload_path', 'upload').$filename,
            $this->config->item('profile_upload_path', 'upload').$username.'_bg.jpg',
            'large', array('spanning'=>true)
            );

        if($result=='done'){
            $this->user_model->put_timestamp(array('id'=>USER_ID));
        }

        return $result;
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
        if($user_id==0) $user_id = $this->ci->tank_auth->get_user_id();
        $this->ci->load->model('api/auth_model');
        $fb_info = $this->ci->auth_model->get_user_fb_info($user_id);
        
        if ($fb_info[$data['type']]=='Y') {
            if(count($data)>0&&isset($data['type'])){
                $post['access_token'] = $fb_info['access_token'];
                switch($data['type']){
                    case "post_work":
                        $target = ":upload";
                        $post['work'] = $this->ci->config->item('base_url')."gallery/".$data['work_id'];
                        break;
                    case "post_comment":
                        $target = ":comment";
                        $post['work'] = $this->ci->config->item('base_url')."gallery/".$data['work_id'];
                        break;
                    case "post_note":
                        $target = ":note";
                        $post['work'] = $this->ci->config->item('base_url')."gallery/".$data['work_id'];
                        break;
                    case "post_test":
                        $target = ":test";
                        $post['work'] = $this->ci->config->item('base_url')."gallery/".$data['work_id'];
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