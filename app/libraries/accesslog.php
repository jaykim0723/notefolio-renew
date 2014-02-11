<?php
/**
 * Notefolio Access Log Management Library
 *
 *
 * @author Yoon, Seongsu(soplel@snooey.net)
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Accesslog {
    
    var $last_error = '';
    //protected $user_data = array();
    
    function __construct($config=null) {
        $this->ci =& get_instance();
        $this->ci->load->library('user_agent');
    }
    
    /*
     * post access log for user.
     * 
     * @param string $memo
     * 
     * @return bool
     */
    function post($memo=null)
    {
        $params = $this->make_param(array('memo'=>null));
        
        $this->ci->db->trans_start();

        foreach($params as $k=>$v) {
            switch($k) {
                default:
                    $this->ci->db->set($k, $v);
                    break;
            }
        }
        
        $this->ci->db->set('remote_addr', $this->ci->input->server('REMOTE_ADDR'));
        $this->ci->db->set('regdate', date("Y-m-d H:i:s"));
        $return = $this->ci->db->insert('log_access');
        log_message('debug', "Last Query: ".$this->ci->db->last_query());
        $this->ci->db->flush_cache();
        
        $data['accesslog_id'] = $this->ci->db->insert_id();
        
        $this->ci->db->trans_complete(); 
        
        return $this->ci->db->trans_status();
    }
    
    /*
     * make access log parameter for user.
     * 
     * @param array $data
     * 
     * @return bool
     */
    private function make_param($data=null)
    {
        $param = array(
            'useragent'   =>  $this->ci->agent->agent_string(),
            'to_access'   =>  '/'                                   //root
                                .$this->ci->uri->uri_string()       //url string
                                .(($this->ci->input->server('QUERY_STRING'))?
                                    '?'.($this->ci->input->server('QUERY_STRING')):''
                                ),                              //querystring
            'is_mobile'   =>  ($this->ci->agent->is_mobile())?'Y':'N',
            'is_robot'    =>  ($this->ci->agent->is_robot()) ?'Y':'N',
            //-- SQL 만들때 기본으로 write
            //'remote_addr'   =>  $this->input->server('REMOTE_ADDR'),
            //'regdate'       =>  date("Y-m-d H:i:s")
        );
        
        if($this->ci->agent->is_referral()){
            $param['referer']  = $this->ci->agent->referrer();
        }

        if($data['memo']){
            $param['memo']      = $data['memo'];
        }

        return $param;
    }
    
}