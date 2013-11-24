<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Member extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();

        $ci =& get_instance();
        
        $this->load->model('oldmodel/user_db');
    }
    
    /*
     * return user info
     * 
     * @param int $user_id, string $username
     * @return array
     */
    function get_user_info($user_id='', $username='', $email=''){
        $this->load->model('oldmodel/auth_model');
        return $this->auth_model->get_user_info($user_id, $username, $email);
    }
    
    /*
     * post user info
     * 
     * @param array $data, int $user_id
     * @return bool
     */
    function post_user_info($data=array())
    {
     
        if($this->check_invite_code_available($data['invite_code'])!='invite_error')
            return "invite_error";

        $result = $this->tank_auth->create_user(
                $data['username'],$data['email'],$data['password'],$this->config->item('email_activation', 'tank_auth'));
                
        if(is_null($result)) 
            return "user_create_error";           
        
        if(!$this->user_db->_update_invite_code(array('code'=>strtoupper(str_replace("-", "", $data['invite_code'])), 'set_use'=>'notefolio', 'user_id'=>$result['user_id']))){
            $this->tank_auth->delete_user($result['user_id']);
            return "invite_code_register_error";
        }
        
        foreach($data as $k=>$v){
            switch($k){
                case 'gender':
                case 'realname':
                case 'description':                    
                case 'birth':
                    $profile_data[$k] = $v;
                    break;
                    
                case 'homepage':
                    $profile_data['website'] = $v;
                    break;
                    
                case 'facebook_url':
                    $profile_data['facebook_id'] = $v;
                    break;
                    
                case 'twitter_screen_name':
                    $profile_data['twitter_id'] = $v;
                    break;
                case 'username':
                    $username = $v;
                case 'categories':
                    $category_list = explode(',', $v);
                default:
                    break;
            }
        }
        
        if( !$this->_change_category($category_list, $result['user_id']))
            return "user_category_update_error";
        
        if( !$this->user_db->_update_user_profile($result['user_id'], $profile_data) )
            return "user_profile_upate_error";
         
        if(!isset($data['recommend'])) $data['recommend'] = '';
        //log_message('debug', 'follow: '.var_export($data['recommend'],true));
        $this->_follow_user_multi($data['recommend'], $result['user_id']);
        
        if ($this->config->item('email_account_details', 'tank_auth')) {    // send "welcome" email

            $this->_send_email('welcome', $data['email'], $data);
        }
        
        return array(
            'user_id' => $result['user_id'],
            'username' => $data['username']
        );
    }
    
    /*
     * put user info
     * 
     * @param array $data, int $user_id
     * @return bool
     */
    function put_user_info($data=array(), $user_id='')
    {
        if ($user_id==''){ # if user_id is not set
            $user_id=$this->tank_auth->get_user_id();
        } else if ($user_id!=$this->tank_auth->get_user_id()){ // 본인의 것에 대해서만 수정하도록..
            return FALSE;
        }
        
        //var_export($data);
        //die();
        
        /*
         *  array ( 
         * 'gender' => 'f', 
         * 'realname' => '홍길동', 
         * 'username' => 'maxzidell', 
         * 'categories' => '', 'description' => '', 
         * 'homepage' => 'http://whooing.com', 
         * 'facebook_url' => 'maxzidell', 
         * 'twitter_screen_name' => 'hong GD', 
         * 'birth' => '', )
         * 
         */
        /*
         *
         *  UPDATE `notefolio`.`user_profiles`
         *  SET
         *  `id` = {id: },
         *  `user_id` = {user_id: },
         *  `location` = {location: },
         *  `categories` = {categories: },
         *  `website` = {website: },
         *  `facebook_id` = {facebook_id: },
         *  `twitter_id` = {twitter_id: },
         *  `gender` = {gender: },
         *  `level` = {level: },
         *  `realname` = {realname: },
         *  `phone` = {phone: },
         *  `description` = {description: },
         *  `moddate` = {moddate: CURRENT_TIMESTAMP},
         *  `regdate` = {regdate: 0000-00-00 00:00:00}
         *  WHERE <{where_condition}>;
         *  
         */
        foreach($data as $k=>$v){
            switch($k){
                case 'gender':
                case 'realname':
                case 'description':                    
                case 'birth':
                    $profile_data[$k] = $v;
                    break;
                    
                case 'homepage':
                    $profile_data['website'] = $v;
                    break;
                    
                case 'facebook_url':
                    $profile_data['facebook_id'] = $v;
                    break;
                    
                case 'twitter_screen_name':
                    $profile_data['twitter_id'] = $v;
                    break;
                case 'username':
                    $username = $v;
                case 'categories':
                    $category_list = explode(',', $v);
                default:
                    break;
            }
        }
        /*if(isset($data['year'])&&isset($data['month'])&&isset($data['day'])) {
            $profile_data['birth']=implode("-", array($data['year'],$data['month'],$data['day']));
        }*/
        
        log_message('debug','Data Send: '.json_encode($profile_data));
        if( $this->user_db->_update_user_profile($user_id, $profile_data) ){
            if ((count($profile_data)==1&&isset($profile_data['description']))
                ||((isset($category_list) && $this->_change_category($category_list))
                  &&(isset($username) && $this->_change_username($username)))){
                return $this->tank_auth->get_username();
            }
        }
        else{
            return FALSE;
        }
        /*
        $dev = TRUE;
        if(isset($dev)){
            return 'maxzidell'; // return username
        }
        */
    }
    
    /*
     * delete user info
     * 
     * @param array $data, int $user_id
     * @return bool
     */
    function delete_user_info($data=array(), $user_id='')
    {
        if($this->acp->is_elevated()==0){
            return FALSE;
        } else if ($user_id==''){ # if user_id is not set
            return FALSE;
        } else if ($user_id!=$this->tank_auth->get_user_id()){ // 본인의 것에 대해서만 수정하도록..
            return FALSE;
        }
        
        if ($data['del_work']){
            
        }
        
        if ($data['del_work_log']){
            
        }
        
        if ($data['del_work_comment']){
            
        }
        
        if ($data['del_user_profile_comment']){
            
        }
        
        if ($data['del_collect']){
            
        }
        
        if ($data['del_following']){
            
        }
        
        if ($data['del_follower']){
            
        }
        
        if ($data['del_fb_info']){
            
        }
        
        if ($data['a']){//asdf
            
        }
        
    }
    
    /*
     * return user info by fb_num_id
     * 
     * @param string $fbid
     * @return array
     */
    function get_user_info_by_fbid($fbid=''){

        $data = $this->user_db->_get_user_fb(array('fb_num_id' =>$fbid));
        if(sizeof($data)>0) {
            $user_id=$data['id'];
        } else {
            $user_id=0;
        }
        
        $output = $this->get_user_info($user_id);
                    
        return $output;
    }
    
    /*
     * post user facebook info
     * 
     * @param int $user_id, string $username
     * @return array
     */
    function post_user_fb_info($user_id='', $fb_num_id=''){
        if($this->user_db->_insert_user_fb(array('id'=>$user_id,'fb_num_id'=>$fb_num_id, 'access_token'=>$this->fbsdk->getAccessToken()))){
            return TRUE;
        }
        return FALSE;
    }
    
    /*
     * put user facebook info
     * 
     * @param int $user_id, string $username
     * @return array
     */
    function put_user_fb_info($user_id='', $fb_num_id=''){
        if($this->user_db->_update_user_fb(array('id'=>$user_id,'fb_num_id'=>$fb_num_id, 'set_access_token'=>$this->fbsdk->getAccessToken()))){
            return TRUE;
        }
        return FALSE;
    }
    
    /*
     * delete user facebook info
     * 
     * @param int $user_id, string $username
     * @return array
     */
    function delete_user_fb_info($user_id='', $fb_num_id=''){
        if($this->user_db->_delete_user_fb(array('id'=>$user_id,'fb_num_id'=>$fb_num_id))){
            return TRUE;
        }
        return FALSE;
    }
    
    function check_invite_code_available($invite_code='')
    {
        if(empty($invite_code))
            return "invite_error";
        
        $invite_code = strtoupper(str_replace("-", "", $invite_code));
        
        $this->load->model('oldmodel/user_db');
        
        $data=$this->user_db->_get_invite_code_list(array("code"=>$invite_code, "user_id"=>0 ));
        if(sizeof($data)===1) {
           return TRUE;
        }
        
        return "invite_error";
        
        
        // 존재하지 않는지 체크하고 존재하면 true, 존재하지 않으면 false를 리턴한다.

        return rand(0,2)==1 ? TRUE : FALSE;        
    }

    
    /*
     * if username is available?
     * 
     * @param string $username
     * @return bool
     */
    function check_username_available($username='')
    {
        if($username=='')
            return NULL;
        if ($username == $this->tank_auth->get_username()) { // 현재 Username과 동일하면 바꿀 필요 없다
            return TRUE;
        }
        
        // 존재하지 않는지 체크하고 존재하지 않으면 true, 존재하면 false를 리턴한다.
        
        if($result = $this->user_db->_get_user(array('username' => $username))) {
            if($result['id'] != $this->tank_auth->get_user_id()) {
                return FALSE;
            }         
        }
        
        return TRUE;
    }
    
    /*
     * @brief change username
     * 
     * @param string $username
     * @return bool
     */
    function _change_username($username=''){
        if ($username==''){ // no given username
            return TRUE;
        } else if ($username == $this->tank_auth->get_username()) { // 현재 Username과 동일하면 바꿀 필요 없다
            return TRUE;
        }
        
        if($this->check_username_available($username)) {
            if ($this->user_db->_update_user($this->tank_auth->get_user_id(), array('username'=> $username))){
                $this->session->set_userdata(array(
                                    'username'  => $username,
                            ));
                
                return TRUE;
            }
        }
        return FALSE;
    }

    /*
     * @brief set new email
     * 
     * @param string $email, string $pwd
     * @return bool
     */
    function set_new_email($email, $pwd){
        
        if(is_null($data = $this->tank_auth->set_new_email($email, $pwd)))
            return NULL;
        
        if($result = $this->tank_auth->activate_new_email($data['user_id'], $data['new_email_key']))
            return $data;
        
        var_export($result);
        die();
        
        return NULL;
    }
    
    /*
     * @brief change category
     * 
     * @param array $category
     * @return bool
     */
    function _change_category($category=array(), $user_id=''){
        if ($category==array()){ // no given
            return FALSE;
        }
        if ($user_id=='') {
            $user_id = $this->tank_auth->get_user_id();
        }
        
        $org_list = $this->user_db->_get_user_category_list(array('user_id'=>$user_id), array('category'));
        
        $del_list = array_diff($org_list, $category);
        $add_list = array_diff($category, $org_list);
        
        if ($del_list!=array()) {
            foreach($del_list as $v) {
                if(!$this->user_db->_delete_user_category($user_id, array('category'=>$v))){
                    return FALSE;
                }
            }
        }
        if ($add_list!=array()) {
            foreach($add_list as $v) {
                if(!$this->user_db->_insert_user_category($user_id, array('category'=>$v))){
                    return FALSE;
                }
            }
        }
        
        return TRUE;
    }
    
    function _follow_user_multi($list='', $user_id=0){
        if (!is_array($list)) $list = @explode(',', $list);
        log_message('debug', 'follow[array]: '.var_export($list,true));
        if(sizeof($list)>0){
            foreach ($list as $v) {
                $this->user_db->_insert_user_follow($v,$user_id);
            }
        }
        return true;
    }
    
    // header에서 쓰기위해서 임시로 함.
    // 나중에 성수씨가 model로 옮겨주세요. model이 보다 더 적합하다고 판단됨.
    // 아래의 모든 것들은 get_user_info랑 연결되어도 상관없음. 결과적으로 realname과 p_i라는 세션만 있으면 됨.
    function _after_login_session()
    {
        $CI =& get_instance();
        
        // realname을 위해서 쿼리날림.
        $CI->load->database();
        $user_profiles = $CI->db->select('realname')->where('user_id', $CI->session->userdata('user_id'))->get('user_profiles')->row();
        $CI->session->set_userdata('realname', $user_profiles->realname);
        
        // 아이콘 출력을 위해서...
        if(file_exists(APPPATH.'../www/profiles/'.$CI->session->userdata('user_id')))
            $CI->session->set_userdata('p_i', time());
        else
            $CI->session->set_userdata('p_i', 0);
    }

    /**
     * Send email message of given type (activate, forgot_password, etc.)
     *
     * @param   string
     * @param   string
     * @param   array
     * @return  void
     */
    function _send_email($type, $email, &$data)
    {
        $this->load->library('email');
        $this->email->from($this->config->item('webmaster_email', 'tank_auth'), $this->config->item('website_name', 'tank_auth'));
        $this->email->reply_to($this->config->item('webmaster_email', 'tank_auth'), $this->config->item('website_name', 'tank_auth'));
        $this->email->to($email);
        $this->email->subject(sprintf($this->lang->line('auth_subject_'.$type), $this->config->item('website_name', 'tank_auth')));
        $this->email->message($this->load->view('email/'.$type.'-html', $data, TRUE));
        $this->email->set_alt_message($this->load->view('email/'.$type.'-txt', $data, TRUE));
        $this->email->send();
        
    }
}
