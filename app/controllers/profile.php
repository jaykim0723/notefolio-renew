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
	

	/**
	 * get user info
	 * 
	 * @param string $username
	 * 
	 * @return object
	 */
	function _get_user_info($username){
		$user = $this->user_model->get_info(array('username'=>$username));
		if($user->status=='fail'||count($user->row)<1)
			exit("redirect('/error_404');");
		else
			$this->user_id = $user->row->id;

		return $user;
	}



	function myworks($username='', $page=1){
		log_message('debug','--------- profile.php > myworks ( params : '.print_r(get_defined_vars(),TRUE)).')';
		
		$user = $this->_get_user_info($username);

        $this->load->model('work_model');
		$work_list = $this->work_model->get_list(array(
			'page' => $page,
			'user_id' => $this->user_id
		));
		$work_list->username = $username;
		if(!$this->input->is_ajax_request())
			$this->layout->set_view('profile/header_view', $user);
		$this->layout->set_view('profile/myworks_listing_view', $work_list)->render();
	}

	/**
	 * 작품상세의 사이드바에서 출력하기 위한 용도
	 * @param  string  $username [description]
	 * @param  integer $page     [description]
	 * @return [type]            [description]
	 */
	function my_recent_works($username='', $id_before=''){
		log_message('debug','--------- profile.php > my_recent_works ( params : '.print_r(get_defined_vars(),TRUE)).')';
		
		$user = $this->_get_user_info($username);

        $this->load->model('work_model');
		$work_list = $this->work_model->get_list(array(
			'id_before' => $id_before,
			'user_id' => $this->user_id
		));
		$work_list->username = $username;
		$this->layout->set_view('profile/my_recent_works_listing_view', $work_list)->render();
	}

	



	function about($username=''){
		log_message('debug','--------- about ( params : '.print_r(get_defined_vars(),TRUE)).')';
		
		$user = $this->_get_user_info($username);
		
		if(!$this->input->is_ajax_request())
			$this->layout->set_view('profile/header_view', $user);
		$this->layout->set_view('profile/about_view')->render();
	}

	



	function collection($username='', $page=1){
		log_message('debug','--------- collection ( params : '.print_r(get_defined_vars(),TRUE)).')';
		
		$user = $this->_get_user_info($username);

		$collection_list = $this->profile_model->get_collection_list(array(
			'page' => $page,
			'user_id' => $this->user_id
		));
		if(!$this->input->is_ajax_request())
			$this->layout->set_view('profile/header_view', $user);
		$this->layout->set_view('profile/collection_listing_view', $collection_list)->render();
	}

	



	function statistics($username='', $page=1){
		log_message('debug','--------- statistics ( params : '.print_r(get_defined_vars(),TRUE)).')';
		
		$user = $this->_get_user_info($username);

		if(!$this->input->is_ajax_request())
			$this->layout->set_view('profile/header_view', $user);
		$this->layout->set_view('profile/statistics_view')->render();
	}

	



	function followings($username='', $page=1){
		log_message('debug','--------- followings ( params : '.print_r(get_defined_vars(),TRUE)).')';
		
		$user = $this->_get_user_info($username);

		if(!$this->input->is_ajax_request())
			$this->layout->set_view('profile/header_view', $user);

		$followings_list = $this->profile_model->get_followings_list(array(
			'page' => $page,
			'user_id' => $this->user_id
		));
		$this->layout->set_view('profile/follow_listing_view', $followings_list)->render();
	}

	function followers($username='', $page=1){
		log_message('debug','--------- followers ( params : '.print_r(get_defined_vars(),TRUE)).')';

		$user = $this->_get_user_info($username);

		if(!$this->input->is_ajax_request())
			$this->layout->set_view('profile/header_view', $user);

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