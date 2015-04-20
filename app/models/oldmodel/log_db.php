<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Log_db extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    /*
     * get log db and return array
     * 
     * @param string $type, array $query, array $field, array $limit, array $order, array $opt
     * 
     * @return array
     */
    function _get_list ($type='', $query=array(), $field=array(), $limit=array(1, 30), $order=array('id'=>'desc'), $opt=array()){
            
        if ($type==''||$field!=array()){
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
                case "follow_join":
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
        
        if($limit!=array()) $this->db->limit($limit[1],($limit[0]-1)*$limit[1]);
        
        if(isset($opt['join'])) {
            foreach ($opt['join'] as $join){
                $this->db->join($join['table'], $join['on'], $join['type']);
            }
        }
        
        if(isset($opt['group'])) $this->db->group_by($opt['group']);
		if(isset($opt['having'])) {
			$this->db->having('feat_order >', 1); 
		}
        $this->db->from('log_'.$type); //set table
        
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
     * insert log and return TRUE/FALSE
     * 
     * @param string $type, array $data
     * 
     * @return bool
     */
     
    function _insert($type='', $data=array()){
        if ($type==''||$data==array()){
            return FALSE;
        }        
        
        foreach($data as $k=>$v) {
            switch($k) {
                default:
                    $this->db->set($k, $v);
                    break;
            }
        }
        
        $this->db->set('remote_addr',$this->input->server('REMOTE_ADDR'));
        $this->db->set('regdate',date("Y-m-d H:i:s"));
        $return = $this->db->insert('log_'.$type);
        log_message('debug', "Last Query: ".$this->db->last_query());
        $this->db->flush_cache();
        
        return $return;
    }
    
    /*
     * update log and return TRUE/FALSE
     * 
     * @param string #type, int $id, array $data
     * 
     * @return bool
     */
     
    function _update($type='', $id=0, $data=array()){
        if ($type==''||$id==0||$data==array()){
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
        $return = $this->db->update('log_'.$type);
        log_message('debug', "Last Query: ".$this->db->last_query());
        $this->db->flush_cache();
        
        return $return;
    }
    
    /*
     * delete log and return TRUE/FALSE
     * 
     * @param string $type, array $query
     * 
     * @return bool
     */
     
    function _delete($type='', $query=array()){
        if ($type==''|| $query==array()){
            return FALSE;
        }        
        
        foreach($query as $k=>$v) {
            switch($k) {
                default:
                    $this->db->where($k, $v);
                    break;
            }
        }
        
        $return = $this->db->delete('log_'.$type);
        log_message('debug', "Last Query: ".$this->db->last_query());
        $this->db->flush_cache();
        
        return $return;
    }
}
