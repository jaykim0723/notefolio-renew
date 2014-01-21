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
	

	function change_color(){
		$color = $this->input->post('color');

		# 이 유저에 대한 배경색을 변경해준다.
		$result = $this->profile_model->set_change_color(USER_ID, $color);
		if($result->status!='done')
			exit('fail');

		$data = array(
			'color' => $color
		);
		$this->layout->set_json($data)->render();
	}
	

	/**
	 * 프로필사진을 선정하고 크롭까지 지정하고 넘어온다.
	 * @return [type] [description]
	 */
	function change_face(){
		$input = $this->input->post(); // upload_id, x, y, w, h : 최대폭 800짜리를 기준으로 들어오므로, 원본크기에서 크롭할 때에는 좌표와 크기 조정을 다시, 800보다 작을 때에도 고려할 것

		#do stuff by 성수

		$data = array(
			'status' => 'done',
			'modified' => '',
		);
		$this->layout->set_json($data)->render();
	}

	/**
	 * 프로필 사진을 기본으로 돌리려고 하는 것이다.
	 * @return [type] [description]
	 */
	function delete_face(){
		// data/profiles/username_face.jpg 파일을 지운다.
		#do stuff by 성수

		$data = array(
			'status' => 'done',
			'modified' => ''
		);
		$this->layout->set_json($data)->render();
	}

	/**
	 * 프로필의 배경사진을 바꾸는 것
	 * @return [type] [description]
	 */
	function change_bg(){
		$upload_id = $this->input->post('upload_id');
	}


	/**
	 * 프로필 배경 사진을 기본으로 돌리려고 하는 것이다.
	 * @return [type] [description]
	 */
	function delete_bg(){
		// data/profiles/username_bg.jpg 파일을 지운다.

		#do stuff by 성수

		$data = array(
			'status' => 'done',
			'modified' => ''
		);
		$this->layout->set_json($data)->render();
	}














	/**
	 * get user info
	 * 
	 * @param string $username
	 * 
	 * @return object
	 */
	function _get_user_info($username){
		$user = $this->user_model->get_info(array(
			'username'=>$username,
			'get_profile' => TRUE
		));
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
	/**
	 * 작품고르기 팝업에서 호출할 용도
	 * @param  string  $username [description]
	 * @param  integer $page     [description]
	 * @return [type]            [description]
	 */
	function my_pop_recent_works($username='', $id_before=''){
		log_message('debug','--------- profile.php > my_pop_recent_works ( params : '.print_r(get_defined_vars(),TRUE)).')';
		
		$user = $this->_get_user_info($username);

        $this->load->model('work_model');
		$work_list = $this->work_model->get_list(array(
			'id_before' => $id_before,
			'user_id' => $this->user_id
		));
		$work_list->username = $username;
		$this->layout->set_view('profile/my_pop_recent_works_listing_view', $work_list)->render();
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
		
		if(USER_ID>0){
			$params = new stdClass();
			$params->follower_id = USER_ID;
			$params->follow_id = $user_id;
			if(!empty($params->follow_id) && $params->follow_id>0){
				switch($follow){
					case 'n':
						$result = $this->profile_model->delete_follow($params);
					break;
					case 'y':
					default:
						$result = $this->profile_model->post_follow($params);
					break;
				}
			}
			else {
				$result = (object)array(
						'status' => 'fail',
						'message' => 'no_follow_id'
					);
			}	
		}
		else{
			$result = (object)array(
					'status' => 'fail',
					'message' => 'not_logged_id'
				);
		}

		if($result->status=="done"){
			$is_follow = $follow;
		}
		else {
			$is_follow = ($follow=='n')?'y':'n';
		}

		$this->layout->set_json(array(
			'user_id'   => $user_id,
			'is_follow' => $is_follow
		))->render();
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */