<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Gallery extends CI_Controller {

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
		redirect('listing');
		// $this->layout->set_header(array('title'=> 'aoenthu'))->set_view('main_view')->render();
	}
	
	function listing($page=1){
		/* 가짜 data를 생성, 모델과 연결하여야 함 */
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
				)
			);
		}
		// 가짜 데이터 끝
		$this->layout->set_view('gallery/listing_view', $data)->render();
	}

	function info($work_id=''){
		// 현재는 가짜, work info model에서 가지고 와야함
		$data = (object)array(
			'row' => (object)array(
				'work_id' => 1,
				'title' => 'aonethun',
				'user' => '',
				'regdate' => '',
				'keywords' => '',
				'tags' => '',
				'user_id' => '',
				'folder' => '',
				'contents' => array(
				),
				'moddate' => '',
				'hit_cnt' => '',
				'note_cnt' => '',
				'collect_cnt' => '',
				'comment_cnt' => '',
				'ccl' => '',
				'discoverbility' => ''
			)
		);
		$this->layout->set_view('gallery/info_view', $data)->render();
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */