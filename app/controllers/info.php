<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Info extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->nf->_member_check(array());
	}
	
	
	//
	function upgrade()
	{
		$this->load->view('info/upgrade_view');
		/*
		$this->notefolio->template(array(
			array('info/upgrade_view')
		));
		*/
	}

	// 가장 처음에 출력되는 것
	function index()
	{
		$this->about_us();
	}
	
	
	function about_us()
	{
		$this->layout
			->set_header('title', 'About Us')
			->set_view('info/about_us_view')
			->render();
	}	
	
	function faq()
	{
		$this->layout
			->set_view('info/faq_view')
			->render();
	}

	function contact_us()
	{

	   	$data = array(
	   		'type' => '문의',
			'name' => '',
			'email' => '',
			'tel' => '',
			'contents' => ''
	   	);
		
		if(!$this->input->post('submit')){
		   ;
		}else{
          	// 폼을 검증한다.
          	function set_value_to_data(){
               return array( // 에러값들을 넣어서 활용할 준비를 한다.
                    'type' => set_value('type'),
                    'name' => set_value('name'),
                    'email' => set_value('email'),
                    'tel' => set_value('tel'),
                    'contents' => set_value('contents'),
               );        
         	}
    		
    		$this->load->library('form_validation');

          	// 입력값의 검사조건 설정(이 함수는 수정할 때에도 동일하게 쓰임)
          	$this->form_validation
          	->set_rules('type', '타입', 'trim|required')
          	->set_rules('name', '이름', 'trim|required|max_length[10]')
          	->set_rules('email', '이메일', 'trim|required|valid_email|max_length[50]')
          	->set_rules('tel', '연락처', 'trim|required')
          	->set_rules('contents', '내용', 'trim|required')
 			->set_error_delimiters('<div class="alert alert-error">', '</div>');
          	if($this->form_validation->run() !== FALSE){
               $row = set_value_to_data();
               // 성공한 경우..
               $this->load->config('tank_auth');
				$this->load->library('email');
				
				$this->email->from($row['email'], $row['name']);
				$this->email->to($this->config->item('webmaster_email', 'tank_auth'));  
				//$this->email->to('zidell@gmail.com');  
				$this->email->subject('Contact Us : '.mb_substr($row['contents'], 0, 20));
				$this->email->message('종류 : '.$row['type'].'<br/>연락처 : '.$row['tel'].'<br/>이메일 : '.$row['email'].'<br/><br/>'.nl2br($row['contents']));
				$this->email->send();

               $data['success'] = TRUE; // 결과 메시지를 출력하도록... 
               
			}else{
			 	$data = set_value_to_data();
			
			}          
		
		}
		
		$this->layout
			->set_view('info/contact_us_view', $data)
			->render();
	}

	function terms()
	{
		$this->layout
			->set_view('info/term_view')
			->render();
	}

	function privacy()
	{
		$this->layout
			->set_view('info/privacy_view')
			->render();
	}


	function test(){
		$this->layout->set_view('info/test_view')->render();
	}


}
