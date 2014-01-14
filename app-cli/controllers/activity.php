<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @brief Facebook SDK Connect Controller
 *
 * @author Yoon, Seongsu(soplel@snooey.net)
 */
 
class activity extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 */
	public function index()
	{
		echo "Hello {$to}!".PHP_EOL;
	}
    
	/**
	 * @brief post to facebook
	 *
     * @param string $data (querystring)
	 */
    public function post($data){
        parse_str($data);

        $this->load->library('activity');
        
        $this->fbsdk->post_data($user_id, array(
	        	'type'=>$post_type,
	        	'work_uploader'=>$work_uploader,
	        	'work_id'=>$work_id,
	        	'base_url'=>$base_url
        	));
        
        return $this->fbsdk->last_response;
    }
}

/* End of file activity.php */
/* Location: ./application/controllers/activity.php */