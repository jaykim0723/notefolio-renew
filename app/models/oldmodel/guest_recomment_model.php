<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Guest_recomment_model extends CI_Model
{

	function __construct()
	{
		parent::__construct();
		$this->load->database();

		$ci =& get_instance();
	}



	function get_guest_recomment_list($comment_id='')
	{
	    if ($comment_id=='')
            return array();
        
        $this->load->model('oldmodel/comment_db');
        $this->load->model('oldmodel/auth_model');
        
        $data = $this->comment_db->_get_list("user_profile", array("parent_id"=>$comment_id), array(), array(), array('id'=>'asc'));
        
        $output = array();
        for($i=0;$i<count($data);$i++){
            $output[$i] = array(
                "recomment_id" => $data[$i]['id'],
                "user" => $this->auth_model->get_user_info($data[$i]['user_id']),
                "contents" => $data[$i]['content'],
                "ins_time" => isset($data[$i]['moddate'])?strtotime($data[$i]['moddate'])+$this->config->item('timezone_calc'):0,
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
			$result = array();
			for($i=0;$i<10;$i++){ // 리스트 반환을 위해 dummy 생성
				$re_tmp['recomment_id'] = $i+201;
				$result[] = $re_tmp;
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
	
	
	
	function get_guest_recomment($recomment_id='')
	{
        if ($recomment_id=='')
            return array();
        
        $this->load->model('oldmodel/comment_db');
        $this->load->model('oldmodel/auth_model');
        
        $data = $this->comment_db->_get_list("user_profile", array("id"=>$recomment_id), array(), array(), array('id'=>'asc'));
        
        if(isset($data[0])){
            $data = $data[0];
            $output = array(
                "recomment_id" => $data['id'],
                "user" => $this->auth_model->get_user_info($data['user_id']),
                "contents" => $data['content'],
                "ins_time" => isset($data['moddate'])?strtotime($data['moddate'])+$this->config->item('timezone_calc'):0,
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
			return $re_tmp;
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
	
	
	function post_guest_recomment($comment_id='', $contents='')
	{
        if ($comment_id==''||$contents=='')
            return FALSE;
        
        $this->load->model('oldmodel/comment_db');

        $parent = $this->comment_db->_get_list("user_profile", array("id"=>$comment_id), array(), array(), array('id'=>'asc'));
        
        if(isset($parent[0])){
            $parent = $parent[0];
        }
        else $parent = array('user_profile_id'=>0);
        
        if($this->comment_db->_insert('user_profile', array("user_profile_id"=>$parent['user_profile_id'], "parent_id"=>$comment_id, "content"=>$contents))){
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




	


	function delete_guest_recomment($recomment_id='')
	{
        if ($recomment_id=='')
            return FALSE;
        
        $this->load->model('oldmodel/comment_db');
        
        if($this->comment_db->_delete('user_profile', $recomment_id))
            return TRUE;
        
        return FALSE;
        
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
