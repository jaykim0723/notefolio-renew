<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Collection_model extends CI_Model
{

	function __construct()
	{
		parent::__construct();
		$this->load->database();

		$ci =& get_instance();
        
        $this->load->model('oldmodel/work_db');
	}

    function get_is_my_collection($work_id='')
    {
        if ($work_id=='') return FALSE;
        $result = $this->work_db->_get_collect_list(array('user_id'=>$this->tank_auth->get_user_id(), 'work_id'=>$work_id), array('count(*) as count'));
            
        if(count($result)>0&&$result[0]['count']>0) return TRUE;
        else return FALSE;
    }

    function get_collection_list($user_id='', $page=1, $limit=10, $limit_opt=0)
	{
        
        $work_query=array('works.id !='=> '0');
        $work_field=array();
        
        if($user_id==''){
            return array();
        } else if ($user_id!='') {
            $collect_query = $this->work_db->_get_collect_list(array('user_id'=>$user_id), array('work_id','comment','regdate as collect_date'), array(), array('regdate'=>'desc'), array('return_type'=>'compiled_select'));
            //var_export($collect_query);
            
			$work_order['collect_date']='desc';
            $work_query['collect_join'] = array('table'=>"(".$collect_query.") collect", 'on'=>"works.id = collect.work_id", 'type'=>'right');
        }
        
            
        $this->load->model('oldmodel/comment_db');
        //$comment_cnt_query = $this->comment_db->_get_list("work", array(), array('work_id', 'count(*) as comment_count'), array(), array(), array('return_type'=>'compiled_select', 'group'=>'work_id'));
        //$work_query['comment_join'] = array('table'=>"(".$comment_cnt_query.") comment_count", 'on'=>'works.id = comment_count.work_id', 'type'=>'left');
        //$work_query['comment_join'] = array('table'=>"log_work_comment_count", 'on'=>'works.id = log_work_comment_count.work_id', 'type'=>'left');		
		$collect_me_query = $this->work_db->_get_collect_list(array('user_id'=>$this->tank_auth->get_user_id()), array('work_id','count(*) as collect_me_count'), array(), array(), array('return_type'=>'compiled_select', 'group'=>'work_id'));
        $work_query['collect_me_join'] = array('table'=>"(".$collect_me_query.") collect_me", 'on'=>"works.id = collect_me.work_id", 'type'=>'left');
        //$work_query['feat_join'] = array('table'=>"log_featured_order", 'on'=>'works.id = log_featured_order.work_id', 'type'=>'left');
	    $this->load->model('oldmodel/log_db');		
        //$note_query = $this->log_db->_get_list("work", array('type'=>'N'), array('work_id', 'count(*) as note_count'), array(), array(), array('return_type'=>'compiled_select', 'group'=>'work_id'));
        //$work_query['note_join'] = array('table'=>"(".$note_query.") note_count", 'on'=>"works.id = note_count.work_id", 'type'=>'left');
        $noted_query = $this->log_db->_get_list("work", array("user_id"=>$this->tank_auth->get_user_id(), 'type'=>'N'), array('work_id', 'count(*) as noted_me_count'), array(), array(),  array('return_type'=>'compiled_select', 'group'=>'work_id'));
        $work_query['noted_join'] = array('table'=>"(".$noted_query.") noted_me", 'on'=>"works.id = noted_me.work_id", 'type'=>'left');
        //$hit_query = $this->log_db->_get_list("work", array('type'=>'V'), array('work_id', 'count(*) as hit_count'), array(), array(), array('return_type'=>'compiled_select', 'group'=>'work_id'));
        //$work_query['hit_join'] = array('table'=>"(".$hit_query.") hit_count", 'on'=>"works.id = hit_count.work_id", 'type'=>'left');
        $hit_me_query = $this->log_db->_get_list("work", array("user_id"=>$this->tank_auth->get_user_id(), 'type'=>'V'), array('work_id', 'count(*) as hit_me_count'), array(), array(), array('return_type'=>'compiled_select', 'group'=>'work_id'));
        $work_query['hit_me_join'] = array('table'=>"(".$hit_me_query.") hit_me_count", 'on'=>"works.id = hit_me_count.work_id", 'type'=>'left');
        $count_query = $this->work_db->_get_count_list(array(),array('work_id','comment_cnt as comment_count', 'hit_cnt as hit_count', 'note_cnt as note_count'), array(), array(), array('return_type'=>'compiled_select'));
		$work_query['count_join'] = array('table'=>"(".$count_query.") count", 'on'=>"works.id = count.work_id", 'type'=>'left');
        
        $work_opt['limit_opt']=$limit_opt;
        $work_field = array('works.id as work_id','user_id','title','comment','moddate','regdate',
							'note_count','hit_count','comment_count',
							'noted_me_count','hit_me_count','collect_me_count');
		 
        $work_list = $this->work_db->_get_list($work_query, $work_field, array($page, $limit), $work_order, $work_opt);
        //var_export($this->db->last_query());
        
        $work_opt['group']='no_group';
		$work_field = array('count(works.id) as total_count','ceil(count(works.id)/'.($limit+$limit_opt).') as total_page');
		$work_count = $this->work_db->_get_list($work_query, $work_field, array(1, $limit), $work_order, $work_opt);
        //var_export($this->db->last_query());
        
        $output=array();
        foreach($work_list as $k => $v) {
            //var_export($v);
            
            $this->load->model('oldmodel/auth_model');
            $user_array = $this->auth_model->get_user_info($v['user_id']);
           
		   /* 
            $this->load->model('oldmodel/comment_db');
            $comment = $this->comment_db->_get_list("work", array("work_id"=>$v['work_id']), array('count(*) as count'), array());
            $this->load->model('oldmodel/work_db');
            $collected = $this->work_db->_get_collect_list(array("user_id"=>$this->tank_auth->get_user_id(), "work_id"=>$v['work_id']), array('count(*) as count'), array());
            $this->load->model('oldmodel/log_db');
            $note = $this->log_db->_get_list("work", array("work_id"=>$v['work_id'], 'type'=>'N'), array('count(*) as count'), array());
            $hit = $this->log_db->_get_list("work", array("work_id"=>$v['work_id'], 'type'=>'V'), array('count(*) as count'), array());
        	*/
            
            $output[$k] = array(
                "work_id" => $v['work_id'],
                "thumbnail_url" => "/thumbnails/".$v['work_id'],
                "title" => $v['title'],
                "categories" => $this->work_db->_get_category_list(array('work_id'=>$v['work_id']),array('category')),              
                "user" => $user_array,
                "note_count" => isset($v['note_count'])?$v['note_count']:0,
                "hit_count" => isset($v['hit_count'])?$v['hit_count']:0,
                "comment_count" => isset($v['comment_count'])?$v['comment_count']:0,
                "collected" => (isset($v['collect_me_count'])&&$v['collect_me_count']>0)?TRUE:FALSE,
                "collected_comment" => isset($v['comment'])?$v['comment']:'',
                "ins_time" => isset($v['moddate'])?strtotime($v['moddate'])+$this->config->item('timezone_calc'):01293882332
            );
        }
			
		$output['pagenation'] =  array( "total_page" => isset($work_count[0]['total_page'])?$work_count[0]['total_page']:0,
										"total_count"=> isset($work_count[0]['total_count'])?$work_count[0]['total_count']:0,
										"now_page"	 => isset($page)?$page:1,
										);
	    
        return $output;
        
		// 완료되면 아래 변수 선언부만 주석처리
		$dev = TRUE;
		if(isset($dev)){
			$tmp = array(
				"work_id" => 2,
				"thumbnail_url" => "/images/work_thumbnail",
				"title" => "끝내주는 처녀작",
				"categories" => array(
					"fine_art",
					"digital_art"
				),				
				"user" => array(
					"user_id" => 2398,
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
				"collected_comment" => "",
				"ins_time" => 1293882332
			);
			$result = array();
			for($i=0;$i<$limit;$i++){ // 리스트 반환을 위해 dummy 생성
				$tmp['work_id'] = $i+201;
				$tmp['collected_comment'] = str_pad('', rand(0, 200), '0 ', STR_PAD_RIGHT);
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
	
	
	
	function post_collection($work_id='', $collected_comment='')
	{
	    if ($work_id=='') return FALSE;
        
        $result = $this->work_db->_insert_collect(array('user_id'=>$this->tank_auth->get_user_id(), 'work_id'=>$work_id, 'comment'=>$collected_comment));
        
        if($result) return $this->db->insert_id();
        else return FALSE;
        
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




	function put_collection($work_id='', $collected_comment='')
    {
        if ($work_id=='') return FALSE;
        
        $result = $this->work_db->_update_collect(array('where_user_id'=>$this->tank_auth->get_user_id(), 'where_work_id'=>$work_id, 'comment'=>$collected_comment));
        
        if($result) return TRUE;
        else return FALSE;
        
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
	
	


	function delete_collection($work_id='')
	{
        if ($work_id=='') return FALSE;
        
        $result = $this->work_db->_delete_collect(array('user_id'=>$this->tank_auth->get_user_id(), 'work_id'=>$work_id));
        
        if($result) return TRUE;
        else return FALSE;
        
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
