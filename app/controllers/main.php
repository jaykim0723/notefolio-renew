<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	
	public function index()
	{
		$this->listing(1);
	}
	
	function listing($page=1){
		$data = (object)array(
			'page' => $page,
			'rows' => array()
		);
		for($i=0; $i<12; $i++){
			$data->rows[] = array(
				'work_id' => 2,
				'title' => 'Lorem Ipsum',
				'user' => (object)array(
					'realname' => '정미나',
					'hit_cnt' => rand(0,234),
					'comment_cnt' => rand(0,234),
					'like_cnt' => rand(0,234)
				)
			);
		}
		$this->layout->set_view('main/listing_view', $data)->render();
	}



}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */