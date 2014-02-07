<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Alarm extends CI_Controller {


    public function __construct()
    {
        parent::__construct();
        $this->load->model('alarm_model');
		$this->nf->_member_check(array('update','delete'));
    }
	
	public function index()
	{
		$this->listing(1);
	}
	

	function listing($page=1){
		$alarm_list = $this->alarm_model->get_list(array(
			'page' => $page,
			'user_id' => USER_ID
		));
		$this->layout->set_view('alarm/listing_view', $alarm_list)->render();

		//-- mark unread to read
		$this->alarm_model->put_readdate(array(
			'user_id' => USER_ID
		));
	}


}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */