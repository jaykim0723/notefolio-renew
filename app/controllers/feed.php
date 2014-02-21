<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Feed extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('feed_model');
        $this->load->model('alarm_model');
		$this->nf->_member_check(array('update','delete'));
    }
	
	public function index()
	{
		$this->listing(1);
	}
	

	function listing($page=1){
		$feed_list = $this->feed_model->get_list(array(
			'page' => $page,
			'user_id' => USER_ID,
			'type' => 'work'
		));
		// 첫페이지 출력을 위하여 이곳에서 불러들이기
		$feed_list->activity = 	$this->feed_model->get_list(array(
			'page' => $page,
			'user_id' => USER_ID,
			'type' => 'activity'
		));
		// USERNAME insert
		$feed_list->user_id = USER_ID;

		$this->layout->set_view('feed/listing_view', $feed_list)->render();

		//-- mark unread to read
		$this->feed_model->put_readdate(array(
			'user_id' => USER_ID
		));
	}
	function activity_listing($page=1){
		$feed_activity_list = $this->feed_model->get_list(array(
			'page' => $page,
			'user_id' => USER_ID,
			'type' => 'activity'
		));
		// USERNAME insert
		$feed_activity_list->user_id = USER_ID;
		
		$this->layout->set_view('feed/activity_listing_view', $feed_activity_list)->render();

		//-- mark unread to read
		$this->feed_model->put_readdate(array(
			'user_id' => USER_ID
		));
	}

	function check_unread(){
		$data = $this->feed_model->get_unread_count(USER_ID);
		$this->layout->set_json($data)->render();
	}


}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */