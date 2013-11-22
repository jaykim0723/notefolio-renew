<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Work_model extends CI_Model
{

	function __construct()
	{
		parent::__construct();
		$this->load->database();

		$ci =& get_instance();
        
        $this->load->model('oldmodel/auth_model');
        $this->load->model('oldmodel/work_db');
    }


    function post_work_view($work_id='')
    {
        if ($work_id=='') return FALSE;
        
        $this->load->model('oldmodel/log_db');
        $result = $this->log_db->_insert('work', array('type'=>'V', 'user_id' =>$this->tank_auth->get_user_id(), 'work_id'=>$work_id));
        
        if($result) return TRUE;
        else return FALSE;
        
        // 완료되면 아래 변수 선언부만 주석처리
        $dev = TRUE;
        if(isset($dev)){
            // 해당 유저에 대하여 note처리를 하고,
            // 최종 noted에 대한 값을 리턴한다.
            return rand(0,1)==1 ? TRUE : FALSE;
        }
        
        /* your real code  */       
    }


    function get_my_note($work_id='')
    {
        if ($work_id=='') return FALSE;
        
        $this->load->model('oldmodel/log_db');
        $result = $this->log_db->_get_list('work', array('type'=>'N', 'user_id'=>$this->tank_auth->get_user_id(), 'work_id'=>$work_id), array('count(`work_id`) as count'));
        
        if(count($result)>0&&$result[0]['count']>0) return TRUE;
        else return FALSE;
        
        // 완료되면 아래 변수 선언부만 주석처리
        $dev = TRUE;
        if(isset($dev)){
            // 해당 유저에 대하여 note처리를 하고,
            // 최종 noted에 대한 값을 리턴한다.
            return rand(0,1)==1 ? TRUE : FALSE;
        }
        
        /* your real code  */       
    }


    function post_note($work_id='')
    {
        if ($work_id=='') return FALSE;
        
        $this->load->model('oldmodel/log_db');
        $result = $this->log_db->_insert('work', array('type'=>'N', 'user_id'=>$this->tank_auth->get_user_id(), 'work_id'=>$work_id));
        
        if($result) return TRUE;
        else return FALSE;
        
        // 완료되면 아래 변수 선언부만 주석처리
        $dev = TRUE;
        if(isset($dev)){
            // 해당 유저에 대하여 note처리를 하고,
            // 최종 noted에 대한 값을 리턴한다.
            return rand(0,1)==1 ? TRUE : FALSE;
        }
        
        /* your real code  */       
    }
    
	function delete_note($work_id='')
    {
        if ($work_id=='') return FALSE;
        
        $this->load->model('oldmodel/log_db');
        $result = $this->log_db->_delete('work', array('type'=>'N', 'user_id'=>$this->tank_auth->get_user_id(), 'work_id'=>$work_id));
        
        if($result) return TRUE;
        else return FALSE;
        
		// 완료되면 아래 변수 선언부만 주석처리
		$dev = TRUE;
		if(isset($dev)){
			// 해당 유저에 대하여 note처리를 하고,
			// 최종 noted에 대한 값을 리턴한다.
			return rand(0,1)==1 ? TRUE : FALSE;
		}
		
		/* your real code  */		
	}

    /*
     * return gallery work list
     * 
     * @param string $categories, string $query, int $user_id, int $page, int $limit, string $sort, string $daysort, int $limit_opt
     * 
     * @return array
     */
	function get_work_list($categories='', $query='', $user_id='', $page=1, $limit=10, $sort='newest', $daysort='total', $limit_opt=0, $opt=array())
    {
        $work_query=array();
        $work_field=array();
        $work_order=array();
        $work_opt=array();
        if ($categories!=''&&count($categories)>0) {
            $category_count = 'category_count';
            $work_order[$category_count]='desc';
            if(!is_array($categories)) $category = explode(',', $categories);
            //SELECT work_id, category, count(`work_id`) as count FROM notefolio.work_categories where category in ('fine_art', 'illustration', 'digital_art') group by work_id order by count desc;
            $category_search_query = $this->work_db->_get_category_list(array('category_in'=>$category), array('work_id', array("group_concat(category separator ',') as category", false), 'count(`work_id`) as '.$category_count), array(), $work_order,
                                                                        array('return_type'=>'compiled_select', 'group'=>'work_id'));
            
            $work_query['category_join'] = array('table'=>"(".$category_search_query.") category", 'on'=>'works.id = category.work_id', 'type'=>'right');
        }
		
		switch($daysort) { //날짜 소팅
            	case "daily":
                    $work_query['regdate >=']=date("Y-m-d H:i:s", strtotime("-1 days"));
                    break;
					
            	case "4days":
                    $work_query['regdate >=']=date("Y-m-d H:i:s", strtotime("-4 days"));
                    break;
					
                case "1week":
                    $work_query['regdate >=']=date("Y-m-d H:i:s", strtotime("-1 week"));
                    break;
					
                case "monthly":
                    $work_query['regdate >=']=date("Y-m-d H:i:s", strtotime("-1 month"));		
                    break;
                    
				case "total":
                    break;
					 
				default:
				break;
         }
		
		switch ($sort) {
			case "newest": 
				$this->load->model('oldmodel/log_db');
				$work_order['regdate']='desc';
				break;
				
			case "noted": 
				$this->load->model('oldmodel/log_db');
				//$noted_query = $this->log_db->_get_list("work", array("user_id"=>$this->tank_auth->get_user_id(), 'type'=>'N'), array('work_id', 'count(`work_id`) as noted_me_count'), array(), array(),  array('return_type'=>'compiled_select', 'group'=>'work_id'));
        		//$work_query['noted_join'] = array('table'=>"(".$noted_query.") noted_me", 'on'=>"works.id = noted_me.work_id", 'type'=>'left');  
				$work_order['note_count']='desc';
				$work_order['regdate']='desc';
				  
				break;				
			
			case "discussed":
				$this->load->model('oldmodel/comment_db');
				//$work_query['comment_join'] = array('table'=>"log_work_comment_count", 'on'=>'works.id = log_work_comment_count.work_id', 'type'=>'left');
				$work_order['comment_count']='desc';
				$work_order['regdate']='desc';
				break;
				
			case "viewed": 
				$this->load->model('oldmodel/log_db');
				//$hit_query = $this->log_db->_get_list("work", array('type'=>'V'), array('work_id', 'count(`work_id`) as hit_count'), array(), array(), array('return_type'=>'compiled_select', 'group'=>'work_id'));
       			//$work_query['hit_join'] = array('table'=>"(".$hit_query.") hit_count", 'on'=>"works.id = hit_count.work_id", 'type'=>'left');
 				$work_order['hit_count']='desc';
				$work_order['regdate']='desc';
       			break;
								
			case "featured":
				
				$work_query['feat_join'] = array('table'=>"log_featured_order", 'on'=>'works.id = log_featured_order.work_id');
				$work_order['feat_date']='desc';
				$work_order['regdate']='desc';
				break;
			
			default: 				
				$this->load->model('oldmodel/log_db');
				$work_order['regdate']='desc';
				break;
				
		} 

		$this->load->model('oldmodel/comment_db');
	    $this->load->model('oldmodel/log_db');

        //-- user
        $user_query = $this->user_db->_get_user(array(),array('id as user_user_id','username'),array(),array(),array("return_type"=>"compiled_select"));
        $work_query['user_join'] =  array('table'=>"(".$user_query.") user", 'on'=>'user.user_user_id = works.user_id', 'type'=>'left');
        $user_profile_query = $this->user_db->_get_user_profile(array(),array('id as user_profile_user_id','realname','moddate as user_moddate'),array(),array(),array("return_type"=>"compiled_select"));
        $work_query['user_profile_join'] =  array('table'=>"(".$user_profile_query.") user_profile", 'on'=>'user_profile.user_profile_user_id = works.user_id', 'type'=>'left');
        //-- info
        if ($this->tank_auth->get_user_id()>0){
            $collect_me_query = $this->work_db->_get_collect_list(array('user_id'=>$this->tank_auth->get_user_id()), array('work_id','count(`work_id`) as collect_me_count'), array(), array(), array('return_type'=>'compiled_select', 'group'=>'work_id'));
            $work_query['collect_join'] = array('table'=>"(".$collect_me_query.") collect_me", 'on'=>"works.id = collect_me.work_id", 'type'=>'left');
            $noted_query = $this->log_db->_get_list("work", array("user_id"=>$this->tank_auth->get_user_id(), 'type'=>'N'), array('work_id', 'count(`work_id`) as noted_me_count'), array(), array(),  array('return_type'=>'compiled_select', 'group'=>'work_id'));
            $work_query['noted_join'] = array('table'=>"(".$noted_query.") noted_me", 'on'=>"works.id = noted_me.work_id", 'type'=>'left');
            $hit_me_query = $this->log_db->_get_list("work", array("user_id"=>$this->tank_auth->get_user_id(), 'type'=>'V'), array('work_id', 'count(`work_id`) as hit_me_count'), array(), array(), array('return_type'=>'compiled_select', 'group'=>'work_id'));
            $work_query['hit_me_join'] = array('table'=>"(".$hit_me_query.") hit_me_count", 'on'=>"works.id = hit_me_count.work_id", 'type'=>'left');
        }
        $count_query = $this->work_db->_get_count_list(array(),array('work_id','comment_cnt as comment_count', 'hit_cnt as hit_count', 'note_cnt as note_count'), array(), array(), array('return_type'=>'compiled_select'));
		$work_query['count_join'] = array('table'=>"(".$count_query.") count", 'on'=>"works.id = count.work_id", 'type'=>'left');
        
      	
        if ($user_id!='') {
            $work_query['user_id'] = $user_id;
        }
        if ($query!='') {
            $work_query['search']=preg_split('/[,\s\.]/', $query, -1, PREG_SPLIT_NO_EMPTY);
            
            $content_text_query = $this->work_db->_get_content("text", array(), array('work_id',array("group_concat(content separator ' ') as work_content", false)), array('return_type'=>'compiled_select', 'group'=>'work_id'));
            $work_query['content_text_join'] = array('table'=>"(".$content_text_query.") content_text", 'on'=>"works.id = content_text.work_id", 'type'=>'left');
            
            $tag_query = $this->work_db->_get_tag_list(array(), array('work_id', array("group_concat(text separator ',') as work_tag", false)), array(), array('id'=>'asc'), array('return_type'=>'compiled_select', 'group'=>'work_id'));
            $work_query['tag_join'] = array('table'=>"(".$tag_query.") tag", 'on'=>"works.id = tag.work_id", 'type'=>'left');
			
			$user_search_query = $this->user_db->_get_user_profile(array(), array('user_profiles.user_id as user_q_id','user_profiles.realname as work_user'), array(), array('user_q_id'=>'asc'), array('return_type'=>'compiled_select', 'group'=>'user_q_id'));
            $work_query['user_search_join'] = array('table'=>"(".$user_search_query.") user_search", 'on'=>"works.user_id = user_search.user_q_id", 'type'=>'left');
			
        }
        if(isset($opt['id_in'])) {
            $work_query['id_in']=$opt['id_in'];
        }
        if(isset($opt['id_not_in'])) {
            $work_query['id_not_in']=$opt['id_not_in'];
        }
		
		//원래이거였음
		
       /* if(isset($opt['data_age'])) {
            switch($opt['data_age']) {
                case "1week":
                    $work_query['regdate >=']=date("Y-m-d H:i:s", strtotime("-1 week"));
                    break;
                case "4days":
                    $work_query['regdate >=']=date("Y-m-d H:i:s", strtotime("-4 days"));
                    break;
            }
        }*/
        
        $work_opt['limit_opt']=$limit_opt;
		$work_field = array('id','user_id', 'title','moddate','regdate',
                            'username','realname','user_moddate',
							'note_count','hit_count','comment_count');
        if ($this->tank_auth->get_user_id()>0){
            $work_field = array_merge($work_field, array('noted_me_count','hit_me_count','collect_me_count'));
		}
        
        $work_list = $this->work_db->_get_list($work_query, $work_field, array($page, $limit), $work_order, $work_opt);
        //var_export($this->db->last_query());
        
        $work_opt['group']='no_group';
		$work_field = array('count(id) as total_count','ceil(count(id)/'.($limit+$limit_opt).') as total_page');
        unset(  $work_query['user_join'],
                $work_query['user_profile_join'],
                $work_query['collect_join'],
                $work_query['noted_join'],
                $work_query['hit_me_join']    );
		$work_count = $this->work_db->_get_list($work_query, $work_field, array(1, $limit), $work_order, $work_opt);
        //var_export($this->db->last_query());
        
        $result = array();
        for($i=0;$i<count($work_list);$i++){ // 리스트 반환을 위해 dummy 생성
            
            $result[$i] = array(
                "work_id" => isset($work_list[$i]['id'])?$work_list[$i]['id']:0,
                "thumbnail_url" => "/thumbnails/".(isset($work_list[$i]['id'])?$work_list[$i]['id']:0)."?t=".(isset($work_data[$i]['moddate'])?strtotime($work_data[$i]['moddate'])+9*90*60:time()),
                "title" => isset($work_list[$i]['title'])?$work_list[$i]['title']:'',
                "categories" => isset($work_list[$i]['id'])?$this->work_db->_get_category_list(array('work_id'=>$work_list[$i]['id']),array('category')):array(),              
                "user" => array(
                    "user_id" => isset($work_list[$i]['user_id'])?$work_list[$i]['user_id']:0,
                    "realname" => isset($work_list[$i]['realname'])?$work_list[$i]['realname']:'',
                    "username" => isset($work_list[$i]['username'])?$work_list[$i]['username']:'',
                    "profile_image" => "/profiles/".(isset($work_list[$i]['user_id'])?$work_list[$i]['user_id']:'')."?h=".(isset($work_list[$i]['user_moddate'])?strtotime($work_list[$i]['user_moddate'])+200+$this->config->item('timezone_calc'):time()),
                ),
                "note_count" => isset($work_list[$i]['note_count'])?$work_list[$i]['note_count']:0,
                "hit_count" => isset($work_list[$i]['hit_count'])?$work_list[$i]['hit_count']:0,
                "is_read" => (isset($work_list[$i]['hit_me_count'])&&($work_list[$i]['hit_me_count']>0))?TRUE:FALSE,
                "comment_count" => isset($work_list[$i]['comment_count'])?$work_list[$i]['comment_count']:0,
                "log_featured_order" => isset($work_data['log_featured_order'])?$work_data['log_featured_order']:0,
                "noted" => (isset($work_list[$i]['noted_me_count'])&&$work_list[$i]['noted_me_count']>0)?TRUE:FALSE,
                "collected" => (isset($work_list[$i]['collect_me_count'])&&$work_list[$i]['collect_me_count']>0)?TRUE:FALSE,
                "ins_time" => isset($work_data[$i]['moddate'])?strtotime($work_data[$i]['moddate'])+$this->config->item('timezone_calc'):0,
            );
        
            if (!is_file($this->input->server('DOCUMENT_ROOT').'/thumbnails/'.(isset($work_list[$i]['id'])?$work_list[$i]['id']:0)))
                $result[$i]['thumbnail_url'] = '/images/work_thumbnail';
            
            if (!is_file($this->input->server('DOCUMENT_ROOT').'/profiles/'.(isset($work_list[$i]['user_id'])?$work_list[$i]['user_id']:0)))
                $result[$i]['user']['profile_image'] = '/images/profile_img';
        }
			
		$result['pagenation'] =  array( "total_page" => isset($work_count[0]['total_page'])?$work_count[0]['total_page']:0,
 										"total_count"=> isset($work_count[0]['total_count'])?$work_count[0]['total_count']:0,
 										"now_page"	 => isset($page)?$page:1,
										"per_page" => 12
										);

        return $result;
		// 완료되면 아래 변수 선언부만 주석처리
		$dev = TRUE;
		if(isset($dev)){
			$tmp = array(
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
				"noted" => FALSE,
				"collected" => FALSE,
				"ins_time" => 1293882332
			);
			$result = array();
			for($i=0;$i<$limit;$i++){ // 리스트 반환을 위해 dummy 생성
				$tmp['work_id'] = $i+201;
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
     * get work gallery
     * 
     * @param int $work_id
     * 
     * @return array
     */
	function get_work($work_id='')
	{
        $work_query=array('id'=>$work_id);
        $work_field=array();
        $work_order=array();
		
		
        $this->load->model('oldmodel/log_db');
        if($this->tank_auth->get_user_id()>0){
            $collect_me_query = $this->work_db->_get_collect_list(array('user_id'=>$this->tank_auth->get_user_id()), array('work_id','count(`work_id`) as collect_me_count','comment as collect_me_comment'), array(), array(), array('return_type'=>'compiled_select', 'group'=>'work_id'));
            $work_query['collect_join'] = array('table'=>"(".$collect_me_query.") collect_me", 'on'=>"works.id = collect_me.work_id", 'type'=>'left');
            $noted_query = $this->log_db->_get_list("work", array("user_id"=>$this->tank_auth->get_user_id(), 'type'=>'N'), array('work_id', 'count(`work_id`) as noted_me_count'), array(), array(),  array('return_type'=>'compiled_select', 'group'=>'work_id'));
            $work_query['noted_join'] = array('table'=>"(".$noted_query.") noted_me", 'on'=>"works.id = noted_me.work_id", 'type'=>'left');
        }
		$count_query = $this->work_db->_get_count_list(array(),array('work_id','comment_cnt as comment_count', 'hit_cnt as hit_count', 'note_cnt as note_count'), array(), array(), array('return_type'=>'compiled_select'));
		$work_query['count_join'] = array('table'=>"(".$count_query.") count", 'on'=>"works.id = count.work_id", 'type'=>'left');
        
        if($this->tank_auth->get_user_id()>0){
            $hit_me_search_query = array("user_id"=>$this->tank_auth->get_user_id(), 'type'=>'V', 'regdate >='=>date('Y-m-d H:i:s', strtotime('-1 day')));
        } else {
            $hit_me_search_query = array("remote_addr"=>$this->input->server('REMOTE_ADDR'), 'type'=>'V', 'regdate >='=>date('Y-m-d H:i:s', strtotime('-1 day')));
        }
        
        if($this->tank_auth->get_user_id()>0){
            $hit_me_query = $this->log_db->_get_list("work", $hit_me_search_query, array('work_id', 'count(`work_id`) as hit_me_count'), array(), array(), array('return_type'=>'compiled_select', 'group'=>'work_id'));
            $work_query['hit_me_join'] = array('table'=>"(".$hit_me_query.") hit_me_count", 'on'=>"works.id = hit_me_count.work_id", 'type'=>'left');
        }
        
    	
	    $work_data = $this->work_db->_get_list($work_query);
        if($work_data==array()) return array();
        $work_data = $work_data[0];
        
        $ccl = array();
        foreach(explode("|", $work_data['license']) as $k=>$v){
            switch($k){
                case 0:
                    $ccl['display'] = $v;
                    break;
                case 1:
                    $ccl['commercial'] = $v;
                    break;
                case 2:
                    $ccl['modify'] = $v;
                    break;
                break;
                
                
            }
        }
        
        $user_array = $this->auth_model->get_user_info($work_data['user_id']);
        $recent_work_list = $this->work_db->_get_list(array('user_id'=>$work_data['user_id'], 'id_not_in'=>$work_id), array('id as work_id', 'moddate'), array(1,2));
        for($j=0;$j<count($recent_work_list);$j++){
		    if (is_file($this->input->server('DOCUMENT_ROOT').'/thumbnails/'.(isset($recent_work_list[$j]['work_id'])?$recent_work_list[$j]['work_id']:0)))
                $recent_work_list[$j]['thumbnail_url'] = "/thumbnails/"
                										.(isset($recent_work_list[$j]['work_id'])?$recent_work_list[$j]['work_id']:0)
                										."?t="
                										.(isset($recent_work_list[$j]['moddate'])?strtotime($recent_work_list[$j]['moddate']):time());
			else $recent_work_list[$j]['thumbnail_url'] = '/images/work_thumbnail';
		}
        $user_array['recent_works'] = $recent_work_list;
        $this->load->model('oldmodel/comment_db');
        
	    $output = array(
                "work_id" => $work_id,
                "thumbnail_url" => "/thumbnails/$work_id?t=".(isset($work_data['moddate'])?strtotime($work_data['moddate']):time()),
                "title" => isset($work_data['title'])?$work_data['title']:'',
                "categories" =>  $this->work_db->_get_category_list(array('work_id'=>$work_data['id']), array('category')),
                "user" => $user_array,
                "note_count" => isset($work_data['note_count'])?$work_data['note_count']:0,
                "hit_count" => isset($work_data['hit_count'])?$work_data['hit_count']:0,
                "is_read" => (isset($work_data['hit_me_count'])&&($work_data['hit_me_count']>0))?TRUE:FALSE,
                "comment_count" => isset($work_data['comment_count'])?$work_data['comment_count']:0,
                "log_featured_order" => isset($work_data['log_featured_order'])?$work_data['log_featured_order']:0,
                "noted" => (isset($work_data['noted_me_count'])&&$work_data['noted_me_count']>0)?TRUE:FALSE,
                "collected" => (isset($work_data['collect_me_count'])&&$work_data['collect_me_count']>0)?TRUE:FALSE,
                "collected_comment" => isset($work_data['collect_me_comment'])?$work_data['collect_me_comment']:'',
                "ins_time" => isset($work_data['regdate'])?strtotime($work_data['regdate']):0,
                "mod_time" => isset($work_data['moddate'])?strtotime($work_data['moddate'])+$this->config->item('timezone_calc'):0,
                "contents" => $this->_get_content_array($work_data['content']),
                "ccl" => $ccl,
            );
        if(MY_ID!=3){
            $output = array_merge($output, array(
                "tags" => $this->work_db->_get_tag_list(array('work_id'=>$work_data['id']), array('text')),
                "coworkers" => $this->work_db->_get_coworker_list(array('work_id'=>$work_data['id']),
                                  array("user_id","username","email","realname"),array(),array('id'=>'asc'),array('data_with'=>'user_realinfo')),
                ));
        }
        
        if (!is_file($this->input->server('DOCUMENT_ROOT').'/thumbnails/'.$work_id))
            $output['thumbnail_url'] = '/images/work_thumbnail';
        
        return $output;
            
		// 완료되면 아래 변수 선언부만 주석처리
		$dev = TRUE;
		if(isset($dev)){
			$tmp = array(
				"work_id" => 23989,
				"thumbnail_url" => "/thumbnails/23989?t=2249",
				"title" => "끝내주는 처녀작",
				"categories" => array(
					"fine_art",
					"digital_art"
				),
				"tags" => "어썸,처녀작",
				"coworkers" => array(
					array(
						"user_id" => 0,
						"username" => "hongs",
						"email" => "example@notefolio.net",
						"realname" => "홍순이"
					),
					array(
						"user_id" => 29377,
						"username" => "gaebal",
						"email" => "api@notefolio.net",
						"realname" => "개발새발자"
					)
				),
				"user" => array(
					"user_id" => 1,
					"realname" => "홍길동",
					"username" => "maxzidell",
					"profile_image" => "/images/profile_img",
					"homepage" => "",
					"twitter_screen_name" => "hong GD",
					"facebook_url" => "",
					"description" => "What are some factors for when a commit becomes too large (non-obvious stuff)? ... What about when your in semi-early stages of development when things are moving .... Commit before it becomes a long list or before you make a code change .... you must be able to commit locally or work in your own branch on the server.",
					"categories" => array(
						"motorcycle",
						"movie"
					),
					"gender" => "f",
					"followed" => TRUE,
					"recent_works" => array(
						array(
							"work_id" => 23989,
							"thumbnail_url" => "/images/work_thumbnail",
							"title" => "끝내주는 처녀작",
							"note_count" => 98,
							"hit_count" => 2938,
							"comment_count" => 12,
							"collected" => TRUE,
							"collected_comment" => "",
							"ins_time" => 1293882332
						),
						array(
							"work_id" => 23989,
							"thumbnail_url" => "/images/work_thumbnail",
							"title" => "끝내주는 처녀작",
							"note_count" => 98,
							"hit_count" => 2938,
							"comment_count" => 12,
							"collected" => TRUE,
							"collected_comment" => "",
							"ins_time" => 1293882332
						)
					)
				),
				"note_count" => 98,
				"hit_count" => 2938,
				"comment_count" => 12,
				"noted" => TRUE,
				"collected" => TRUE,
				"collected_comment" => "",
				"ins_time" => 1293882332,
				"mod_time" => 1293882332,
				"contents" => array(
					array(
						"cont_id" => "29384287268328",
						"type" => "image",
						"content" => "/images/work"
					),
					array(
						"cont_id" => "87229384268328",
						"type" => "text",
						"content" => "꽃이지네, 꽃지네, 갈봄여름없이 꽃이지네..."
					),
					array(
						"cont_id" => "98722384268328",
						"type" => "video",
						"content" => "http://www.youtube.com/embed/OpL0joqJmqY"
					)
				),
				"ccl" => array(
					"display" => "y",
					"commercial" => "n",
					"modify" => 0
				)
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
	
    /*
     * insert work gallery and return gallery work number
     * 
     * @param string $title, string $thumbnail_url, array $contents, array $tags, array $coworkers
     * 
     * @return array
     */
	function post_work(
		$title='',
		$categories=array(),
		$contents=array(),
		$tags=array(),
		$ccl=array(),
		$coworkers=array()
	)
	{
	    $ccl_data = implode('|',$ccl);
        
        if ($work_return = $this->work_db->_insert(array(
                                                          'user_id'=>$this->tank_auth->get_user_id(), 
                                                          'title'=>$title,
                                                          'license'=>$ccl_data))
           )
        {
            $work_id = $this->db->insert_id();
        }
        else{
            return 0;
        }
        
        if(!$this->_change_category($work_id, $categories)) {
            return 0;
        }
        
        if(!$this->_change_tag($work_id, $tags)) {
            return 0;
        }
        
        if(!$this->_proc_coworker($work_id, $coworkers)) {
            return 0;
        }
        
        if($contents!=array()){
            $content_data = $this->_proc_content($work_id, $contents);
        }
        
        if($this->work_db->_update($work_id, array('content'=>$content_data)))
            return $work_id;
        else return 0;
        
        
        /*
		// 완료되면 아래 변수 선언부만 주석처리
		$dev = TRUE;
		if(isset($dev)){
			return rand(0,4)==4 ? FALSE : 23989;
		}
         * 
         */


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
		
		/*
         * INSERT INTO `notefolio`.`works`
         * (`id`,
         * `user_id`,
         * `title`,
         * `content`,
         * `license`,
         * `regdate`,
         * `moddate`)
         * VALUES
         * (
         * <{id: }>,
         * <{user_id: }>,
         * <{title: }>,
         * <{content: }>,
         * <{license: }>,
         * <{regdate: 0000-00-00 00:00:00}>,
         * <{moddate: CURRENT_TIMESTAMP}>
         * );
		*/
        
	}


    
    /*
     * update work gallery and return gallery work number
     * 
     * @param int $work_id, string $title, string $thumbnail_url, array $contents, array $tags, array $coworkers
     * 
     * @return array
     */
	function put_work(
		$work_id='',
		$title='',
		$categories=array(),
		$contents=array(),
		$tags=array(),
		$ccl=array(),
		$coworkers=array()
	)
	{
        if ($work_id=='') return FALSE;
        
        $ccl_data = implode('|',$ccl);
        
        if(!$this->_change_category($work_id, $categories)) {
            return FALSE;
        }
        
        if(!$this->_change_tag($work_id, $tags)) {
            return FALSE;
        }
        
        if(!$this->_proc_coworker($work_id, $coworkers)) {
            return FALSE;
        }
        
        if($contents!=array()){
            $content_data = $this->_proc_content($work_id, $contents);
        }
        
        if($this->work_db->_update($work_id, array('title'=>$title, 'content'=>$content_data, 'license'=>$ccl_data)))
            return $work_id;
        else return FALSE;
        
        
        //--- end of func
        
		// 완료되면 아래 변수 선언부만 주석처리
		$dev = TRUE;
		if(isset($dev)){
			return rand(0,4)==4 ? FALSE : 23989;
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

		
        
        $this->db->set('content', $content_data); 
            
        $this->db->where('id', $work_id);
        $return = $this->db->update('works'); 
        
        if (isset($work_id)&&$return) {
            return $work_id;
        }
        
        return 0;
	}
	
    /*
     * delete work gallery and return TRUE/FALSE
     * 
     * @param int $work_id
     * 
     * @return bool
     */
	function delete_work(
		$work_id=''
	)
	{
        if ($work_id=='') return FALSE;
        
        if(!$this->work_db->_delete_category($work_id)) {
            return FALSE;
        }
        
        if(!$this->work_db->_delete_tag($work_id)) {
            return FALSE;
        }
        
        if(!$this->work_db->_delete_coworker($work_id)) {
            return FALSE;
        }
        
        $content_list = $this->work_db->_get_list(array('id'=>$work_id), array('content'));$org_list = array();
        $del_list=array();
        if($content_list!=array()) {
            $content_list = explode(';',$content_list[0]['content']);
            
            for($i=0;$i<count($content_list);$i++){
                if ($content_list[$i]!='') {
                    $temp_data = explode(':',$content_list[$i]);
                    $del_list[$i] = array('type' => $temp_data[0], 'id' => $temp_data[1]);
                    unset($temp_data);
                }
            }
            unset($org_content, $i);
        }
        if($del_list!=array()) $this->_proc_content('0', $del_list);
        
        $this->load->model("oldmodel/collection_model");
        $this->collection_model->delete_collection($work_id);
        
        if($this->work_db->_delete($work_id))
            return TRUE;
        else return FALSE;
        
        
        //--- end of func
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
    
    /*
     * @brief change category
     * 
     * @param int $work_id, array $category_list
     * @return bool
     */
    function _change_category($work_id=0, $category_list=array()){
        if ($work_id==0||$category_list==array()){ // no given
            return FALSE;
        }
        
        $org_list = $this->work_db->_get_category_list(array('work_id'=>$work_id),array('category'));
        
        $del_list = array_diff($org_list, $category_list);
        $add_list = array_diff($category_list, $org_list);
        
        if ($del_list!=array()) {
            foreach($del_list as $v) {
                if(!$this->work_db->_delete_category($work_id, array('category'=>$v))){
                    return FALSE;
                }
            }
        }
        if ($add_list!=array()) {
            foreach($add_list as $v) {
                if(!$this->work_db->_insert_category($work_id, array('category'=>$v))){
                    return FALSE;
                }
            }
        }
        
        return TRUE;
    }
    
    /*
     * @brief change tag
     * 
     * @param int $work_id, array $tag_list
     * @return bool
     */
    function _change_tag($work_id=0, $tag_list=array()){
        if ($work_id==0||$tag_list==array()){ // no given
            return FALSE;
        }
        
        $org_list = $this->work_db->_get_tag_list(array('work_id'=>$work_id),array('text'));
        
        $del_list = array_diff($org_list, $tag_list);
        $add_list = array_diff($tag_list, $org_list);
        
        if ($del_list!=array()) {
            foreach($del_list as $v) {
                if(!$this->work_db->_delete_tag($work_id, array('text'=>$v))){
                    return FALSE;
                }
            }
        }
        if ($add_list!=array()) {
            foreach($add_list as $v) {
                if(!$this->work_db->_insert_tag($work_id, array('text'=>$v))){
                    return FALSE;
                }
            }
        }
        
        return TRUE;
    }
    
    /*
     * @brief process coworker
     * 
     * @param int $work_id, array $coworker_list
     * @return string
     */
    function _proc_coworker($work_id=0, $coworker_list=array()){
        if ($work_id==0||$coworker_list==array()){ // no given
            return '';
        }
        $this->load->model('oldmodel/auth_model');
        
        $org_list = $this->work_db->_get_coworker_list(array('work_id'=>$work_id), array('id','user_id','email','username','realname'));
        
        foreach ($coworker_list as $co_k=>$co_v) {
            $updated = 0;
            if ($co_v['realname']==''&&$co_v['email']=='') continue;
            
            if($org_list!=array()){
                foreach ($org_list as $org_k=>$org_v) {
                   if($co_v['email']==$org_v['email']) {
                        if($co_v['realname']!=$org_v['realname']){
                            $user_info = $this->auth_model->get_user_info('', '', $co_v['email']);
                            
                            $co_v['user_id'] = $user_info['user_id'];
                            if($user_info['username']!='') $co_v['username'] = $user_info['username'];
                            if($user_info['realname']!='') $co_v['realname'] = $user_info['realname'];
                            
                            if(!$this->work_db->_update_coworker($work_id, $co_v)) return FALSE;
                            
                            if ($co_v['user_id']>0) {
                                $this->load->library('activity');
                                $this->activity->post('work', 'add_coworker', array('user_id'=>$this->tank_auth->get_user_id(), 'work_id'=>$work_id, 'coworker_user_id'=>$co_v['user_id']));
                            }
                        }
                        
                        $updated = 1;
                        unset($org_list[$org_k]);
                        break;
                    }
                }
            }
            
            
            if(!$updated) {
                $user_info = $this->auth_model->get_user_info('', '', $co_v['email']);
                if($user_info['user_id']!=$this->tank_auth->get_user_id()) {
                    $co_v['user_id'] = $user_info['user_id'];
                    if($user_info['username']!='') $co_v['username'] = $user_info['username'];
                    if($user_info['realname']!='') $co_v['realname'] = $user_info['realname'];
                            
                    if(!$this->work_db->_insert_coworker($work_id, $co_v)) return FALSE;
                    
                    if ($co_v['user_id']>0) {
                        $this->load->library('activity');
                        $this->activity->post('work', 'add_coworker', array('user_id'=>$this->tank_auth->get_user_id(), 'work_id'=>$work_id, 'coworker_user_id'=>$co_v['user_id']));
                    }
                } else continue;
            }
        }
        
        foreach ($org_list as $co_k => $co_v) {
            if(!$this->work_db->_delete_coworker($work_id, array( 'id' => $co_v['id']) )) return FALSE;
            
        }
        
        return TRUE;
    }
    
    /*
     * @brief process content
     * 
     * @param int $work_id, array $content_list
     * @return srting
     */
    function _proc_content($work_id=0, $content_list=array()){
        if ($work_id==0||$content_list==array()){ // no given
            return '';
        }
        
        $org_content = $this->work_db->_get_list(array('id'=>$work_id), array('content'));
        $org_list = array();
        if(isset($org_content[0])) {
            $org_content = explode(';',$org_content[0]['content']);
            
            for($i=0;$i<count($org_content);$i++){
                if ($org_content[$i]!='') {
                    $temp_data = explode(':',$org_content[$i]);
                    $org_list[$i] = array('type' => $temp_data[0], 'id' => $temp_data[1]);
                    unset($temp_data);
                }
            }
            unset($org_content, $i);
        }
        
        $output = '';        
        foreach ($content_list as $co_k=>$co_v) {
            $updated = 0;
            
            $temp = explode("-", $co_v['cont_id']);
            $co_v['id'] = isset($temp[1])?$temp[1]:0;
            unset($temp);
            
            if($org_list!=array()) {
                foreach ($org_list as $org_k=>$org_v) {
                    if(($co_v['type']==$org_v['type'])&&($co_v['id']==$org_v['id'])) {
                        $param = array('work_id' => $work_id);
                        switch($co_v['type']){
                            case "text":
                            case "video":
                                $param['content'] = $co_v['content'];
                                break;
                            case "image":
                                break;
                        }
                        if(!$this->work_db->_update_content($co_v['type'], $co_v['id'], $param)) return '';
                                            
                        $updated = 1;
                        unset($org_list[$org_k]);
                        $output .= $co_v['type'].':'.$co_v['id'].';';
                        break;
                    }
                }
            }
            
            
            if(!$updated) {
                $param = array('work_id' => $work_id);
                switch($co_v['type']){
                    case "text":
                    case "video":
                        $param['content'] = $co_v['content'];
                        if(!$this->work_db->_insert_content($co_v['type'], $param)) return '';
                        $co_v['id'] = $this->db->insert_id();
                        break;
                    case "image":
                        $img_data = explode('/',$co_v['content']);
                        $co_v['id'] = $img_data[count($img_data)-1];
                        if(!$this->work_db->_update_content($co_v['type'], $co_v['id'], $param)) return '';
                        break;
                }
                $output .= $co_v['type'].':'.$co_v['id'].';';
            }
        }
        
        foreach ($org_list as $co_k => $co_v) {
            if(!$this->work_db->_update_content($co_v['type'], $co_v['id'], array('work_id'=> 0) )) return '';
            
        }
        return $output;
    }

    /*
     * @brief get content by list
     * 
     * @param string $content_list
     * @return array
     */
    function _get_content_array($content_list=''){
        if ($content_list==''){ // no given
            return array();
        }
        
        $content_array = explode(';',$content_list);
		$block_list = array('text'=>array(),
							'image'=>array(),
							'video'=>array(),);
        
        $output=array();
		
		//-- make id list for fast loading
        for($i=0;$i<count($content_array);$i++){
            if ($content_array[$i]!='') {
                $content_param = explode(':',$content_array[$i]);
				array_push($block_list[$content_param[0]], $content_param[1]);
				unset($content_param);
            }
        }
		
		//-- get block by list
        $block_list['text'] = (count($block_list['text'])>0)?
        	$this->work_db->_get_content('text', array('id_in'=>$block_list['text']),array(),array('return_type' => 'data_array_by_data_id')):array();
        $block_list['video'] = (count($block_list['video'])>0)?
        	$this->work_db->_get_content('video', array('id_in'=>$block_list['video']),array(),array('return_type' => 'data_array_by_data_id')):array();
        $block_list['image'] = (count($block_list['image'])>0)?
        	$this->work_db->_get_content('image', array('id_in'=>$block_list['image']),array(),array('return_type' => 'data_array_by_data_id')):array();
        
        for($i=0;$i<count($content_array);$i++){
            if ($content_array[$i]!='') {
                $content_param = explode(':',$content_array[$i]);
				$data = $block_list[$content_param[0]][$content_param[1]];
                switch($content_param[0]){
                    case "text":
                    case "video":
                        $content_data = $data['content'];
                        break;
                    case "image":
                        $content_data = '/img/'.date('ym', strtotime($data['regdate'])).'/'.$data['id'];
                        break;
                }
                $output[$i] = array(
                        "cont_id" => $content_param[0].'-'.$content_param[1],
                        "type" => $content_param[0],
                        "content" => $content_data,
                    );
                unset($content_param);
				unset($data);
            }
        }
        
        return $output;
    }
    
    /*
     * @brief post work model
     * 
     * @param string $work_id, array $content_list
     * @return array
     */
    function post_work_content($type='', $data=array()){
        if ($type==''||$data==array())
            return array();
        
        $result = $this->work_db->_insert_content($type, $data);
        
        $output = array ('id'=>$this->db->insert_id(), 'result'=>$result);
        
        return $output;
    }
	
	/*
     * insert work gallery and return gallery work number
     * 
     * @param string $title, string $thumbnail_url, array $contents, array $tags, array $coworkers
     * 
     * @return array
     */
	function featured_work(
	)
	
	{
        
		$work_return = $this->work_db->_insert(array('featdate'=>now()));
		$work_id = $this->db->insert_id();
		
		/* your real code  */
		
		/*
         * INSERT INTO `notefolio`.`works`
         * (`id`,
		 * 
         * `user_id`,
         * `title`,
         * `content`,
         * `license`,
         * `regdate`,
         * `moddate`)
         * VALUES
         * (
         * <{id: }>,
         * <{user_id: }>,
         * <{title: }>,
         * <{content: }>,
         * <{license: }>,
         * <{regdate: 0000-00-00 00:00:00}>,
         * <{moddate: CURRENT_TIMESTAMP}>
         * );
		*/
		
		return $work_return;
        
	}
	
}
