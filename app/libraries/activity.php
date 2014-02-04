<?php
/**
 * Notefolio Activity Management Library
 *
 *
 * @author Yoon, Seongsu(soplel@snooey.net)
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Activity {
    
    function __construct($config=null) {
        $this->ci =& get_instance();
    }
    
    /**
     * post activity for user.
     * 
     * @param array $params
     * 
     * @return string
     */
    function post($params=array())
    {
        /*
        $params = array(
            'crud' => $crud,
            'area' => $area,
            'type'  => $type,
            'work_id' => $work_id,
            'user_A' => $user_A_id,
            'user_B' => $user_B_id,
            'parent_id' => $parent_id,
            'comment' => $comment,
            );
        */

        $default_cmd = 'php '.$this->ci->input->server('DOCUMENT_ROOT').'../app-cli/cli.php activity';
        $errmsg = 'eAccelerator: Unable to change cache directory /var/cache/eaccelerator permissions';
        
        $data=http_build_query(array(
            'user_A'    =>$params['user_A'],
            'user_B'    =>$params['user_B'],
            'work_id'   =>$params['work_id'],
            'parent_id' =>$params['parent_id'],
            'comment'   =>$params['comment'],
            ));

        $cmd = $default_cmd." ".$params['crud']." ".$params['area']." ".$params['type']." ".$data." > /dev/null &";

        $response = exec($cmd);

        return $response;
    }
    
}