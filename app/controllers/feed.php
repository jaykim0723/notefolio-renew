<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Feed extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('feed_model');
		$this->nf->_member_check(array('update','delete'));
    }
	
	public function index()
	{
		$this->listing(1);
	}
	

	function listing($page=1){
		$feed_list = $this->feed_model->get_list(array(
			'page' => $page,
			'user_id' => USER_ID
		));
		$this->layout->set_view('feed/listing_view', $feed_list)->render();
	}


}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */