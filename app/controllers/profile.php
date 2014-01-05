<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Profile extends CI_Controller {

	public $user_id=0;

    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('profile_model','user_model'));
		$this->nf->_member_check(array('statistics'));
    }

	
	public function index()
	{
		exit('no_index');
	}
	

	



	function myworks($username='', $page=1){
		log_message('debug','--------- gallery ( params : '.print_r(get_defined_vars(),TRUE)).')';
		
		$user = $this->user_model->get_info(array('username'=>$username));
		$this->user_id = $user->row->id;

        $this->load->model('work_model');
		$work_list = $this->work_model->get_list(array(
			'page' => $page,
			'user_id' => $this->user_id
		));
		$work_list->username = $username;
		if(!$this->input->is_ajax_request())
			$this->layout->set_view('profile/header_view', $user->row);
		$this->layout->set_view('profile/myworks_listing_view', $work_list)->render();
	}

	



	function about($username=''){
		log_message('debug','--------- about ( params : '.print_r(get_defined_vars(),TRUE)).')';
		$user = $this->user_model->get_info(array('username'=>$username));
		$this->user_id = $user->row->id;
		
		if(!$this->input->is_ajax_request())
			$this->layout->set_view('profile/header_view', $user->row);
		$this->layout->set_view('profile/about_view')->render();
	}

	



	function collection($username='', $page=1){
		log_message('debug','--------- collection ( params : '.print_r(get_defined_vars(),TRUE)).')';

		$user = $this->user_model->get_info(array('username'=>$username));
		$this->user_id = $user->row->id;

		$collection_list = $this->profile_model->get_collection_list(array(
			'page' => $page,
			'user_id' => $this->user_id
		));
		if(!$this->input->is_ajax_request())
			$this->layout->set_view('profile/header_view', $user->row);
		$this->layout->set_view('profile/collection_listing_view', $collection_list)->render();
	}

	



	function statistics($username='', $page=1){
		log_message('debug','--------- statistics ( params : '.print_r(get_defined_vars(),TRUE)).')';

		$user = $this->user_model->get_info(array('username'=>$username));
		$this->user_id = $user->row->id;

		if(!$this->input->is_ajax_request())
			$this->layout->set_view('profile/header_view', $user->row);
		$this->layout->set_view('profile/statistics_view')->render();
	}

	



	function followings($username='', $page=1){
		log_message('debug','--------- followings ( params : '.print_r(get_defined_vars(),TRUE)).')';

		$user = $this->user_model->get_info(array('username'=>$username));
		$this->user_id = $user->row->id;

		if(!$this->input->is_ajax_request())
			$this->layout->set_view('profile/header_view', $user->row);

		$followings_list = $this->profile_model->get_followings_list(array(
			'page' => $page,
			'user_id' => $this->user_id
		));
		$this->layout->set_view('profile/follow_listing_view', $followings_list)->render();
	}

	function followers($username='', $page=1){
		log_message('debug','--------- followers ( params : '.print_r(get_defined_vars(),TRUE)).')';

		$user = $this->user_model->get_info(array('username'=>$username));
		$this->user_id = $user->row->id;

		if(!$this->input->is_ajax_request())
			$this->layout->set_view('profile/header_view', $user->row);

		$followers_list = $this->profile_model->get_followers_list(array(
			'page' => $page,
			'user_id' => $this->user_id
		));
		$this->layout->set_view('profile/follow_listing_view', $followers_list)->render();
	}






	function follow_action(){
		$user_id = (int)$this->input->post('user_id');
		$follow = $this->input->post('follow');


		if($follow=='y'){ // do follow
			# code here
			
			$is_follow = 'y';
		}else{ // do unfollow
			# code here
			
			$is_follow = 'n';
		}
		
		$this->layout->set_json(array(
			'user_id'   => $user_id,
			'is_follow' => $is_follow
		))->render();
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */