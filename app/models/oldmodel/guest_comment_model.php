<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Guest_comment_model extends CI_Model
{

	function __construct()
	{
		parent::__construct();
		$this->load->database();

		$ci =& get_instance();
	}



	function get_guest_comment_list($user_id='')
	{
        if ($user_id=='')
            return array();
        
        $this->load->model('oldmodel/comment_db');
        $this->load->model('oldmodel/auth_model');
        $this->load->model('oldmodel/guest_recomment_model');
        
        $data = $this->comment_db->_get_list("user_profile", array("user_profile_id"=>$user_id, "parent_id"=>0), array(), array(), array('id'=>'desc'));
	    
        $output = array();
		for($i=0;$i<count($data);$i++){
		    $recomment = $this->comment_db->_get_list("user_profile", array("parent_id"=>$data[$i]['id']), array('count(*) as count'), array(), array('id'=>'desc'));
            
		    $output[$i] = array(
                "comment_id" => $data[$i]['id'],
                "user" => $this->auth_model->get_user_info($data[$i]['user_id']),
                "contents" => nl2br($data[$i]['content']),
                "ins_time" => isset($data[$i]['moddate'])?strtotime($data[$i]['moddate'])+$this->config->item('timezone_calc'):0,
                "recomment_count" => isset($recomment['count'])?$recomment['count']:0,
                "recomments" => $this->guest_recomment_model->get_guest_recomment_list($data[$i]['id']),
            );
            
		    
		}
        
        return $output;
        
        // 완료되면 아래 변수 선언부만 주석처리
		$dev = TRUE;
		if(isset($dev)){
			$re_tmp = array(
				"recomment_id" => 927323,
				"user" => array(
					"user_id" => 1,
					"realname" => "홍길동",
					"username" => "maxzidell",
					"profile_image" => "/images/profile_img",
					"homepage" => "",
					"twitter_screen_name" => "hong GD",
					"facebook_url" => "",
					"description" => "부자집을 털어보자",
					"categories" => array(
						"motorcycle",
						"movie"
					),
					"gender" => "f",
					"followed" => TRUE
				),
				"contents" => "방명록코멘트에 댓글입니다.",
				"ins_time" => 1392399273
			);
			$tmp = array(
				"comment_id" => 239898,
				"user" => array(
					"user_id" => 1,
					"realname" => "홍길동",
					"username" => "maxzidell",
					"profile_image" => "/images/profile_img",
					"homepage" => "",
					"twitter_screen_name" => "hong GD",
					"facebook_url" => "",
					"description" => "부자집을 털어보자",
					"categories" => array(
						"motorcycle",
						"movie"
					),
					"gender" => "f",
					"followed" => TRUE
				),
				"contents" => "방명록 코멘트입니다.",
				"ins_time" => 1392399273,
				"recomment_count" => 19,
				"recomments" => array($re_tmp,$re_tmp,$re_tmp,$re_tmp,$re_tmp)
			);
			$result = array();
			for($i=0;$i<10;$i++){ // 리스트 반환을 위해 dummy 생성
				$tmp['comment_id'] = $i+201;
				$result[] = $tmp;
			}
			return $result;
		}
		
		/* 
		 *	params validation
		 * 	example : 	return array(
		 *					'status'=> 'error',
		 *					'params' => array(
		 *						'user_id' => 'required'
		 *					)
		 *				);
		 */
		 
		 
		 
		/* your real code  */		
		
	}
	
	
	
	function get_guest_comment($comment_id='')
	{
        if ($comment_id=='')
            return array();
        $this->load->model('oldmodel/comment_db');
        $this->load->model('oldmodel/auth_model');
        $this->load->model('oldmodel/guest_recomment_model');
        
	    $data = $this->comment_db->_get_list("user_profile", array("id"=>$comment_id), array(), array(), array('id'=>'desc'));
		if(isset($data[0])){
            $recomment = $this->comment_db->_get_list("user_profile", array("parent_id"=>$comment_id), array('count(*) as count'), array(), array('id'=>'desc'));
         
            $data = $data[0];
            $output = array(
                "comment_id" => $data['id'],
                "user" => $this->auth_model->get_user_info($data['user_id']),
                "contents" => $data['content'],
                "ins_time" => isset($data['moddate'])?strtotime($data['moddate'])+$this->config->item('timezone_calc'):0,
                "recomment_count" => isset($recomment['count'])?$recomment['count']:0,
                "recomments" => $this->guest_recomment_model->get_guest_recomment_list($data['id']),
            );
            
            return $output;
        }
        
        return array();
        		
		// 완료되면 아래 변수 선언부만 주석처리
		$dev = TRUE;
		if(isset($dev)){
			$re_tmp = array(
				"recomment_id" => 927323,
				"user" => array(
					"user_id" => 1,
					"realname" => "홍길동",
					"username" => "maxzidell",
					"profile_image" => "/images/profile_img",
					"homepage" => "",
					"twitter_screen_name" => "hong GD",
					"facebook_url" => "",
					"description" => "부자집을 털어보자",
					"categories" => array(
						"motorcycle",
						"movie"
					),
					"gender" => "f",
					"followed" => TRUE
				),
				"contents" => "방명록코멘트에 댓글입니다.",
				"ins_time" => 1392399273
			);
			$tmp = array(
				"comment_id" => 239898,
				"user" => array(
					"user_id" => 1,
					"realname" => "홍길동",
					"username" => "maxzidell",
					"profile_image" => "/images/profile_img",
					"homepage" => "",
					"twitter_screen_name" => "hong GD",
					"facebook_url" => "",
					"description" => "부자집을 털어보자",
					"categories" => array(
						"motorcycle",
						"movie"
					),
					"gender" => "f",
					"followed" => TRUE
				),
				"contents" => "방명록 코멘트입니다.",
				"ins_time" => 1392399273,
				"recomment_count" => 19,
				"recomments" => array($re_tmp,$re_tmp,$re_tmp,$re_tmp,$re_tmp)
			);
			return $tmp;
		}	

		/* 
		 *	params validation
		 * 	example : 	return array(
		 *					'status'=> 'error',
		 *					'params' => array(
		 *						'user_id' => 'required'
		 *					)
		 *				);
		 */
		 
		 
		 
		/* your real code  */		
	
	}
	
	
	function post_guest_comment($user_id='', $contents='')
	{
	    if ($user_id==''||$contents=='')
            return FALSE;
        
        $this->load->model('oldmodel/comment_db');

        if($this->comment_db->_insert('user_profile', array("user_profile_id"=>$user_id, "parent_id"=>0, "content"=>$contents))){
            return $this->db->insert_id();
        }
        
        return FALSE;
        
		// 완료되면 아래 변수 선언부만 주석처리
		$dev = TRUE;
		if(isset($dev)){
			return rand(0,4)==4 ? FALSE : 2993;
		}


		/* 
		 *	params validation
		 * 	example : 	return array(
		 *					'status'=> 'error',
		 *					'params' => array(
		 *						'user_id' => 'required'
		 *					)
		 *				);
		 */
		 
		 
		/* your real code  */
			
	
	}




	function put_guest_comment($comment_id='', $contents='')
    {
        if ($comment_id==''||$contents=='')
            return FALSE;
        
        $this->load->model('oldmodel/comment_db');
        
        $data = $this->comment_db->_get_list("user_profile", array("id"=>$comment_id), array(), array(), array('id'=>'desc'));
        
        if(isset($data[0])) {
            if($data[0]['user_id']!=$this->tank_auth->get_user_id())
                return FALSE;
          
        }

        if($this->comment_db->_update('user_profile', $comment_id , array( "content"=>$contents))){
            return $comment_id;
        }
        
        return FALSE;

		// 완료되면 아래 변수 선언부만 주석처리
		$dev = TRUE;
		if(isset($dev)){
			return rand(0,4)==4 ? FALSE : 2993;
		}
	

		/* 
		 *	params validation
		 * 	example : 	return array(
		 *					'status'=> 'error',
		 *					'params' => array(
		 *						'user_id' => 'required'
		 *					)
		 *				);
		 */
		 
		 
		/* your real code  */
			
	}
	
	


	function delete_guest_comment($comment_id='', $profile_user_id='')
    {
        if ($comment_id==''||($comment_id==''&& $profile_user_id==''))
            return FALSE;
        
        $this->load->model('oldmodel/comment_db');
        
        $query= array();
        if ($comment_id!='') {
            $query["id"]=$comment_id;
        }
        if ($work_id!='') {
            $query["user_profile_id"]=$profile_user_id;
        }
        
        $data = $this->comment_db->_get_list("user_profile", $query, array(), array(), array('id'=>'desc'));
        
        if(isset($data[0])) {
            if($data[0]['user_id']!=$this->tank_auth->get_user_id())
                return FALSE;
          
        }
        foreach ($data as $comment) {
            /*
            $data = $this->comment_db->_get_list("user_profile", array("parent_id"=>$comment['id']), array("count(*) as count"), array(), array('id'=>'desc'));
            
            if(isset($data[0])) {
                if($data[0]['count']>0){
                    if($this->comment_db->_update('user_profile', $comment['id'] , array("user_id"=> 0, "content"=>"작성자가 지운 댓글입니다."))){
                        return TRUE;
                    }
                }
                else {
                    if($this->comment_db->_delete('user_profile', $comment['id'])){
                        return TRUE;
                    }
                }
                    return FALSE;
              
            }
            */
            
            $result = FALSE;
            if($result = $this->comment_db->_delete('user_profile', $comment['id'])){ //정말 단순 삭제
                $recomment_data = $this->comment_db->_get_list("user_profile", array("parent_id"=>$comment['id']), array(), array(), array('id'=>'asc'));
                
                for($i=0;$i<count($recomment_data);$i++){
                    $this->comment_db->_delete('user_profile', $recomment_data[$i]['id']);
                    
                }
            }
        }

        return $result;
		// 완료되면 아래 변수 선언부만 주석처리
		$dev = TRUE;
		if(isset($dev)){
			return rand(0,4)==4 ? FALSE : TRUE;
		}
	

		/* 
		 *	params validation
		 * 	example : 	return array(
		 *					'status'=> 'error',
		 *					'params' => array(
		 *						'user_id' => 'required'
		 *					)
		 *				);
		 */

		 
		 
		/* your real code  */
			
	}

}
