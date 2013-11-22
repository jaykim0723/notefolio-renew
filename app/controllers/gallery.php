<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Gallery extends CI_Controller {


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
		$this->layout->set_view('gallery/listing_view', $work_list)->render();
	}

	function info($work_id=''){
		$work = $this->work_model->get_info($work_id);
		$this->layout->set_view('gallery/info_view', $work)->render();
	}


	function mod($work_id=''){
		$work = $this->work_model->get_info($work_id);
		
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */