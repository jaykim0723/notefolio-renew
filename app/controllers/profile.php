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
		
		$this->user_id = $this->profile_model->get_user_id_from_username($username);
		$user = $this->user_model->get(array('id'=>$this->user_id));

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
		$this->user_id = $this->profile_model->get_user_id_from_username($username);
		$user = $this->user_model->get(array('id'=>$this->user_id));
		
		if(!$this->input->is_ajax_request())
			$this->layout->set_view('profile/header_view', $user->row);
		$this->layout->set_view('profile/about_view')->render();
	}

	



	function collection($username='', $page=1){
		log_message('debug','--------- collection ( params : '.print_r(get_defined_vars(),TRUE)).')';

		$this->user_id = $this->profile_model->get_user_id_from_username($username);
		$user = $this->user_model->get(array('id'=>$this->user_id));

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
		$this->user_id = $this->profile_model->get_user_id_from_username($username);
		$user = $this->user_model->get(array('id'=>$this->user_id));

		if(!$this->input->is_ajax_request())
			$this->layout->set_view('profile/header_view', $user->row);
		$this->layout->set_view('profile/statistics_view')->render();
	}

	



	function following($username='', $page=1){
		log_message('debug','--------- following ( params : '.print_r(get_defined_vars(),TRUE)).')';
		$this->user_id = $this->profile_model->get_user_id_from_username($username);
		$user = $this->user_model->get(array('id'=>$this->user_id));

		if(!$this->input->is_ajax_request())
			$this->layout->set_view('profile/header_view', $user->row);
		$following_list = $this->profile_model->get_following_list(array(
			'page' => $page,
			'user_id' => $this->user_id
		));
		// $this->layout->set_view('profile/follow_listing_view', $following_list)->render();
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */