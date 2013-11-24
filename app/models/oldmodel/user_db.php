<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class User_db extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();

        $ci =& get_instance();
		$ci->db =& $this->db; 
    }
    
    /*
     * get user db list and return array
     * 
     * @param array $query, array $field, array $limit, array $order, array $opt
     * 
     * @return array
     */
    function _get_user_list ($query=array(), $field=array(), $limit=array(1, 30), $order=array('id'=>'desc'), $opt=array()){
           
        var_export($query);
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
                case "category_join":
                case "profile_join":
                case "folloing_join":
                case "follower_join":
				case "feat_follow_join":
                case "is_following_join":
                case "fb_join":
                case "upload_works_recent_join":
                    $this->db->join($v['table'], $v['on'], $v['type']);
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
        
        $this->db->from('users'); //set table
        
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
     * get user db and return array
     * 
     * @param array $query, array $field, array $limit, array $order, array $opt
     * 
     * @return array
     */
    function _get_user ($query=array(), $field=array(), $limit=array(1, 1), $order=array('id'=>'desc'), $opt=array()){
           
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
            	
                case "basic_join":
                case "category_join":
                case "profile_join":
                case "folloing_join":
                case "follower_join":
                case "is_following_join":
                case "fb_join":
                    $this->db->join($v['table'], $v['on'], $v['type']);
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
        
        if(isset($opt['group'])) $this->db->group_by($opt['group']);
        
        $this->db->from('users'); //set table
        
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
            foreach ($return->result() as $row) {
                foreach($row as $k =>$v)
                $output[$k] = $v;
            }
        }
        
        return $output;
    }

	/*
     * get user content
     * 
     * @param string $type, array $query, array $field, $array $opt
     * 
     * @return array
     */
    function _get_user_content ($type='', $query=array(), $field=array(), $opt=array())
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
            foreach ($return->result() as $row_k=>$row_v) {
                $output[$row_k] = array();
                foreach ($row_v as $k=>$v)
                    $output[$row_k][$k] = $v;
            }
        }
        
        return $output;
    }
    
    /*
     * update user db and return TRUE/FALSE
     * 
     * @param int $user_id, array $data
     * 
     * @return bool
     */
     
    function _update_user($user_id='', $data=array()){
        if ($user_id==''||$data==array()){
            return FALSE;
        }        
        
        foreach($data as $k=>$v) {
            switch($k) {
                default:
                    $this->db->set($k, $v);
                    break;
            }
        }
        
        $this->db->where('id',$user_id);
        $return = $this->db->update('users');
        log_message('debug', "Last Query: ".$this->db->last_query());
        $this->db->flush_cache();
        
        return $return;
    }
	
	
	
    /*
     * insert follow_user for featured_user and return TRUE/FALSE
     * 
     * @param array $data
     * 
     * @return bool
     */
     
    function _follow_pick($id, $data=array()){
    	
        $data = array(
           'feat_date' => date("Y-m-d H:i:s")
        );
					
  		$this->db->where('user_id',$id);
        $this->db->update('feat_follow', $data); 
		
    }
    
    /*
     * delete follow_user for featured_user and return TRUE/FALSE
     * 
     * @param array $data
     * 
     * @return bool
     */
     
    function _follow_delete($id, $data=array()){
    	
        $data = array(
           'feat_date' => date("0000-00-00 00:00:00")
        );
					
  		$this->db->where('user_id',$id);
        $this->db->update('feat_follow', $data); 
		
    }
    
    
    /*
     * get user profile list and return array
     * 
     * @param array $query, array $field, array $limit, array $order, array $opt
     * 
     * @return array
     */
     
    function _get_user_profile_list ($query=array(), $field=array(), $limit=array(1, 30), $order=array('id'=>'desc'), $opt=array()){
        if ( $query==array()
            ||(( !isset($query['id']) || ( isset($query['id'])&&$query['id'] == '') ) 
            && ( !isset($query['user_id']) || ( isset($query['user_id'])&&$query['user_id'] == '') ) ) ){
            return array();
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
        
        if(!isset($opt['limit_opt'])) $opt['limit_opt']=0;
        if($limit!=array())$this->db->limit($limit[1],($limit[0]-1)*($limit[1]+$opt['limit_opt']));
        
        if(isset($opt['join'])) {
            foreach ($opt['join'] as $join){
                $this->db->join($join['table'], $join['on'], $join['type']);
            }
        }
        
        if(isset($opt['group'])) $this->db->group_by($opt['group']);
        
        $this->db->from('user_profiles'); //set table
        
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
        }
        
        return $output;
    }
    
    /*
     * get user profile and return array
     * 
     * @param array $query, array $field, array $limit, array $order, array $opt
     * 
     * @return array
     */
     
    function _get_user_profile ($query=array(), $field=array(), $limit=array(1, 1), $order=array('id'=>'desc'), $opt=array()){
        if (( $query==array()
            ||(( !isset($query['id']) || ( isset($query['id'])&&$query['id'] == '') ) 
            && ( !isset($query['user_id']) || ( isset($query['user_id'])&&$query['user_id'] == '') ) ) )
            && (isset($opt['return_type'])&&$opt['return_type']!='compiled_select')){
            return array();
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
        
        foreach($query as $k=>$v) {
            switch($k) {
            	case "user_join":
                    $this->db->join($v['table'], $v['on'], $v['type']);
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
        
        if(isset($opt['group'])) $this->db->group_by($opt['group']);
        
        $this->db->from('user_profiles'); //set table
        
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
                foreach ($return->result() as $row) {
                    foreach($row as $k =>$v)
                        $output[$k] = $v;
                }        
                break;
        }
        
        return $output;
    }
    
    /*
     * update user profile and return TRUE/FALSE
     * 
     * @param int $user_id, array $data
     * 
     * @return bool
     */
     
    function _update_user_profile($user_id='', $data=array()){
        if ($user_id==''||$data==array()){
            return FALSE;
        }        
        
        foreach($data as $k=>$v) {
            switch($k) {
                default:
                    $this->db->set($k, $v);
                    break;
            }
        }
        
        $this->db->where('user_id',$user_id);
        $return = $this->db->update('user_profiles');
        log_message('debug', "Last Query: ".$this->db->last_query());
        $this->db->flush_cache();
        
        return $return;
    }
    
    /*
     * get user category list and return array
     * 
     * @param array $query, array $field, array $limit, array $order, array $opt
     * 
     * @return array
     */
     
    function _get_user_category_list ($query=array(), $field=array(), $limit=array(1, 3), $order=array('category'=>'asc'), $opt=array()){
        if (( $query==array()
            ||(( !isset($query['user_id']) || ( isset($query['user_id'])&&$query['user_id'] == '') ) ))
            &&( !isset($query['category_in']) )
            && (isset($opt['return_type'])&&$opt['return_type']!='compiled_select')){
            return array();
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
        
        foreach($query as $k=>$v) {
            switch($k) {
                case "category_join":
                    $this->db->join($v['table'], $v['on'], $v['type']);
                    $this->db->group_by('id');
                    break;
                case "category_in":
                    $this->db->where_in('category', $v);
                    break;
                default:
                    $this->db->where($k, $v);
                    break;
            }
        }
        
        if(isset($opt['group'])) $this->db->group_by($opt['group']);
        
        $this->db->from('user_categories'); //set table
        
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
                    $output[$row_k] = $row_v->category;
                }
        }
        
        return $output;
    }
    
    /*
     * insert user category and return TRUE/FALSE
     * 
     * @param int $user_id, array $data
     * 
     * @return bool
     */
     
    function _insert_user_category($user_id='', $data=array()){
        if ($user_id==''||$data==array()){
            return FALSE;
        }        
        
        foreach($data as $k=>$v) {
            switch($k) {
                default:
                    $this->db->set($k, $v);
                    break;
            }
        }
        
        $this->db->set('user_id',$user_id);
        $this->db->set('regdate',date("Y-m-d H:i:s"));
        $return = $this->db->insert('user_categories');
        log_message('debug', "Last Query: ".$this->db->last_query());
        $this->db->flush_cache();
        
        return $return;
    }
    
    /*
     * delete user category and return TRUE/FALSE
     * 
     * @param int $user_id, array $data
     * 
     * @return bool
     */
     
    function _delete_user_category($user_id=0, $data=array()){
        if ($user_id==0||$data==array()){
            return FALSE;
        }        
        
        foreach($data as $k=>$v) {
            switch($k) {
                default:
                    $this->db->where($k, $v);
                    break;
            }
        }
        
        $this->db->where('user_id',$user_id);
        $return = $this->db->delete('user_categories');
        log_message('debug', "Last Query: ".$this->db->last_query());
        $this->db->flush_cache();
        
        return $return;
    }
    
    /*
     * get user follow list and return array
     * 
     * @param array $query, array $field, array $limit, array $order, array $opt
     * 
     * @return array
     */
     
    function _get_user_follow_list ($query=array(), $field=array(), $limit=array(1, 30), $order=array('id'=>'desc'), $opt=array()){
        if (( $query==array()
            ||( !isset($query['follow_id']) && ( !isset($query['follower_id']) ) ) )
            && (isset($opt['return_type'])&&$opt['return_type']!='compiled_select')){
            return array();
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
        
        foreach($query as $k=>$v) {
            switch($k) {
                case "follower_id":
                    $this->db->where($k, $v);
                    //if(!isset($query['follow_id'])) $this->db->where('follow_id !=', $v);
                    break;
                case "follow_id":
                    $this->db->where($k, $v);
                    //if(!isset($query['follower_id'])) $this->db->where('follower_id !=', $v);
                    break;
                default:
                    $this->db->where($k, $v);
                    break;
            }
        }
        
        if(!isset($opt['limit_opt'])) $opt['limit_opt']=0;
        if($limit!=array())$this->db->limit($limit[1],($limit[0]-1)*($limit[1]+$opt['limit_opt']));
        
        if(isset($opt['group'])) $this->db->group_by($opt['group']);
        
        $this->db->from('user_follow'); //set table
        
        $output = array();
        if(!isset($opt['return_type'])) $opt['return_type'] = 'data_array';
        if ($opt['return_type']!='compiled_select') {
            $return = $this->db->get();
            log_message('user_follow', "Last Query: ".$this->db->last_query());
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
                    foreach ($row_v as $k=>$v)
                        $output[$row_k][$k] = $v;
                }
        }
        
        return $output;
    }
    
    /*
     * insert user follow and return TRUE/FALSE
     * 
     * @param int $follow
     * 
     * @return bool
     */
     
    function _insert_user_follow($follow=0, $follower=0){
        if ($follow==0){
            return FALSE;
        }
        if ($follower==0) {
            $follower = $this->tank_auth->get_user_id();
        }
        
        $this->db->set('follow_id',$follow);
        $this->db->set('follower_id',$follower);
        $this->db->set('regdate',date("Y-m-d H:i:s"));
        $return = $this->db->insert('user_follow');
        log_message('debug', "Last Query: ".$this->db->last_query());
        $this->db->flush_cache();
        
        if($return){
            return TRUE;
        }
        return FALSE;
    }
    
    /*
     * delete user follow and return TRUE/FALSE
     * 
     * @param int $follow
     * 
     * @return bool
     */
     
    function _delete_user_follow($follow=0){
        if ($follow==0){
            return FALSE;
        }
        
        $this->db->where('follow_id',$follow);
        $this->db->where('follower_id',$this->tank_auth->get_user_id());
        $return = $this->db->delete('user_follow');
        log_message('debug', "Last Query: ".$this->db->last_query());
        $this->db->flush_cache();
        
        if($return){
            return TRUE;
        }
        return FALSE;
    }
    
    /*
     * get user invite code list and return array
     * 
     * @param array $query, array $field, array $limit, array $order, array $opt
     * 
     * @return array
     */
     
    function _get_invite_code_list ($query=array(), $field=array(), $limit=array(), $order=array('id'=>'desc'), $opt=array()){
        if ( $query==array() ){
            return array();
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
        
        foreach($query as $k=>$v) {
            switch($k) {
                default:
                    $this->db->where($k, $v);
                    break;
            }
        }
        
        if(isset($opt['group'])) $this->db->group_by($opt['group']);
        
        $this->db->from('invite_code'); //set table
        
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
     * update user invite code and return TRUE/FALSE
     * 
     * @param int $follow
     * 
     * @return bool
     */
     
    function _update_invite_code($query=array()){
        if (is_array($query) && sizeof($query)==0){
            return FALSE;
        }
        
        
        foreach($query as $k=>$v) {
            switch($k) {
                case "set_use":
                    $this->db->set('user_id', $query['user_id']);
                    $this->db->set('usedate', date("Y-m-d H:i:s"));
                    break;
                case "user_id":
                    break;
                default:
                    $this->db->where($k, $v);
                    break;
            }
        }
        
        $return = $this->db->update('invite_code');
        log_message('debug', "Last Query: ".$this->db->last_query());
        $this->db->flush_cache();
        
        if($return){
            return TRUE;
        }
        return FALSE;
    }
    
    /*
     * get user facebook data
     * 
     * @param array $query, array $field, array $limit, array $order, array $opt
     * 
     * @return array
     */
    function _get_user_fb ($query=array(), $field=array(), $limit=array(1, 1), $order=array('id'=>'desc'), $opt=array()){
           
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
        
        if(!isset($opt['limit_opt'])) $opt['limit_opt']=0;
        if($limit!=array())$this->db->limit($limit[1],($limit[0]-1)*($limit[1]+$opt['limit_opt']));
        
        if(isset($opt['join'])) {
            foreach ($opt['join'] as $join){
                $this->db->join($join['table'], $join['on'], $join['type']);
            }
        }
        
        if(isset($opt['group'])) $this->db->group_by($opt['group']);
        
        $this->db->from('user_sns_fb'); //set table
        
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
            foreach ($return->result() as $row) {
                foreach($row as $k =>$v)
                $output[$k] = $v;
            }
        }
        
        return $output;
    }

    /*
     * insert user facebook info and return TRUE/FALSE
     * 
     * @param int $user_id, array $data
     * 
     * @return bool
     */
     
    function _insert_user_fb($data=array()){
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
        
        $this->db->set('regdate',date("Y-m-d H:i:s"));
        $return = $this->db->insert('user_sns_fb');
        log_message('debug', "Last Query: ".$this->db->last_query());
        $this->db->flush_cache();
        
        return $return;
    }
    
    /*
     * update user facebook info and return TRUE/FALSE
     * 
     * @param int $user_id, array $data
     * 
     * @return bool
     */
     
    function _update_user_fb($data=array()){
        if ($data==array()){
            return FALSE;
        }        
        
        foreach($data as $k=>$v) {
            switch($k) {
                case "set_access_token":
                    $this->db->set('access_token', $v);
                    break;
                case "set_post_work":
                    $this->db->set('post_work', $v);
                    break;
                case "set_post_comment":
                    $this->db->set('post_comment', $v);
                    break;
                case "set_post_note":
                    $this->db->set('post_note', $v);
                    break;
                default:
                    $this->db->where($k, $v);
                    break;
            }
        }
        
        $return = $this->db->update('user_sns_fb');
        log_message('debug', "Last Query: ".$this->db->last_query());
        $this->db->flush_cache();
        
        return $return;
    }
    
    /*
     * delete user facebook info and return TRUE/FALSE
     * 
     * @param int $user_id, array $data
     * 
     * @return bool
     */
     
    function _delete_user_fb($data=array()){
        if ($data==array()){
            return FALSE;
        }        
        
        foreach($data as $k=>$v) {
            switch($k) {
                default:
                    $this->db->where($k, $v);
                    break;
            }
        }
        
        $return = $this->db->delete('user_sns_fb');
        log_message('debug', "Last Query: ".$this->db->last_query());
        $this->db->flush_cache();
        
        return $return;
    }
    
    /*
     * get user alarm list
     * 
     * @param array $query, array $field, array $limit, array $order, array $opt
     * 
     * @return array
     */
    function _get_user_alarm_list ($query=array(), $field=array(), $limit=array(1, 10), $order=array('id'=>'desc'), $opt=array()){
           
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
                case "log_join":
                case "user_join":
                case "log_user_join":
                    $this->db->join($v['table'], $v['on'], $v['type']);
                    break;
                case "where_query":
                    $this->db->where($v, NULL, FALSE);
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
        
        if(isset($opt['group'])) $this->db->group_by($opt['group']);
        
        $this->db->from('user_alarms'); //set table
        
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
     * insert user alarm and return TRUE/FALSE
     * 
     * @param int $user_id, array $data
     * 
     * @return bool
     */
     
    function _insert_user_alarm($data=array()){
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
        
        $this->db->set('regdate',date("Y-m-d H:i:s"));
        $return = $this->db->insert('user_alarms');
        log_message('debug', "Last Query: ".$this->db->last_query());
        $this->db->flush_cache();
        
        return $return;
    }
    
    /*
     * update user alarm and return TRUE/FALSE
     * 
     * @param int $user_id, array $data
     * 
     * @return bool
     */
     
    function _update_user_alarm($data=array()){
        if ($data==array()){
            return FALSE;
        }        
        
        foreach($data as $k=>$v) {
            switch($k) {
                case "set_readdate":
                    $this->db->set('readdate', $v);
                    break;
                case "where_in":
                    $this->db->where_in('id', $v);
                    break;
                default:
                    $this->db->where($k, $v);
                    break;
            }
        }
        
        $return = $this->db->update('user_alarms');
        log_message('debug', "Last Query: ".$this->db->last_query());
        $this->db->flush_cache();
        
        return $return;
    }
    
    /*
     * delete user alarm and return TRUE/FALSE
     * 
     * @param int $user_id, array $data
     * 
     * @return bool
     */
     
    function _delete_user_alarm($data=array()){
        if ($data==array()){
            return FALSE;
        }        
        
        foreach($data as $k=>$v) {
            switch($k) {
                default:
                    $this->db->where($k, $v);
                    break;
            }
        }
        
        $return = $this->db->delete('user_alarms');
        log_message('debug', "Last Query: ".$this->db->last_query());
        $this->db->flush_cache();
        
        return $return;
    }
    
}
