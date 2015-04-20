<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Auth_model extends CI_Model
{
    var $error = array();
    var $data = array();

	function __construct()
	{
		parent::__construct();
		$this->load->database();

		$ci =& get_instance();
        
        $this->load->model('oldmodel/user_db');
        $this->load->library('acp');
	}
    
    /*
     * return user info
     * 
     * @param int $user_id, string $username
     * @return array
     */
    function get_user_info($user_id='', $username='', $email='', $opt=array()){

         /* your real code  */
         
        $user_query=array();
        
        $user_basic_query = $this->user_db->_get_user(array(),array('last_login','created'),array(),array(),array("return_type"=>"compiled_select"));
        
        $user_profile_query = $this->user_db->_get_user_profile(array(),array('user_id','location','website','facebook_id','twitter_id','gender','phone','birth','description','mailing','following_cnt','follower_cnt','moddate','regdate'),array(),array(),array("return_type"=>"compiled_select"));
        $user_query['profile_join'] =  array('table'=>"(".$user_profile_query.") profile", 'on'=>'users.id = profile.user_id', 'type'=>'left');
        $user_is_following_query = $this->user_db->_get_user_follow_list(array("follower_id"=>$this->tank_auth->get_user_id()), array('follow_id', 'count(`follow_id`) as following'),array(),array(),array("group"=>"follow_id", "return_type"=>"compiled_select"));
        $user_query['is_following_join'] =  array('table'=>"(".$user_is_following_query.") is_following", 'on'=>'users.id = is_following.follow_id', 'type'=>'left');
        $fb_query = $this->user_db->_get_user_fb(array(), array('id as user_id', 'fb_num_id', 'access_token', 'post_work', 'post_comment','post_note'), array(), array('id'=>'desc'), array('return_type'=>'compiled_select'));
        $user_query['fb_join'] = array('table'=>"(".$fb_query.") fb_info", 'on'=>'users.id = fb_info.user_id', 'type'=>'left');
        
        if(isset($opt['fb_num_id'])) {
            $user_query['fb_num_id'] = $opt['fb_num_id'];
        } else if($user_id==''&&$email!='') {
            $user_query['email'] = $email;
        } else if($user_id=='') {
            $user_query['username'] = $username;
        } else {
            $user_query['id'] = $user_id;
        }
        
        $user = $this->user_db->_get_user($user_query);

        //var_export($user_query);
        //var_export($this->db->last_query());
        
        $output = array(
                "user_id" => isset($user['id'])?$user['id']:0,
                "realname" => isset($user['realname'])?$user['realname']:'',
                "username" => isset($user['username'])?$user['username']:'',
                "email" => isset($user['email'])?$user['email']:'',
                "level" => isset($user['level'])?$user['level']:'',
                "profile_image" => "/profiles/".(isset($user['id'])?$user['id']:'')."?h=".(isset($user['moddate'])?strtotime($user['moddate'])+200+$this->config->item('timezone_calc'):time()),
                "homepage" => isset($user['website'])?$user['website']:'',
                "twitter_screen_name" => isset($user['twitter_id'])?$user['twitter_id']:'',
                "facebook_url" => isset($user['facebook_id'])?$user['facebook_id']:'',
                "description" => isset($user['description'])?$user['description']:'',
                //"categories" => (isset($user['id'])&&($user['id']>0))?$this->user_db->_get_user_category_list(array('user_id'=>$user['id']),array('category')):'',
                "gender" => isset($user['gender'])?$user['gender']:'',
                "birth" => isset($user['birth'])?$user['birth']:'',
                "ins_time" => isset($user['regdate'])?$user['regdate']:'',
                "mod_time" => isset($user['moddate'])?$user['moddate']:'',
                "last_login" => isset($user['last_login'])?$user['last_login']:'',
                "created" => isset($user['created'])?$user['created']:'',
                "phone" => isset($user['phone'])?$user['phone']:'',
                "fb_num_id"=> isset($user['fb_num_id'])?$user['fb_num_id']:0,
                "mailing"=> $user['mailing'],
                "following" => isset($user['following_cnt'])?$user['following_cnt']:0,
                "follower" => isset($user['follower_cnt'])?$user['follower_cnt']:0,
                "followed" => (isset($user['following'])&&($user['following']==1))?TRUE:FALSE,
                "activated" => isset($user['activated'])?$user['activated']:0,
            );
            
        if (!is_file($this->input->server('DOCUMENT_ROOT').'/profiles/'.(isset($user['id'])?$user['id']:0)))
            $output['profile_image'] = '/images/profile_img';
            
        if (($user['fb_num_id']!=0)&&$this->tank_auth->get_user_id()==$user['id']){// for fb user
            $output['fb_access_token'] = $user['access_token'];
            $output['fb_post_work'] = $user['post_work'];
            $output['fb_post_comment'] = $user['post_comment'];
            $output['fb_post_note'] = $user['post_note'];
        }
                    
        return $output;
    }
    
    /*
     * post user info
     * 
     * @param array $data, int $user_id
     * @return bool
     */
    function post_user_info($data=array())
    {
        $this->db->trans_start();
     
        if($this->check_invite_code_restrict($data['invite_code'])!='invite_error')
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
                    break;
                case 'categories':
                    $category_list = explode(',', $v);
                    break;
                case 'thumbnail_url':
                    $profile_data['thumbnail_url'] = $v;
                    break;
					
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
        
        $this->db->trans_complete(); 
        
        if ($this->config->item('email_account_details', 'tank_auth')) {    // send "welcome" email

            $this->_send_email('welcome', $data['email'], $data);
        }
        
        return array(
        	'user_id' => $result['user_id'],
        	'username' => $data['username']
        );
    }
    
    /*
     * post user info new version
     * 
     * @param array $data, int $user_id
     * @return bool
     */
    function post_user_info_new($data=array())
    {
        $this->db->trans_start();
        
		$result = $this->tank_auth->create_user(
                $data['username'],$data['email'],$data['password'],$this->config->item('email_activation', 'tank_auth'));
                
        if(is_null($result)) {
            $this->error= "user_create_error";
            return false;
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
                    break;
                case 'categories':
                    $category_list = explode(',', $v);
                    break;
                case 'thumbnail_url':
                    $profile_data['thumbnail_url'] = $v;
                    break;
                case 'mailing':
                    $profile_data['mailing'] = $v;
                    break;
					
                default:
                    break;
            }
        }
        
        if( !$this->_change_category($category_list, $result['user_id'])){
            $this->error= "user_category_update_error";
            return false;
        }
        if( !$this->user_db->_update_user_profile($result['user_id'], $profile_data) ) {
            $this->error= "user_profile_upate_error";
            return false;
        }
         
        if(!isset($data['recommend'])) $data['recommend'] = '';
        //log_message('debug', 'follow: '.var_export($data['recommend'],true));
        $this->_follow_user_multi($data['recommend'], $result['user_id']);
        
        $this->db->trans_complete(); 
        
        $this->data= array(
        	'user_id' => $result['user_id'],
        	'username' => $data['username'],
            'email' => $data['email']
        );
        return true;
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
        } else if ($user_id!=$this->tank_auth->get_user_id()&&$this->acp->is_elevated()==0){
            // 본인의 것에 대해서만 수정하도록.. && 관리자가 아니라면
            return FALSE;
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
                case 'thumbnail_url':
                    $profile_data['thumbnail_url'] = $v;
                    break;	
                case 'username':
                    $username = $v;
                    break;
                case 'categories':
                    $category_list = explode(',', $v);
                    break;
                case 'fb_post_work':
                    $fb_data['set_post_work'] = ($v=="Y")?"Y":"N";
                    break;
                case 'fb_post_comment':
                    $fb_data['set_post_comment'] = ($v=="Y")?"Y":"N";
                    break;
                case 'fb_post_note':
                    $fb_data['set_post_note'] = ($v=="Y")?"Y":"N";
                    break;
                case 'mailing':
                    $profile_data['mailing'] = $v;
                    break;
                default:
                    break;
            }
        }
        /*if(isset($data['year'])&&isset($data['month'])&&isset($data['day'])) {
            $profile_data['birth']=implode("-", array($data['year'],$data['month'],$data['day']));
        }*/
        
        log_message('debug','Data Send: '.json_encode($profile_data));
        //die(var_export($data));
        
        $this->db->trans_start();
        $this->user_db->_update_user_profile($user_id, $profile_data);
        if(isset($category_list))
            $this->_change_category($category_list);
        if(isset($username))
            $this->_change_username($username);
        if(isset($data['fb_num_id']))
            $this->put_user_fb_info($this->tank_auth->get_user_id(), $data['fb_num_id'], $fb_data);
        $this->db->trans_complete(); 
        if($this->db->trans_status()){
            return $this->tank_auth->get_username();
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
        if ($user_id==''){ # if user_id is not set
            $user_id=$this->tank_auth->get_user_id();
        } else if ($user_id!=$this->tank_auth->get_user_id()&&$this->acp->is_elevated()==0){
            // 본인의 것에 대해서만.. && 관리자가 아니라면
            return FALSE;
        }
        
        if (($this->acp->is_elevated()==0)                      // 관리자가 아니고
        &&$user_id!==$this->tank_auth->get_user_id()            // 본인의 것에 대해서만..
        &&(isset($data['ok_to_del'])&&$data['ok_to_del']!='y')){ // 확실히 지우기로 했는가?
            return FALSE;
        }
        
        $this->db->trans_start();
        
        if ($data['del_work']=='y'){
            $work_query=array();
            $work_field=array();
            if ($user_id!='') {
                $work_query['user_id'] = $user_id;
            }
            
            $this->load->model('oldmodel/work_model');
            $this->load->model('oldmodel/comment_db');
            $this->load->model('oldmodel/work_db');
            $this->load->model('oldmodel/log_db');
            
            $work_list = $this->work_db->_get_list($work_query, $work_field, array());
            //var_export($this->db->last_query());
            
            for($i=0;$i<count($work_list);$i++){ // 리스트 반환을 위해 dummy 생성
                $this->work_model->delete_work($work_list['id']);
                $this->log_db->_delete('work', array('work_id'=>$work_list['id']));
                $this->comment_db->_delete('work', 0, array('work_id'=>$work_list['id']));
                $this->work_model->_delete_collect(array('work_id'=>$work_list['id']));
            }
            
        }
        
        if ($data['del_my_work_log']=='y'){
            $this->load->model('oldmodel/log_db');
            $result = $this->log_db->_delete('work', array('user_id'=>$user_id));
            //var_export($result);
            if(!$result) return FALSE;
        }
        
        if ($data['del_my_collect']=='y'){
            $this->load->model('oldmodel/work_db');
            $result = $this->work_db->_delete_collect(array('user_id'=>$user_id));
            //var_export($result);
        }
        
        if ($data['del_my_comment']=='y'){
            $this->load->model('oldmodel/comment_db');
            $this->comment_db->_delete('work', 0, array('user_id'=>$user_id));
            $result = $this->comment_db->_delete('user_profile', 0, array('user_id'=>$user_id));
            //var_export($result);
            
        }
        
        if ($data['del_user_profile_comment']=='y'){
            $this->load->model('oldmodel/comment_db');
            $result = $this->comment_db->_delete('user_profile', 0, array('user_profile_id'=>$user_id));
            //var_export($result);
            if(!$result) return FALSE;
        }
        
        if ($data['del_follow']=='y'){
        
            $this->db->where('follow_id',$user_id);
            $return = $this->db->delete('user_follow'); //delete all follower
            log_message('debug', "Last Query: ".$this->db->last_query());
            $this->db->flush_cache();
            //var_export($result);
            if(!$return){
                return FALSE;
            }
            
            $this->db->where('follower_id',$user_id);
            $return = $this->db->delete('user_follow'); //delete all following
            log_message('debug', "Last Query: ".$this->db->last_query());
            $this->db->flush_cache();
            
            if(!$return){
                return FALSE;
            }
            
        }
        
        if ($data['del_fb_info']=='y'){
            $result=$this->auth_model->delete_user_fb_info($user_id);
            //var_export($result);
        }
        
        if ($data['del_user_basic_data']=='y'){
            $this->load->model('tank_auth/users');
            $output = $this->users->delete_user($user_id);
            var_export($output);
            return $output;
        }
        
        $this->db->trans_complete(); 
        
        return $this->db->trans_status();
        
    }
    
    /*
     * return user info by fb_num_id
     * 
     * @param int $fb_num_id
     * @return array
     */
    function get_user_info_by_fbid($fb_num_id=0){
        $output = $this->get_user_info('', '', '', array('fb_num_id'=>$fb_num_id));
                    
        return $output;
    }
    
    /*
     * get user facebook info
     * 
     * @param int $user_id, string $fb_num_id
     * @return array
     */
    function get_user_fb_info($user_id='', $fb_num_id=''){
        if($user_id!='') {
            $user_query['id'] = $user_id;
        } else if($fb_num_id!='') {
            $user_query['fb_num_id'] = $fb_num_id;
        } else {
            return array();
        }
        
        $output = $this->user_db->_get_user_fb($user_query);
        return $output;
    }
    
    /*
     * post user facebook info
     * 
     * @param int $user_id, string $fb_num_id
     * @return array
     */
    function post_user_fb_info($user_id='', $fb_num_id=''){
        if ($fb_num_id=='') $fb_num_id = $this->fbsdk->getUser();// get the facebook user
        if($this->user_db->_insert_user_fb(array('id'=>$user_id,'fb_num_id'=>$fb_num_id, 'access_token'=>$this->fbsdk->getAccessToken()))){
            return TRUE;
        }
        return FALSE;
    }
    
    /*
     * put user facebook info
     * 
     * @param int $user_id, string $fb_num_id, array $data
     * @return array
     */
    function put_user_fb_info($user_id='', $fb_num_id='', $data=array()){
        $data['id'] = isset($data['id'])?$data['id']:$user_id;
        $data['fb_num_id'] = isset($data['fb_num_id'])?$data['fb_num_id']:$fb_num_id;
        $data['set_access_token'] = isset($data['set_access_token'])?$data['set_access_token']:$this->fbsdk->getAccessToken();
        if($this->user_db->_update_user_fb($data)){
            return TRUE;
        }
        return FALSE;
    }
    
    /*
     * delete user facebook info
     * 
     * @param int $user_id
     * @return array
     */
    function delete_user_fb_info($user_id=0){
        if($user_id==0) $user_id = MY_ID;
        $this->db->trans_start();
        $this->user_db->_delete_user_fb(array('id'=>$user_id));
        $this->db->trans_complete();
        
        return $this->db->trans_status();
    }
    
    function check_invite_code_restrict($invite_code='')
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
     * if email is available?
     * 
     * @param string $email
     * @return bool
     */
    function check_email_available($email='')
    {
        if($email=='')
            return NULL;
        
        // 존재하지 않는지 체크하고 존재하지 않으면 true, 존재하면 false를 리턴한다.
        
        if($result = $this->user_db->_get_user(array('email' => $email))) {
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
        
        //var_export($result);
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
