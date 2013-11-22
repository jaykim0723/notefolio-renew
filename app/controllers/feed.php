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
		$data = (object)array(
			'page' => $page,
			'rows' => array()
		);
		for($i=0; $i<12; $i++){
			$data->rows[] = array(
				'work_id' => 1,
				'title' => 'Lorem Ipsum',
				'user' => (object)array(
					'realname' => '정미나',
					'hit_cnt' => rand(0,234),
					'comment_cnt' => rand(0,234),
					'like_cnt' => rand(0,234)
				),
				'timestamp' => 1392792372
			);
		}
		$this->layout->set_view('feed/listing_view', $data)->render();
	}


}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */