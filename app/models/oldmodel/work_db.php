<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Work_db extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();

        $ci =& get_instance();
		$ci->db =& $this->db; 
    }
    
    /*
     * get work db and return array
     * 
     * @param array $query, array $field, array $limit, array $order, array $opt
     * 
     * @return array
     */
    function _get_list ($query=array(), $field=array(), $limit=array(1, 30), $order=array('id'=>'desc'), $opt=array()){
            
        if ($field!=array()){
            foreach($field as $k=>$v) {
                switch($k) {
                    default:
                        $this->db->select($v);
                        break;
                }
            }
        }
        
        foreach($query as $k=>$v) {
            switch($k) {
                case "feat_join":
                case "category_join":
                case "collect_join":
                case "collect_me_join":
                case "comment_join":
                case "hit_join":
                case "hit_me_join":
                case "note_join":
                case "noted_join":
                case "content_text_join":
                case "tag_join":
				case "user_join":
				case "user_profile_join":
				case "user_search_join":
                case "sort_join":
                case "count_join":	
				case "monthly_avg":
                case "join":
                    $this->db->join($v['table'], $v['on'], $v['type'], $v['having']);
                    break;
                case "id_in":
                    $this->db->where_in('id', $v);
                    break;					
                case "id_not_in":
                    $this->db->where_not_in('id', $v);
                    break;					
                case "search":
                    if(empty($v)||count($v)==0){
                        continue;
                    }
                    $this->db->where('id !=','');
                    $this->db->where('id !=',0);
                    $search_str = '';
                    $order_str = '';
                    foreach ($v as $key=>$val){
                        if($search_str!='') {
                            $search_str = ' or '.$search_str;
                        }
                        if($order_str!='') {
                            $order_str = ', '.$order_str;
                        }
                        $str = "instr(concat_ws(',',`title`,`work_content`,`work_tag`,`work_user`), '".$val."')";
                        $search_str = $str.$search_str;
                        $order_str = $str." asc".$order_str;
                    }
                    $this->db->where('('.$search_str.')', null, false);
                    
                    $this->db->_protect_identifiers = FALSE;
                    $this->db->order_by($order_str, false);
                    $this->db->_protect_identifiers = TRUE;
                    
                    break;
				//acp-통계-하루에 사용됨
				case "where":
                    $this->db->where('regdate >', $v);
                    break;			
				case "where_week": //메인화면 주 나타내기
					$weekly = "regdate > date_add(now(),interval -7 day) ";
					$this->db->where($weekly);
                    break;						
				case "scope":
                    $this->db->where($v);
					$this->db->from('works');
                    break;	
                case "scope_month":
					$scope_month = "regdate BETWEEN '".$v."' AND date_add('".$v."', INTERVAL 30 day)";
					$this->db->where($scope_month);
					break;
				case "scope_week": //결과화면 주
					$scope_week = "regdate BETWEEN '".$v."' AND date_add('".$v."', INTERVAL 7 day)";
					$this->db->where($scope_week);
					break;
				case "scope_day": //결과화면 일
					$scope_day = "regdate BETWEEN '".$v."' AND date_add('".$v."', INTERVAL 1 day)";
					$this->db->where($scope_day);
					break;
                case "curdate":
					$get_date = "regdate > curdate()";
                    $this->db->where($get_date);
                    break;		
                case "having":
					$this->db->having('count(id)>0'); 
					break;
                default:
                    $this->db->where($k, $v);
                    break;
            }
        }
        
        foreach($order as $k=>$v) {
            $this->db->order_by($k, $v);
        }
        
		
        if(!isset($opt['limit_opt'])) $opt['limit_opt']=0;
        if($limit!=array())$this->db->limit($limit[1],($limit[0]-1)*($limit[1]+$opt['limit_opt']));
        
        if(isset($opt['join'])) {
            foreach ($opt['join'] as $join){
                $this->db->join($join['table'], $join['on'], $join['type']);
            }
        }
		
        if(isset($opt['group'])&&$opt['group']=='no_group') {}
        else if(isset($opt['group'])) $this->db->group_by($opt['group']);
        else $this->db->group_by('id');
		
        $this->db->from('works'); //set table
        
        $output = array();
        if(!isset($opt['return_type'])) $opt['return_type'] = 'data_array';
        if ($opt['return_type']!='compiled_select') {
            $return = $this->db->get();
            log_message('debug', "Last Query: ".$this->db->last_query());
        }
        //echo '<!-- '.var_export($this->db->last_query(), true).' -->';
        switch($opt['return_type']){
            case "compiled_select":
                return $this->db->get_compiled_select();
                break; 
                
            case 'last_query':
                return $this->db->last_query();
                
            case 'data_array':
            default:
            foreach ($return->result() as $row_k=>$row_v) {
                $output[$row_k] = array();
                foreach ($row_v as $k=>$v)
                    $output[$row_k][$k] = $v;
            }
        }
        
        return $output;
    }
	
	 /*
     * get featured work db and return array
     * 
     * @param array $query, array $field, array $limit, array $order, array $opt
     * 
     * @return array
     */
    function _get_featured_list ($query=array(), $field=array(), $limit=array(1, 30), $order=array('id'=>'desc'), $opt=array()){
            
        if ($field!=array()){
            foreach($field as $k=>$v) {
                switch($k) {
                    default:
                        $this->db->select($v);
                        break;
                }
            }
        }
        
        foreach($query as $k=>$v) {
            switch($k) {
                case "feat_join":               
                case "join":
                    $this->db->join($v['table'], $v['on'], $v['type'], $v['having']);
                    break;
                case "id_in":
                    $this->db->where_in('id', $v);
                    break;					
                case "id_not_in":
                    $this->db->where_not_in('id', $v);
                    break;					
                case "search":
                    $this->db->where('id !=','');
                    $this->db->where('id !=',0);
                    foreach ($v as $val){  
                        $this->db->or_like('title', $val);
                        $this->db->or_like('work_content', $val);
                        $this->db->or_like('work_tag', $val);
                    }
                    break;
					
                default:
                    $this->db->where($k, $v);
                    break;
            }
        }
        
        foreach($order as $k=>$v) {
            $this->db->order_by($k, $v);
        }
        
		
        if(!isset($opt['limit_opt'])) $opt['limit_opt']=0;
        if($limit!=array())$this->db->limit($limit[1],($limit[0]-1)*($limit[1]+$opt['limit_opt']));
        
        if(isset($opt['join'])) {
            foreach ($opt['join'] as $join){
                $this->db->join($join['table'], $join['on'], $join['type']);
            }
        }
		
        if(isset($opt['group'])&&$opt['group']=='no_group') {}
        else if(isset($opt['group'])) $this->db->group_by($opt['group']);
        else $this->db->group_by('id');
		
        $this->db->from('works'); //set table
        
        $output = array();
        if(!isset($opt['return_type'])) $opt['return_type'] = 'data_array';
        if ($opt['return_type']!='compiled_select') {
            $return = $this->db->get();
            log_message('debug', "Last Query: ".$this->db->last_query());
        }
        
        switch($opt['return_type']){
            case "compiled_select":
                return $this->db->get_compiled_select();
                break; 
                
            case 'last_query':
                return $this->db->last_query();
                
            case 'data_array':
            default:
            foreach ($return->result() as $row_k=>$row_v) {
                $output[$row_k] = array();
                foreach ($row_v as $k=>$v)
                    $output[$row_k][$k] = $v;
            }
        }
        
        return $output;
    }
	
	/*
     * get work db and return array for acp statics
     * 
     * @param array $query, array $field, array $limit, array $order, array $opt
     * 
     * @return array
     */
    function _get_stat_list ($query=array(), $field=array(), $limit=array(1, 30), $order=array('id'=>'desc'), $opt=array()){
            
       if ($field!=array()){
            foreach($field as $k=>$v) {
                switch($k) {
                    default:
                        $this->db->select($v);
                        break;
                }
            }
        }
        
        foreach($query as $k=>$v) {
            switch($k) {
				case "monthly_avg":
                case "join":
                    $this->db->join($v['table'], $v['on'], $v['type'], $v['having']);
                    break;
                case "id_in":
                    $this->db->where_in('id', $v);
                    break;			
				case "where":
                    $this->db->where('regdate >', $v);
                    break;			
				case "where_week": //메인화면 주 나타내기
					$weekly = "regdate > date_add(now(),interval -7 day) ";
					$this->db->where($weekly);
                    break;						
				case "scope":
                    $this->db->where($v);
					$this->db->from('works');
                    break;	
                case "scope_month":
					$scope_month = "regdate BETWEEN '".$v."' AND date_add('".$v."', INTERVAL 30 day)";
					$this->db->where($scope_month);
					break;
				case "scope_week": //결과화면 주
					$scope_week = "regdate BETWEEN '".$v."' AND date_add('".$v."', INTERVAL 7 day)";
					$this->db->where($scope_week);
					break;
				case "scope_day": //결과화면 일
					$scope_day = "regdate BETWEEN '".$v."' AND date_add('".$v."', INTERVAL 1 day)";
					$this->db->where($scope_day);
					break;
                case "curdate":
					$get_date = "regdate > curdate()";
                    $this->db->where($get_date);
                    break;		
                case "id_not_in":
                    $this->db->where_not_in('id', $v);
                    break;					
                case "search":
                    $this->db->where('id !=','');
                    $this->db->where('id !=',0);
                    foreach ($v as $val){  
                        $this->db->or_like('title', $val);
                        $this->db->or_like('work_content', $val);
                        $this->db->or_like('work_tag', $val);
                    }
                    break;
					
                default:
                    $this->db->where($k, $v);
                    break;
            }
        }
        
        foreach($order as $k=>$v) {
            $this->db->order_by($k, $v);
        }
		
        foreach($sum as $k=>$v) {
        	$this->db->select_sum($k,$v);
        }
		
        if(!isset($opt['limit_opt'])) $opt['limit_opt']=0;
        if($limit!=array())$this->db->limit($limit[1],($limit[0]-1)*($limit[1]+$opt['limit_opt']));
        
        if(isset($opt['join'])) {
            foreach ($opt['join'] as $join){
                $this->db->join($join['table'], $join['on'], $join['type']);
            }
        }
		
        if(isset($opt['group'])&&$opt['group']=='no_group') {}
        else if(isset($opt['group'])) $this->db->group_by($opt['group']);
        else $this->db->group_by('id');
		
        //$this->db->from('works'); //set table
		$this->db->from('work_counts');
		
        $output = array();
        if(!isset($opt['return_type'])) $opt['return_type'] = 'data_array';
        if ($opt['return_type']!='compiled_select') {
            $return = $this->db->get();
            log_message('debug', "Last Query: ".$this->db->last_query());
        }
        
        switch($opt['return_type']){
            case "compiled_select":
                return $this->db->get_compiled_select();
                break; 
                
            case 'last_query':
                return $this->db->last_query();
                
            case 'data_array':
            default:
            foreach ($return->result() as $row_k=>$row_v) {
                $output[$row_k] = array();
                foreach ($row_v as $k=>$v)
                    $output[$row_k][$k] = $v;
            }
        }
        
        return $output;
    }
    /*
     * insert work and return TRUE/FALSE
     * 
     * @param array $data
     * 
     * @return bool
     */
     
    function _insert($data=array()){
        if ($data==array()){
            return FALSE;
        }        
        
        foreach($data as $k=>$v) {
            switch($k) {
                default:
                    $this->db->set($k, $v);
                    break;
            }
        }
        
        $this->db->set('user_id', $this->tank_auth->get_user_id());
        $this->db->set('regdate',date("Y-m-d H:i:s"));
        $return = $this->db->insert('works');
        log_message('debug', "Last Query: ".$this->db->last_query());
        $this->db->flush_cache();
        
        return $return;
    }
    
	
    /*
     * insert featdate for featured and return TRUE/FALSE
     * 
     * @param array $data
     * 
     * @return bool
     */
     
    function _insert_featdate($id, $data=array()){
    	
        $data = array(
           'feat_date' => date("Y-m-d H:i:s")
        );
					
  		$this->db->where('work_id',$id);
        $this->db->update('log_featured', $data); 
		
    }
    
	
    /*
     * update work and return TRUE/FALSE
     * 
     * @param int $id, array $data
     * 
     * @return bool
     */
     
    function _update($id=0, $data=array()){
        if ($id==0||$data==array()){
            return FALSE;
        }        
        
        foreach($data as $k=>$v) {
            switch($k) {
                default:
                    $this->db->set($k, $v);
                    break;
            }
        }
        
        
        $this->db->where('id',$id);
        $return = $this->db->update('works');
        log_message('debug', "Last Query: ".$this->db->last_query());
        $this->db->flush_cache();
        
        return $return;
    }
    
    
    /*
     * update featured and return TRUE/FALSE
     * 
     * @param int $id, array $data
     * 
     * @return bool
     */
    function _update_featured($id, $data=array()){ //1.update가 featdate지우는거임 2.acp/featured에서는 delete로 돼있음 
    
        $data = array(
           'feat_date' => '0000-00-00 00:00:00'
        );
					
  		$this->db->where('work_id',$id);
        $this->db->update('log_featured', $data); 
		
    }
	
		
    /*
     * delete work and return TRUE/FALSE
     * 
     * @param int $id, array $data
     * 
     * @return bool
     */
     
    function _delete($id=0){
        if ($id==0){
            return FALSE;
        }
        
        $this->db->where('id',$id);
        $return = $this->db->delete('works');
        log_message('debug', "Last Query: ".$this->db->last_query());
        $this->db->flush_cache();
        
        return $return;
    }
    
	
    /*
     * get work tag db and return array
     * 
     * @param array $query, array $field, array $limit, array $order, array $opt
     * 
     * @return array
     */
    function _get_tag_list ($query=array(), $field=array(), $limit=array(), $order=array('id'=>'asc'), $opt=array()){
            
        if ($field!=array()){
            foreach($field as $k=>$v) {
                switch($k) {
                    default:
                        $this->db->select($v);
                        break;
                }
            }
        }
        
        foreach($query as $k=>$v) {
            switch($k) {
                case "text_in":
                    $this->db->where_in('text', $v);
                    break;
                default:
                    $this->db->where($k, $v);
                    break;
            }
        }
        
        foreach($order as $k=>$v) {
            $this->db->order_by($k, $v);
        }
        
        if ($limit!=array()){
            $this->db->limit($limit[1],($limit[0]-1)*$limit[1]);
        }
        
        
        if(isset($opt['join'])) {
            foreach ($opt['join'] as $join){
                $this->db->join($join['table'], $join['on'], $join['type']);
            }
        }
        
        if(isset($opt['group'])) $this->db->group_by($opt['group']);
        
        $this->db->from('work_tags'); //set table
        
        $output = array();
		
        if(!isset($opt['return_type'])) $opt['return_type'] = 'data_array';
        if ($opt['return_type']!='compiled_select') {
            $return = $this->db->get();
            log_message('debug', "Last Query: ".$this->db->last_query());
        }
        
        switch($opt['return_type']){
            case "compiled_select":
                return $this->db->get_compiled_select();
                break; 
                
            case 'last_query':
                return $this->db->last_query();
                
            case 'data_array':
            default:
            foreach ($return->result() as $row_k=>$row_v) {
                $output[$row_k] = array();
                foreach ($row_v as $k=>$v)
                    $output[$row_k] = $v;
            }
        }
        
        return $output;
    }
    
    /*
     * insert work tag and return TRUE/FALSE
     * 
     * @param int $work_id, array $data
     * 
     * @return bool
     */
     
    function _insert_tag($work_id=0, $data=array()){
        if ($work_id==0||$data==array()){
            return FALSE;
        }        
        
        foreach($data as $k=>$v) {
            switch($k) {
                default:
                    $this->db->set($k, $v);
                    break;
            }
        }
        
        $this->db->set('work_id',$work_id);
        $this->db->set('regdate',date("Y-m-d H:i:s"));
        $return = $this->db->insert('work_tags');
        log_message('debug', "Last Query: ".$this->db->last_query());
        $this->db->flush_cache();
        
        return $return;
    }
    
    /*
     * delete work tag and return TRUE/FALSE
     * 
     * @param int $work_id, array $data
     * 
     * @return bool
     */
     
    function _delete_tag($work_id=0, $data=array()){
        if ($work_id==0){
            return FALSE;
        }        
        
        foreach($data as $k=>$v) {
            switch($k) {
                default:
                    $this->db->where($k, $v);
                    break;
            }
        }
        
        $this->db->where('work_id',$work_id);
        $return = $this->db->delete('work_tags');
        log_message('debug', "Last Query: ".$this->db->last_query());
        $this->db->flush_cache();
        
        return $return;
    }
    
    /*
     * get work category db and return array
     * 
     * @param array $query, array $field, array $limit, array $order, array $opt
     * 
     * @return array
     */
    function _get_category_list ($query=array(), $field=array(), $limit=array(), $order=array('category'=>'asc'), $opt=array()){
            
        if ($field!=array()){
            foreach($field as $k=>$v) {
                switch($k) {
                    default:
                        $this->db->select($v);
                        break;
                }
            }
        }
        
        foreach($query as $k=>$v) {
            switch($k) {
                case "category_in":
                    $this->db->where_in('category', $v);
                    break;
                default:
                    $this->db->where($k, $v);
                    break;
            }
        }
        
        foreach($order as $k=>$v) {
            $this->db->order_by($k, $v);
        }
        
        if ($limit!=array()){
            $this->db->limit($limit[1],($limit[0]-1)*$limit[1]);
        }
        
        
        if(isset($opt['join'])) {
            foreach ($opt['join'] as $join){
                $this->db->join($join['table'], $join['on'], $join['type']);
            }
        }
        
        if(isset($opt['group'])) $this->db->group_by($opt['group']);
        
        $this->db->from('work_categories'); //set table
        
        $output = array();
        if(!isset($opt['return_type'])) $opt['return_type'] = 'data_array';
        if ($opt['return_type']!='compiled_select') {
            $return = $this->db->get();
            log_message('debug', "Last Query: ".$this->db->last_query());
        }
        
        switch($opt['return_type']){
            case "compiled_select":
                return $this->db->get_compiled_select();
                break; 
                
            case 'last_query':
                return $this->db->last_query();
                
            case 'data_array':
            default:
            foreach ($return->result() as $row_k=>$row_v) {
                $output[$row_k] = array();
                foreach ($row_v as $k=>$v)
                    $output[$row_k] = $v;
            }
        }
        
        return $output;
    }
    
    /*
     * insert work category and return TRUE/FALSE
     * 
     * @param int $work_id, array $data
     * 
     * @return bool
     */
     
    function _insert_category($work_id=0, $data=array()){
        if ($data==array()){
            return FALSE;
        }        
        
        foreach($data as $k=>$v) {
            switch($k) {
                default:
                    $this->db->set($k, $v);
                    break;
            }
        }
        
        $this->db->set('work_id',$work_id);
        $this->db->set('regdate',date("Y-m-d H:i:s"));
        $return = $this->db->insert('work_categories');
        log_message('debug', "Last Query: ".$this->db->last_query());
        $this->db->flush_cache();
        
        return $return;
    }
    
    /*
     * delete work category and return TRUE/FALSE
     * 
     * @param int $work_id, array $data
     * 
     * @return bool
     */
     
    function _delete_category($work_id=0, $data=array()){
        if ($work_id==0){
            return FALSE;
        }        
        
        foreach($data as $k=>$v) {
            switch($k) {
                default:
                    $this->db->where($k, $v);
                    break;
            }
        }
        
        $this->db->where('work_id',$work_id);
        $return = $this->db->delete('work_categories');
        log_message('debug', "Last Query: ".$this->db->last_query());
        $this->db->flush_cache();
        
        return $return;
    }
    
    /*
     * get work coworker db and return array
     * 
     * @param array $query, array $field, array $limit, array $order, array $opt
     * 
     * @return array
     */
    function _get_coworker_list ($query=array(), $field=array(), $limit=array(), $order=array('id'=>'asc'), $opt=array()){
            
        if ($field!=array()){
            foreach($field as $k=>$v) {
                switch($k) {
                    default:
                        $this->db->select($v);
                        break;
                }
            }
        }
        
        foreach($query as $k=>$v) {
            switch($k) {
                default:
                    $this->db->where($k, $v);
                    break;
            }
        }
        
        foreach($order as $k=>$v) {
            $this->db->order_by($k, $v);
        }
        
        if($limit!=array()) $this->db->limit($limit[1],($limit[0]-1)*$limit[1]);
        
        if(isset($opt['join'])) {
            foreach ($opt['join'] as $join){
                $this->db->join($join['table'], $join['on'], $join['type']);
            }
        }
        
        if(isset($opt['group'])) $this->db->group_by($opt['group']);
        
        if(isset($opt['data_with'])&&$opt['data_with']=="user_realinfo"){
            $this->db->from('work_coworker_list_with_user_info'); //set table
            
        } else {
            $this->db->from('work_coworker_list'); //set table
        }
        
        $output = array();
        if(!isset($opt['return_type'])) $opt['return_type'] = 'data_array';
        if ($opt['return_type']!='compiled_select') {
            $return = $this->db->get();
            log_message('debug', "Last Query: ".$this->db->last_query());
        }
        
        switch($opt['return_type']){
            case "compiled_select":
                return $this->db->get_compiled_select();
                break; 
                
            case 'last_query':
                return $this->db->last_query();
                
            case 'data_array':
            default:
            foreach ($return->result() as $row_k=>$row_v) {
                $output[$row_k] = array();
                foreach ($row_v as $k=>$v)
                    $output[$row_k][$k] = $v;
            }
        }
        
        return $output;
    }
    
    /*
     * insert work tag and return TRUE/FALSE
     * 
     * @param int $work_id, array $data
     * 
     * @return bool
     */
     
    function _insert_coworker($work_id=0, $data=array()){
        if ($work_id==0||$data==array()){
            return FALSE;
        }        
        
        foreach($data as $k=>$v) {
            switch($k) {
                default:
                    $this->db->set($k, $v);
                    break;
            }
        }
        
        $this->db->set('work_id',$work_id);
        $this->db->set('regdate',date("Y-m-d H:i:s"));
        $return = $this->db->insert('work_coworker_list');
        log_message('debug', "Last Query: ".$this->db->last_query());
        $this->db->flush_cache();
        
        return $return;
    }
    
    /*
     * update work coworker and return TRUE/FALSE
     * 
     * @param int $work_id, array $data
     * 
     * @return bool
     */
     
    function _update_coworker($work_id=0, $data=array()){
        if ($work_id==0||$data==array()){
            return FALSE;
        }        
        
        foreach($data as $k=>$v) {
            switch($k) {
                default:
                    $this->db->set($k, $v);
                    break;
            }
        }
        
        $this->db->where('work_id',$work_id);
        $return = $this->db->update('work_coworker_list');
        log_message('debug', "Last Query: ".$this->db->last_query());
        $this->db->flush_cache();
        
        return $return;
    }
    
    /*
     * delete work coworker and return TRUE/FALSE
     * 
     * @param int $work_id, array $data
     * 
     * @return bool
     */
     
    function _delete_coworker($work_id=0, $data=array()){
        if ($work_id==0){
            return FALSE;
        }        
        
        foreach($data as $k=>$v) {
            switch($k) {
                default:
                    $this->db->where($k, $v);
                    break;
            }
        }
        
        $this->db->where('work_id',$work_id);
        $return = $this->db->delete('work_coworker_list');
        log_message('debug', "Last Query: ".$this->db->last_query());
        $this->db->flush_cache();
        
        return $return;
    }
        
    /*
     * get work gallery content
     * 
     * @param string $type, array $query, array $field, $array $opt
     * 
     * @return array
     */
    function _get_content ($type='', $query=array(), $field=array(), $opt=array())
    {
        if(($type==''||!(isset($query['id'])||isset($query['id_in'])))
          &&(isset($opt['return_type'])&&$opt['return_type']!='compiled_select')) {
            return array();
        }
        
        if ($query!=array()){
            foreach($query as $k=>$v) {
                switch($k) {
	                case "search":
	                    $this->db->where('work_id !=',0);
	                    foreach ($v as $val){
	                        if($val=='') continue;  
	                        $this->db->like('content', $val);
	                    }
	                    break;
	                case "join":
	                    $this->db->join($v['table'], $v['on'], $v['type'], $v['having']);
	                    break;
	                case "id_in":
	                    $this->db->where_in('id', $v);
	                    break;					
	                case "id_not_in":
	                    $this->db->where_not_in('id', $v);
	                    break;			
                    default:
                        $this->db->where($k, $v);
                        break;
                }
            }
        }
        
        if ($field!=array()){
            foreach($field as $k=>$v) {
                switch($k) {	
                    default:
                        $this->db->select($v);
                        break;
                }
            }
        }
        
        if(isset($opt['join'])) {
            foreach ($opt['join'] as $join){
                $this->db->join($join['table'], $join['on'], $join['type']);
            }
        }
        
        if(isset($opt['group'])) $this->db->group_by($opt['group']);
        
        $this->db->from('work_content_'.$type); //set table
        
        $output = array();
        if(!isset($opt['return_type'])) $opt['return_type'] = 'data_array';
        if ($opt['return_type']!='compiled_select') {
            $return = $this->db->get();
            log_message('debug', "Last Query: ".$this->db->last_query());
        }
        
        switch($opt['return_type']){
            case "compiled_select":
                return $this->db->get_compiled_select();
                break; 
                
            case 'last_query':
                return $this->db->last_query();
                break;   
				
            case 'data_array_by_data_id':
            default:
	            foreach ($return->result() as $row_k=>$row_v) {
					$data_id = $row_v->id;
	                $output[$data_id] = array();
	                foreach ($row_v as $k=>$v)
	                    $output[$data_id][$k] = $v;
					unset($data_id);
	            }
                break;    
				
            case 'data_array':
            default:
	            foreach ($return->result() as $row_k=>$row_v) {
	                $output[$row_k] = array();
	                foreach ($row_v as $k=>$v)
	                    $output[$row_k][$k] = $v;
	            }
                break;    
				
            case 'data_single':
            foreach ($return->result() as $row_v) {
                foreach ($row_v as $k=>$v)
                    $output[$k] = $v;
            }
        }
        
        return $output;
    }
        
    /*
     * insert work gallery content
     * 
     * @param string $type, array $data
     * 
     * @return boolian
     */
    function _insert_content ($type='', $data=array())
    {
        if ($type==''||$data==array()) {
            return 0;
        }        
        
        foreach($data as $k=>$v) {
            switch($k) {
                default:
                    $this->db->set($k, $v);
                    break;
            }
        }
        
        $this->db->set('user_id', $this->tank_auth->get_user_id());
        $this->db->set('regdate',date("Y-m-d H:i:s"));
        $return = $this->db->insert('work_content_'.$type);
        log_message('debug', "Last Query: ".$this->db->last_query());
        $this->db->flush_cache();
        
        return $return;
    }
        
    /*
     * update work gallery content
     * 
     * @param string $type, int $id, array $data
     * 
     * @return boolian
     */
    function _update_content ($type='', $id=0, $data=array())
    {
        if ($type==''||$data==array()) {
            return false;
        }       
        
        foreach($data as $k=>$v) {
            switch($k) {
                default:
                    $this->db->set($k, $v);
                    break;
            }
        }
        
        $this->db->where('id',$id);
        $return = $this->db->update('work_content_'.$type);
        log_message('debug', "Last Query: ".$this->db->last_query());
        $this->db->flush_cache();
        
        return $return;
    }
        
    /*
     * delete work gallery content
     * 
     * @param string $type, int $id, array $data
     * 
     * @return boolian
     */
    function _delete_content ($type='', $id=0, $data=array())
    {
        if ($work_id==0||$content_id==0) {
            return false;
        }        
        
        foreach($data as $k=>$v) {
            switch($k) {
                default:
                    $this->db->where($k, $v);
                    break;
            }
        }
        
        $this->db->where('id',$id);
        $return = $this->db->delete('work_content_'.$type);
        log_message('debug', "Last Query: ".$this->db->last_query());
        $this->db->flush_cache();
        
        return $return;
    }
        
    /*
     * get gallery collect list
     * 
     * @param array $query, array $field, array $limit, array $order, array $opt
     * 
     * @return array
     */
    function _get_collect_list ($query=array(), $field=array(), $limit=array(1, 30), $order=array('id'=>'desc'), $opt=array()){
            
        if ($field!=array()){
            foreach($field as $k=>$v) {
                switch($k) {
                    default:
                        $this->db->select($v);
                        break;
                }
            }
        }
        
        foreach($query as $k=>$v) {
            switch($k) {
                default:
                    $this->db->where($k, $v);
                    break;
            }
        }
        
        foreach($order as $k=>$v) {
            $this->db->order_by($k, $v);
        }
        
        if($limit!=array()) $this->db->limit($limit[1],($limit[0]-1)*$limit[1]);
        
        if(isset($opt['join'])) {
            foreach ($opt['join'] as $join){
                $this->db->join($join['table'], $join['on'], $join['type']);
            }
        }
        
        if(isset($opt['group'])) $this->db->group_by($opt['group']);
        
        $this->db->from('user_work_collect'); //set table
        
        $output = array();
        if(!isset($opt['return_type'])) $opt['return_type'] = 'data_array';
        if ($opt['return_type']!='compiled_select') {
            $return = $this->db->get();
            log_message('debug', "Last Query: ".$this->db->last_query());
        }
        
        switch($opt['return_type']){
            case "compiled_select":
                return $this->db->get_compiled_select();
                break; 
                
            case 'last_query':
                return $this->db->last_query();
                
            case 'data_array':
            default:
                foreach ($return->result() as $row_k=>$row_v) {
                    $output[$row_k] = array();
                    foreach ($row_v as $k=>$v)
                        $output[$row_k][$k] = $v;
                }
                break;    
            case 'data_single':
                foreach ($return->result() as $row_k=>$row_v) {
                    $output[$row_k] = array();
                    foreach ($row_v as $k=>$v)
                        $output[0][$k] = $v;
                }
                break;
        }
        
        return $output;
    }
        
    /*
     * insert work gallery collect list
     * 
     * @param array $data
     * 
     * @return boolian
     */
    function _insert_collect ($data=array())
    {
        if ($data==array()) {
            return 0;
        }        
        
        foreach($data as $k=>$v) {
            switch($k) {
                default:
                    $this->db->set($k, $v);
                    break;
            }
        }
        
        $this->db->set('regdate',date("Y-m-d H:i:s"));
        $return = $this->db->insert('user_work_collect');
        log_message('debug', "Last Query: ".$this->db->last_query());
        $this->db->flush_cache();
        
        return $return;
    }
        
    /*
     * update work gallery collect list
     * 
     * @param array $data
     * 
     * @return boolian
     */
    function _update_collect ($data=array())
    {
        if ($type==''||$data==array()) {
            return false;
        }       
        
        foreach($data as $k=>$v) {
            switch($k) {
                default:
                    $this->db->set($k, $v);
                case "where_id":
                    $this->db->where('id', $v);
                case "where_work_id":
                    $this->db->where('work_id', $v);
                    break;
                case "where_user_id":
                    $this->db->where('user_id', $v);
                    break;
            }
        }
        
        $return = $this->db->update('user_work_collect');
        log_message('debug', "Last Query: ".$this->db->last_query());
        $this->db->flush_cache();
        
        return $return;
    }
        
    /*
     * delete work gallery collect list
     * 
     * @param array $query
     * 
     * @return boolian
     */
    function _delete_collect ($query=array())
    {
        if ($query==array()) {
            return false;
        }        
        
        foreach($query as $k=>$v) {
            switch($k) {
                default:
                    $this->db->where($k, $v);
                    break;
            }
        }
        
        $return = $this->db->delete('user_work_collect');
        log_message('debug', "Last Query: ".$this->db->last_query());
        $this->db->flush_cache();
        
        return $return;
    }
    /*
     * get work count db and return array
     * 
     * @param array $query, array $field, array $limit, array $order, array $opt
     * 
     * @return array
     */
    function _get_count_list ($query=array(), $field=array(), $limit=array(), $order=array('work_id'=>'asc'), $opt=array()){
            
        if ($field!=array()){
            foreach($field as $k=>$v) {
                switch($k) {
                    default:
                        $this->db->select($v);
                        break;
                }
            }
        }
        
        foreach($query as $k=>$v) {
            switch($k) {
                case "category_in":
                    $this->db->where_in('category', $v);
                    break;
                default:
                    $this->db->where($k, $v);
                    break;
            }
        }
        
        foreach($order as $k=>$v) {
            $this->db->order_by($k, $v);
        }
        
        if ($limit!=array()){
            $this->db->limit($limit[1],($limit[0]-1)*$limit[1]);
        }
        
        
        if(isset($opt['join'])) {
            foreach ($opt['join'] as $join){
                $this->db->join($join['table'], $join['on'], $join['type']);
            }
        }
        
        if(isset($opt['group'])) $this->db->group_by($opt['group']);
        
        $this->db->from('work_counts'); //set table
        
        $output = array();
        if(!isset($opt['return_type'])) $opt['return_type'] = 'data_array';
        if ($opt['return_type']!='compiled_select') {
            $return = $this->db->get();
            log_message('debug', "Last Query: ".$this->db->last_query());
        }
        
        switch($opt['return_type']){
            case "compiled_select":
                return $this->db->get_compiled_select();
                break; 
                
            case 'last_query':
                return $this->db->last_query();
                
            case 'data_array':
            default:
            foreach ($return->result() as $row_k=>$row_v) {
                $output[$row_k] = array();
                foreach ($row_v as $k=>$v)
                    $output[$row_k] = $v;
            }
        }
        
        return $output;
    }
}
