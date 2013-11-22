<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {


    public function __construct()
    {
        parent::__construct();
        $this->load->model('work_model');
    }

	
	public function index()
	{
		$this->listing(1);
	}
	
	function listing($page=1){
		$work_list = $this->work_model->get_list(array(
			'page' => $page
		));
		$this->layout->set_view('main/listing_view', $work_list)->render();
	}



}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */