<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Acp_user_db extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();

        $ci =& get_instance();
        $ci->db =& $this->db; 
    }
    
    /*
     * get admin user db and return array
     * 
     * @param array $query, array $field, array $limit, array $order, array $opt
     * 
     * @return array
     */
    function _get_list ($query=array(), $field=array(), $limit=array(1, 30), $order=array('id'=>'asc'), $opt=array()){
            
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
                case "user_profile_join":
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
        
        $this->db->from('acp_user'); //set table
        
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
        
        $this->db->set('regdate',date("Y-m-d H:i:s"));
        $return = $this->db->insert('acp_user');
        log_message('debug', "Last Query: ".$this->db->last_query());
        $this->db->flush_cache();
        
        return $return;
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
                case "moddate":
                    if ($v)
                        $this->db->set('moddate',$v);
                    else
                        $this->db->set('moddate',date("Y-m-d H:i:s"));
                    break;
                default:
                    $this->db->set($k, $v);
                    break;
            }
        }
        
        
        $this->db->where('id',$id);
        $return = $this->db->update('acp_user');
        log_message('debug', "Last Query: ".$this->db->last_query());
        $this->db->flush_cache();
        
        return $return;
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
        $return = $this->db->delete('acp_user');
        log_message('debug', "Last Query: ".$this->db->last_query());
        $this->db->flush_cache();
        
        return $return;
    }
    
}
