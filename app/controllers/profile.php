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
	function change_face($upload_id=0, $username=''){
		$this->load->config('upload', TRUE);
		$this->load->model('upload_model');
		$this->load->library('file_save');
		
		if(empty($upload_id)){
			$upload_id = $this->input->get_post('upload_id');
		}
		if(empty($username)){
			$username = $this->tank_auth->get_username();
		}

		$upload = $this->upload_model->get(array('id'=>$upload_id));
		if($upload->status=='done')
			$upload = $upload->row;

		$filename = preg_replace(
                        '/^(..)(..)([^\.]+)(\.[a-zA-Z]+)/', 
                        '$1/$2/$1$2$3$4', 
                        $upload->filename
                        );

        list($width, $height) = getimagesize($this->config->item('img_upload_path', 'upload').$filename);

        $size = array('width'=> $width, 'height'=> $height);
        $o_crop = array(
				'width'  => $this->input->get_post('w'),
				'height' => $this->input->get_post('h'),
				'pos_x'  => $this->input->get_post('x'),
				'pos_y'  => $this->input->get_post('y')
			);

    	$maxsize = $this->config->item('thumbnail_medium', 'upload');
        if($size['width']<$maxsize['max_width']){
        	$opt['width'] = $size['width'];
        } else {
        	$opt = array();
        }

		$to_crop = $this->file_save->get_crop_opt($size, $o_crop, $opt);

		$result = $this->file_save->make_thumbnail(
			$this->config->item('img_upload_path', 'upload').$filename,
			$this->config->item('profile_upload_path', 'upload').$username.'_face.jpg',
			'profile_face', 
			array('crop_to'=>$to_crop)
			);

		if($result=='done'){
			$this->user_model->put_timestamp(array('id'=>USER_ID));
		}

		//upload_id=111&x=98&y=0&w=293&h=293
		$json = array(
			'status'=>($result)?'done':'fail',
			'src'=>$this->config->item('profile_upload_uri', 'upload').$username.'_face.jpg?_='.time()
			);
		$this->layout->set_json($json)->render();
	}

	/**
	 * 프로필 사진을 기본으로 돌리려고 하는 것이다.
	 * @return [type] [description]
	 */
	function delete_face(){
		// data/profiles/username_face.jpg 파일을 지운다.

		if(empty($username)){
			$username = $this->tank_auth->get_username();
		}

		$this->load->config('upload', TRUE);
		$result = unlink($this->config->item('profile_upload_path', 'upload').$username.'_face.jpg');

		$data = array(
			'status'=>($result)?'done':'fail'
			);

		$this->layout->set_json($data)->render();
	}

	/**
	 * 프로필의 배경사진을 바꾸는 것
	 * @return [type] [description]
	 */
	function change_bg($upload_id=0, $username=''){
		$this->load->config('upload', TRUE);
		$this->load->model('upload_model');
		$this->load->library('file_save');

		if(empty($upload_id)){
			$upload_id = $this->input->get_post('upload_id');
		}
		if(empty($username)){
			$username = $this->tank_auth->get_username();
		}

		$upload = $this->upload_model->get(array('id'=>$upload_id));
		if($upload->status=='done')
			$upload = $upload->row;

		$filename = $upload->filename;
		$filename = substr($filename, 0,2).'/'.substr($filename, 2, 2).'/'.$filename;

		$result = $this->file_save->make_thumbnail(
			$this->config->item('img_upload_path', 'upload').$filename,
			$this->config->item('profile_upload_path', 'upload').$username.'_bg.jpg',
			'exlarge', array('spanning'=>true)
			);

		if($result=='done'){
			$this->user_model->put_timestamp(array('id'=>USER_ID));
		}

		//upload_id=111&x=98&y=0&w=293&h=293
		$json = array(
			'status'=>($result)?'done':'fail',
			'src'=>$this->config->item('profile_upload_uri', 'upload').$username.'_bg.jpg?_='.time()
			);
		$this->layout->set_json($json)->render();
	}




	/**
	 * 프로필 배경 사진을 기본으로 돌리려고 하는 것이다.
	 * @return [type] [description]
	 */
	function delete_bg(){
		// data/profiles/username_bg.jpg 파일을 지운다.

		if(empty($username)){
			$username = $this->tank_auth->get_username();
		}

		$this->load->config('upload', TRUE);
		$result = unlink($this->config->item('profile_upload_path', 'upload').$username.'_bg.jpg');

		$data = array(
			'status'=>($result)?'done':'fail'
			);

		$this->layout->set_json($data)->render();
	}








	/**
	 * 사용자의 사용자명을 변경하는 것
	 * @return [type] [description]
	 */
	function change_realname(){
		$realname = $this->input->post('realname'); 

		$json = $this->profile_model->set_change_realname(USER_ID, $realname);
		if($json->status=='done'){
			$this->load->config('upload', TRUE); //load upload config file
			
			$old_file = $this->config->item('profile_upload_path', 'upload').$this->session->userdata('realname');
			$new_file = $this->config->item('profile_upload_path', 'upload').$realname;
			foreach(array( '_face.jpg', '_bg.jpg' ) as $file_tail){
				if(file_exists($old_file.$file_tail)){
					rename($old_file.$file_tail, $new_file.$file_tail);
				}
			}	

			$this->session->set_userdata('realname', $realname); //change session realname
			$json->realname = $realname;	
		}
		else{
			$json->realname = $this->session->userdata('realname');
		}
		
		$this->layout->set_json($json)->render();
	}


	/**
	 * 사용자의 카테고리를 변경하는 것
	 * @return [type] [description]
	 */
	function change_keywords(){
		$keywords = $this->input->post('keywords');

		$json = $this->profile_model->set_change_keywords(USER_ID, $keywords);

		$json->keywords_string = $this->nf->category_to_string($keywords, true); // php에서 만드는 것을 통일하려고.
		$this->layout->set_json($json)->render();
	}


	/**
	 * 사용자의 SNS 주소를 변경하는 것
	 * @return [type] [description]
	 */
	function change_sns(){
		$input = $this->input->post(); // 값이 비어있으면 그것은 제외하는 것이다.
		$json = $this->profile_model->set_change_sns(USER_ID, $input);
		$json->sns_string = $this->nf->sns_to_string($input);
		$this->layout->set_json($json)->render();
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
		$user->total = $this->profile_model->get_statistics_total(array('user_id'=>$this->user_id))->row;

        $this->load->model('work_model');
		$work_list = $this->work_model->get_list(array(
			'page' => $page,
			'user_id' => $this->user_id
		));
		$work_list->username = $username;
		$profile_header = array(
			'username'  =>$user->row->username,
			'is_follow' =>$user->row->is_follow,
			'user_id'   =>$user->row->user_id,
		);
		if(!$this->input->is_ajax_request())
			$this->layout->set_header('profile', $profile_header)->set_view('profile/header_view', $user);
		$this->layout->set_header(array(
            'keywords' => implode(', ', $this->nf->category_to_array($user->keywords)),
            'title' => $user->row->realname.'님의 작품 - '.implode(', ', $this->nf->category_to_array($user->row->keywords)),
        ))->set_view('profile/myworks_listing_view', $work_list)->render();
	}

	/**
	 * 작품상세의 사이드바에서 출력하기 위한 용도
	 * @param  string  $username [description]
	 * @param  integer $page     [description]
	 * @return [type]            [description]
	 */
	function my_recent_works($username=''){
		log_message('debug','--------- profile.php > my_recent_works ( params : '.print_r(get_defined_vars(),TRUE)).')';
		
		$user = $this->_get_user_info($username);
        $this->load->model('work_model');
        $params = array(
			'user_id' => $this->user_id,
		);
        if($this->input->get('id_before')!=''){
        	$params['id_before'] = $this->input->get('id_before');
        	$params['order_by'] = 'idlarger';
        }else{
         	$params['id_after'] = $this->input->get('id_after');
        	$params['order_by'] = 'idlarger';
       }
		$work_list = $this->work_model->get_list($params);
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
		$profile_header = array(
			'username'=>$user->row->username,
			'is_follow'=>$user->row->is_follow,
			'user_id'   =>$user->row->user_id,
			);
		if(!$this->input->is_ajax_request())
			$this->layout->set_header('profile', $profile_header);
		$this->layout->set_view('profile/my_pop_recent_works_listing_view', $work_list)->render();
	}

	



	function about($username=''){
		log_message('debug','--------- about ( params : '.print_r(get_defined_vars(),TRUE)).')';
		
		$user = $this->_get_user_info($username);
		$user->total = $this->profile_model->get_statistics_total(array('user_id'=>$this->user_id))->row;

		$data = $this->profile_model->get_about(array(
			'user_id' => $user->row->id
		));
		$data->user = $user;
		
		$profile_header = array(
			'username'=>$user->row->username,
			'is_follow'=>$user->row->is_follow,
			'user_id'   =>$user->row->user_id,
			);
		if(!$this->input->is_ajax_request())
			$this->layout->set_header('profile', $profile_header)->set_view('profile/header_view', $user);
	
		$this->layout->set_view('profile/about_view', $data)->render();
	}

	function update_about(){
		$contents = $this->input->post('contents');
		$attachments = $this->input->post('attachments');
		
		$about = $this->profile_model->get_about(array(
			'user_id' => USER_ID
		));

		$json = $this->profile_model->put_about(array(
			'user_id' => USER_ID,
			'contents' => $contents,
			'attachments' => $attachments
		));
		$this->layout->set_json($json)->render();
	}

	function read_about(){
		$json = $this->profile_model->get_about(array(
			'user_id' => USER_ID
		));
		$json->row->contents = nl2br($json->row->contents);
		$this->layout->set_json($json)->render();
	}


	



	function collect($username='', $page=1){
		log_message('debug','--------- collection ( params : '.print_r(get_defined_vars(),TRUE)).')';
		
		$user = $this->_get_user_info($username);
		$user->total = $this->profile_model->get_statistics_total(array('user_id'=>$this->user_id))->row;

		$collection_list = $this->profile_model->get_collection_list(array(
			'page' => $page,
			'user_id' => $user->row->id
		));

		$profile_header = array(
			'username'=>$user->row->username,
			'is_follow'=>$user->row->is_follow,
			'user_id'   =>$user->row->user_id,
			);
		if(!$this->input->is_ajax_request())
			$this->layout->set_header('profile', $profile_header)->set_view('profile/header_view', $user);
		$this->layout->set_view('profile/collection_listing_view', $collection_list)->render();
	}

	
	function collection($username='', $page=1){ // for old version
		return $this->collect($username, $page);
	}



	function statistics($username='', $page=1){
		log_message('debug','--------- statistics ( params : '.print_r(get_defined_vars(),TRUE)).')';
		$user = $this->_get_user_info($username);
		if(USER_ID!=$user->row->id)
			alert('본인의 통계만 확인할 수 있습니다.');

		$user->total = $this->profile_model->get_statistics_total(array('user_id'=>$this->user_id))->row;

		$data = (object)array(
		);

		$profile_header = array(
			'username'=>$user->row->username,
			'is_follow'=>$user->row->is_follow,
			'user_id'   =>$user->row->user_id,
			);
		if(!$this->input->is_ajax_request())
			$this->layout->set_header('profile', $profile_header)->set_view('profile/header_view', $user);
		$this->layout->set_view('profile/statistics_view', $data)->render();
	}
	function _get_date_by_period($period){
		$edate = date('Y-m-d', time());
		switch($period){
			case 'latest1':
				$sdate = date('Y-m-d', strtotime('-1 month'));
				break;
			case 'latest3':
				$sdate = date('Y-m-d', strtotime('-3 month'));
				break;
			case 'this_m':
				$sdate = date('Y-m', time()).'-01';
				$edate = date('Y-m', time()).'-'.date('t');
				break;
			case 'prev_m':
				$sdate = date('Y-m', strtotime('-1 month')).'-01';
				$edate = date('Y-m', strtotime('-1 month')).'-'.date('t', strtotime('-1 month'));
				break;
			default :
				list($sdate, $edate) = explode('~', $period);
		}
		return (object)array(
			'sdate' => $sdate,
			'edate' => $edate
		);
	}

	function statistics_count(){
		$period = $this->input->get('period');
		$date = $this->_get_date_by_period($period);
		$json = $this->profile_model->get_statistics_count(array(
			'user_id' => USER_ID,
			'sdate' => $date->sdate,
			'edate' => $date->edate
		));
		$this->layout->set_json($json)->render();
	}

	function statistics_chart(){
		$type = $this->input->get('type');
		$period = $this->input->get('period');
		$date = $this->_get_date_by_period($period);
		$json = $this->profile_model->get_statistics_chart(array(
			'user_id' => USER_ID,
			'type' => $type,
			'sdate' => $date->sdate,
			'edate' => $date->edate
		));
		$new_rows = array();
		foreach($json->rows as $ymd => $value){
			$new_rows[] = array(strtotime($ymd)*1000, $value);
		}
		$json->rows = $new_rows;
		$this->layout->set_json($json)->render();
	}

	function statistics_datatable(){
		$period = $this->input->get('period');
		$date = $this->_get_date_by_period($period);
		$json = $this->profile_model->get_statistics_datatable(array(
			'user_id' => USER_ID,
			'sdate' => $date->sdate,
			'edate' => $date->edate
		));
		$this->layout->set_json($json)->render();
	}




	function followings($username='', $page=1){
		log_message('debug','--------- followings ( params : '.print_r(get_defined_vars(),TRUE)).')';
		
		$user = $this->_get_user_info($username);
		$user->total = $this->profile_model->get_statistics_total(array('user_id'=>$this->user_id))->row;

		if(!$this->input->is_ajax_request())
			$this->layout->set_view('profile/header_view', $user);

		$followings_list = $this->profile_model->get_followings_list(array(
			'page' => $page,
			'user_id' => $this->user_id
		));
		
		$profile_header = array(
			'username'=>$user->row->username,
			'is_follow'=>$user->row->is_follow,
			'user_id'   =>$user->row->user_id,
			);
		if(!$this->input->is_ajax_request())
			$this->layout->set_header('profile', $profile_header)->set_view('profile/follow_listing_view', $followings_list)->render();
	}


	function followers($username='', $page=1){
		log_message('debug','--------- followers ( params : '.print_r(get_defined_vars(),TRUE)).')';

		$user = $this->_get_user_info($username);
		$user->total = $this->profile_model->get_statistics_total(array('user_id'=>$this->user_id))->row;

		if(!$this->input->is_ajax_request())
			$this->layout->set_view('profile/header_view', $user);

		$followers_list = $this->profile_model->get_followers_list(array(
			'page' => $page,
			'user_id' => $this->user_id
		));

		$profile_header = array(
			'username'=>$user->row->username,
			'is_follow'=>$user->row->is_follow,
			'user_id'   =>$user->row->user_id,
			);
		if(!$this->input->is_ajax_request())
			$this->layout->set_header('profile', $profile_header)->set_view('profile/follow_listing_view', $followers_list)->render();
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

                        if($result->status=="done"){
                            //-- write activity
                            $this->load->library('activity');
                            $this->activity->post(array(
                                'crud' => 'delete',
                                'area' => 'user',
                                'type'  => 'follow',
                                'user_A' => $params->follower_id,
                                'user_B' => $params->follow_id,
                                ));
                        }
					break;
					case 'y':
					default:
						$result = $this->profile_model->post_follow($params);

                        if($result->status=="done"){
                            //-- write activity
                            $this->load->library('activity');
                            $this->activity->post(array(
                                'crud' => 'create',
                                'area' => 'user',
                                'type'  => 'follow',
                                'user_A' => $params->follower_id,
                                'user_B' => $params->follow_id,
                                ));
                        }
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
			'status'	=> $result->status,
			'user_id'   => $user_id,
			'is_follow' => $is_follow
		))->render();
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */