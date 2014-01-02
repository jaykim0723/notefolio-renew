<?php
/* application/hooks/LogQueryHook.php */
class LogQueryHook {
 
    function log_queries() {    
        $CI =& get_instance();
        $times = $CI->db->query_times;
        $dbs    = array();
        $output = NULL;     
        $queries = $CI->db->queries;
 
        if (count($queries) == 0){
            $output .= "no queries\n";
        }else{
            foreach ($queries as $key=>$query){
                $output .= $query . "\n";
            }
            $took = round(doubleval($times[$key]), 3);
            $output .= "===[took:{$took}]\n\n";
        }
 
        $CI->load->helper('file');
        $log_file = APPPATH . 'logs/';
        if($CI->config->item('log_path')!='')
            $log_file = APPPATH . $CI->config->item('log_path');
        $logs_file .= 'log-'.date('Y-m-d').'.php';

        if ( ! write_file($logs_file, $output, 'a+')){
             log_message('debug','Unable to write query the file');
        }   
    }
}