<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Log_user_active_db extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    /*
     * get log db and return array
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
        $this->db->from('log_user_actives'); //set table
        
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
        
        $return = $this->db->insert('log_user_actives');
        log_message('debug', "Last Query: ".$this->db->last_query());
        $this->db->flush_cache();
        
        return $return;
    }
    
    /*
     * update log and return TRUE/FALSE
     * 
     * @param string $date, array $data
     * 
     * @return bool
     */
     
    function _update($date=''){
        if ($date==''){
            return FALSE;
        }        

        $sql = "UPDATE log_user_actives 
                    set 
                        upload_day = (SELECT 
                                count(distinct user_id)
                            FROM
                                works
                            where
                                date_format(regdate, '%Y-%m-%d') = date),
                        upload_week = (select 
                                count(distinct user_id)
                            from
                                works
                            where
                                regdate between date_add(date, interval '-1' week) and date),
                        upload_month = (select 
                                count(distinct user_id)
                            from
                                works
                            where
                                regdate between date_add(date, interval '-1' month) and date),
                        upload_total = (select 
                                count(distinct user_id)
                            from
                                works
                            where
                                regdate <= date),
                        work_enabled_day = (SELECT 
                                count(distinct user_id)
                            FROM
                                works
                            where
                                works.status = 'enabled' and
                                date_format(moddate, '%Y-%m-%d') = date),
                        work_enabled_week = (select 
                                count(distinct user_id)
                            from
                                works
                            where
                                works.status = 'enabled' and
                                moddate between date_add(date, interval '-1' week) and date),
                        work_enabled_month = (select 
                                count(distinct user_id)
                            from
                                works
                            where
                                works.status = 'enabled' and
                                moddate between date_add(date, interval '-1' month) and date),
                        work_enabled_total = (select 
                                count(distinct user_id)
                            from
                                works
                            where
                                works.status = 'enabled' and
                                moddate <= date),
                        logged_in_day = (SELECT 
                                count(distinct id)
                            FROM
                                users
                            where
                                date_format(last_login, '%Y-%m-%d') = date),
                        logged_in_week = (select 
                                count(distinct id)
                            from
                                users
                            where
                                last_login between date_add(date, interval '-1' week) and date),
                        logged_in_month = (select 
                                count(distinct id)
                            from
                                users
                            where
                                last_login between date_add(date, interval '-1' month) and date),
                        logged_in_month_three = (select 
                                count(distinct id)
                            from
                                users
                            where
                                last_login between date_add(date, interval '-3' month) and date)
                    where
                        date = ?;
        ";
        $return = $this->db->query($sql, array($date));
        log_message('debug', "Last Query: ".$this->db->last_query());
        $this->db->flush_cache();
        
        return $return;
    }
    
    /*
     * delete log and return TRUE/FALSE
     * 
     * @param array $query
     * 
     * @return bool
     */
     
    function _delete($query=array()){
        if ($query==array()){
            return FALSE;
        }        
        
        foreach($query as $k=>$v) {
            switch($k) {
                default:
                    $this->db->where($k, $v);
                    break;
            }
        }
        
        $return = $this->db->delete('log_user_actives');
        log_message('debug', "Last Query: ".$this->db->last_query());
        $this->db->flush_cache();
        
        return $return;
    }
}
