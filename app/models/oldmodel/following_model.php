<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Following_model extends CI_Model
{

	function __construct()
	{
		parent::__construct();
		$this->load->database();

		$ci =& get_instance();
        
        $this->load->model('oldmodel/auth_model');
        $this->load->model('oldmodel/work_model');
        $this->load->model('oldmodel/user_db');
	}


	/**
	 *
	 * 추천 팔로우 리스트, 가입시에 출력하기 위함.
	 */
	function get_recommend($categories=array(), $limit=10)
	{	    
        $user_query=array();
        $user_field=array();
        if ($categories!='') {
            $category_search_query = $this->user_db->_get_user_category_list(array('category_in'=>$categories), array('user_id'), array(), array('category'=>'asc'),
                                                                        $opt=array('return_type'=>'last_query'));
            
            $user_query['category_join'] = array('table'=>"(".$category_search_query.") category", 'on'=>'users.id = category.user_id', 'type'=>'right');
        }
        
        $user_query['upload_works_recent_join'] = array('table'=>"user_when_upload_works_recent", 'on'=>'user_when_upload_works_recent.user_id = users.id', 'type'=>'left');;
        $user_query['work_count >'] = 0;
        
        $user_list = $this->user_db->_get_user_list($user_query, $user_field, array(1, $limit), array('work_regdate'=>'desc'));
        
        $result = array();
        for($i=0;$i<count($user_list);$i++){ // 리스트 반환
        
            $user_array = $this->auth_model->get_user_info($user_list[$i]['id']);
            
            $result[$i] = $user_array;
        }
        return $result;

		// 완료되면 아래 변수 선언부만 주석처리
		$dev = TRUE;
		if(isset($dev)){
			$tmp = array(
				"user_id" => 2398,
				"realname" => "홍길동",
				"profile_image" => "/images/profile_img?p=23",
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
			);
            $result = array();
            for($i=0;$i<count($user_list);$i++){ // 리스트 반환을 위해 dummy 생성
                $tmp['user_id'] = $i+10;
                $result[] = $tmp;
            }
            return $result;
		}
				
	}

	/**
	 *
	 * 추천 팔로우 리스트, 가입시에 출력하기 위함.
	 */
	function get_recommend_new($categories=array(), $limit=10)
	{
	   $user_query=array();
        $user_field=array();
        if ($categories!='') {
            $category_search_query = $this->user_db->_get_user_category_list(array('category_in'=>$categories), array('user_id'), array(), array('category'=>'asc'),
                                                                        $opt=array('return_type'=>'last_query'));
            
            $user_query['category_join'] = array('table'=>"(".$category_search_query.") category", 'on'=>'users.id = category.user_id', 'type'=>'right');
        }
        
        $user_query['upload_works_recent_join'] = array('table'=>"user_when_upload_works_recent", 'on'=>'user_when_upload_works_recent.user_id = users.id', 'type'=>'left');;
        $user_query['work_count >'] = 0;
        
        $user_list = $this->user_db->_get_user_list($user_query, $user_field, array(1, $limit), array('work_regdate'=>'desc'));
        
        $result = array();
        for($i=0;$i<count($user_list);$i++){ // 리스트 반환
        
            $user_array = $this->auth_model->get_user_info($user_list[$i]['id']);
		
             $recent_work_list = $this->work_db->_get_list(array('user_id'=>$user_list[$i]['id']), array('id as work_id', 'moddate'), array(1,2));
	        for($j=0;$j<count($recent_work_list);$j++){
			    if (is_file($this->input->server('DOCUMENT_ROOT').'/thumbnails/'.(isset($recent_work_list[$j]['work_id'])?$recent_work_list[$j]['work_id']:0)))
	                $recent_work_list[$j]['thumbnail_url'] = "/thumbnails/"
	                										.(isset($recent_work_list[$j]['work_id'])?$recent_work_list[$j]['work_id']:0)
	                										."?t="
	                										.(isset($recent_work_list[$j]['moddate'])?strtotime($recent_work_list[$j]['moddate']):time());
				else $recent_work_list[$j]['thumbnail_url'] = '/images/work_thumbnail';
			}
            $user_array['recent_works'] = $recent_work_list;
            $result[$i] = $user_array;
        }
        return $result;

		// 완료되면 아래 변수 선언부만 주석처리
		$dev = TRUE;
		if(isset($dev)){
			$tmp = array(
				"user_id" => 2398,
				"realname" => "홍길동",
				"profile_image" => "/images/profile_img?p=23",
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
			);
            $result = array();
            for($i=0;$i<count($user_list);$i++){ // 리스트 반환을 위해 dummy 생성
                $tmp['user_id'] = $i+10;
                $result[] = $tmp;
            }
            return $result;
		}
				
	}



	/*
	 * 메인에 직접 선택해서 불러오는 follow list
	 */
	function get_feat_follow($categories=array(), $limit=4)
	{
	    
        $user_query=array();
        $user_field=array();
        $user_query['feat_follow_join'] = array('table'=>"feat_follow_list", 'on'=>'feat_follow_list.user_id = users.id');		
		
		$user_list = $this->user_db->_get_user_list($user_query, $user_field, array(1,4), array('feat_date'=>'desc'));
        
        $result = array();
        for($i=0;$i<count($user_list);$i++){ // 리스트 반환
        
            $user_array = $this->auth_model->get_user_info($user_list[$i]['id']);
            
            $result[$i] = $user_array;
        }
        return $result;		

		// 완료되면 아래 변수 선언부만 주석처리
		$dev = TRUE;
		if(isset($dev)){
			$tmp = array(
				"user_id" => 2398,
				"realname" => "홍길동",
				"profile_image" => "/images/profile_img?p=23",
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
			);
            $result = array();
            for($i=0;$i<count($user_list);$i++){ // 리스트 반환을 위해 dummy 생성
                $tmp['user_id'] = $i+10;
                $result[] = $tmp;
            }
            return $result;
		}
				
	}
	

	function get_following_list($user_id='', $page=1, $limit=10, $limit_opt=0, $opt=array())
	{
	    if ($user_id=='') return array();
        
	    $list = $this->user_db->_get_user_follow_list(array('follower_id'=>$user_id), array('follow_id'), array($page, $limit), array('id'=>'desc'), array('limit_opt'=>$limit_opt));
        
        $result = array();
        $work_list = array();
        
        for($i=0;$i<count($list);$i++){ // 리스트 반환
        
            $user_array = $this->auth_model->get_user_info($list[$i]['follow_id']);
			//$user_array['recent_works'] = $this->work_model->get_work_list('','',$list[$i]['follow_id'],1,2);
            
            $recent_work_list = $this->work_db->_get_list(array('user_id'=>$list[$i]['follow_id']), array('id as work_id', 'moddate'), array(1,2));
	        for($j=0;$j<count($recent_work_list);$j++){
			    if (is_file($this->input->server('DOCUMENT_ROOT').'/thumbnails/'.(isset($recent_work_list[$j]['work_id'])?$recent_work_list[$j]['work_id']:0)))
	                $recent_work_list[$j]['thumbnail_url'] = "/thumbnails/"
	                										.(isset($recent_work_list[$j]['work_id'])?$recent_work_list[$j]['work_id']:0)
	                										."?t="
	                										.(isset($recent_work_list[$j]['moddate'])?strtotime($recent_work_list[$j]['moddate']):time());
				else $recent_work_list[$j]['thumbnail_url'] = '/images/work_thumbnail';
			}
            $user_array['recent_works'] = $recent_work_list;
            $result[$i] = $user_array;
        }
        
        return $result;
	    
        
		// 완료되면 아래 변수 선언부만 주석처리
		$dev = TRUE;
		if(isset($dev)){
			$tmp = array(
				"user_id" => 2398,
				"realname" => "홍길동",
				"profile_image" => "/images/profile_img",
				"homepage" => "",
				"twitter_screen_name" => "hong GD",
				"facebook_url" => "",
				"description" => "부자집을 털어보자",
				"categories" => array(
					"fine_art",
					"digital_art",
					"ui_ux"
				),
				"recent_works" => array(
					array(
						"work_id" => 23989,
						"thumbnail_url" => "/images/work_thumbnail",
						"title" => "끝내주는 처녀작",
						"categories" => array(
							"fine_art",
							"digital_art"
						),				
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
						"note_count" => 98,
						"hit_count" => 2938,
						"comment_count" => 12,
						"collected" => TRUE,
						"ins_time" => 1293882332
					),
					array(
						"work_id" => 23989,
						"thumbnail_url" => "/images/work_thumbnail",
						"title" => "끝내주는 처녀작",
						"categories" => array(
							"fine_art",
							"digital_art"
						),				
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
						"note_count" => 98,
						"hit_count" => 2938,
						"comment_count" => 12,
						"collected" => TRUE,
						"ins_time" => 1293882332
					)
				),				"gender" => "f",
				"followed" => TRUE
			);
			$result = array();
			for($i=0;$i<$limit;$i++){ // 리스트 반환을 위해 dummy 생성
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
	
	
	
	function post_following($user_id='')
	{
        if($user_id=='') return FALSE;
        
        $user_array = $this->auth_model->get_user_info($user_id);
        if($user_array['followed']) return TRUE;
                
        return $this->user_db->_insert_user_follow($user_id);
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



	function delete_following($user_id='')
	{
	    if($user_id=='') return FALSE;
        
        $user_array = $this->auth_model->get_user_info($user_id);
        if(!$user_array['followed']) return TRUE;
	    
	    return $this->user_db->_delete_user_follow($user_id);
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
	




	function get_follower_list($user_id='', $page=1, $limit=1, $limit_opt=0, $opt=array())
	{
        if ($user_id=='') return array();
        
        $list = $this->user_db->_get_user_follow_list(array('follow_id'=>$user_id), array('follower_id'), array($page, $limit), array('id'=>'desc'), array('limit_opt'=>$limit_opt));
        
        $result = array();
        for($i=0;$i<count($list);$i++){ // 리스트 반환
        
            $user_array = $this->auth_model->get_user_info($list[$i]['follower_id']);
            //$user_array['recent_works'] = $this->work_model->get_work_list('','',$list[$i]['follower_id'],1,2);
            
            $recent_work_list = $this->work_db->_get_list(array('user_id'=>$list[$i]['follower_id']), array('id as work_id', 'moddate'), array(1,2));
	        for($j=0;$j<count($recent_work_list);$j++){
			    if (is_file($this->input->server('DOCUMENT_ROOT').'/thumbnails/'.(isset($recent_work_list[$j]['work_id'])?$recent_work_list[$j]['work_id']:0)))
	                $recent_work_list[$j]['thumbnail_url'] = "/thumbnails/"
	                										.(isset($recent_work_list[$j]['work_id'])?$recent_work_list[$j]['work_id']:0)
	                										."?t="
	                										.(isset($recent_work_list[$j]['moddate'])?strtotime($recent_work_list[$j]['moddate']):time());
				else $recent_work_list[$j]['thumbnail_url'] = '/images/work_thumbnail';
			}
            $user_array['recent_works'] = $recent_work_list;
            $result[$i] = $user_array;
        }
        return $result;
        
		// 완료되면 아래 변수 선언부만 주석처리
		$dev = TRUE;
		if(isset($dev)){
			$tmp = array(
				"user_id" => 2398,
				"realname" => "홍길동",
				"profile_image" => "/images/profile_img",
				"homepage" => "",
				"twitter_screen_name" => "hong GD",
				"facebook_url" => "",
				"description" => "부자집을 털어보자",
				"categories" => array(
					"fine_art",
					"digital_art",
					"ui_ux"
				),
				"recent_works" => array(
					array(
						"work_id" => 23989,
						"thumbnail_url" => "/images/work_thumbnail",
						"title" => "끝내주는 처녀작",
						"categories" => array(
							"fine_art",
							"digital_art"
						),				
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
						"note_count" => 98,
						"hit_count" => 2938,
						"comment_count" => 12,
						"collected" => TRUE,
						"ins_time" => 1293882332
					),
					array(
						"work_id" => 23989,
						"thumbnail_url" => "/images/work_thumbnail",
						"title" => "끝내주는 처녀작",
						"categories" => array(
							"fine_art",
							"digital_art"
						),				
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
						"note_count" => 98,
						"hit_count" => 2938,
						"comment_count" => 12,
						"collected" => TRUE,
						"ins_time" => 1293882332
					)
				),
				"gender" => "f",
				"followed" => TRUE
			);
			$result = array();
			for($i=0;$i<$limit;$i++){ // 리스트 반환을 위해 dummy 생성
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
    
    /*
     * return user follow count
     * 
     * @param int $user_id, string $key
     * 
     * @return int
     */
     function get_user_follow_count($user_id='', $key='ing') {
        if ($user_id=='') return 0;
        
        switch($key) {
            case "following":
            case "ing":
                $data = $this->user_db->_get_user(array('user_id'=>$user_id), array('following_cnt'));
                break;
            case "follower":
            case "er":
                $data = $this->user_db->_get_user(array('user_id'=>$user_id), array('follower_cnt'));
                break;
        }
        
        return $data[0]['count'];
     }
	

}
