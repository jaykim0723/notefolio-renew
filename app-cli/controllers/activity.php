<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @brief Activity Write Controller
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
	 * post to activity
	 *
     * @param string $data (querystring)
	 */
    public function post($work, $area, $act, $type, $data){
        parse_str($data);

        $this->load->library('activity');
        $this->load->model('activity_model');

        $params = $this->activity->make_param($work, array(
        	'area' => $area,
        	'act'  => $act,
        	'type' => $type,
        	'data' => $data,

        	));

        $this->activity_model->post($params);

        echo $this->activity->last_response;
        return true;
    }
}

/* End of file activity.php */
/* Location: ./application/controllers/activity.php */