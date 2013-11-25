<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Gallery extends CI_Controller {


    public function __construct()
    {
        parent::__construct();
        $this->load->model('work_model');
		$this->nf->_member_check(array('update','delete'));
    }

	
	public function index()
	{
		$this->listing(1);
	}
	
	/**
	 * 리스트 출력에 관한 것
	 * @param  integer $page [description]
	 * @return [type]        [description]
	 */
	function listing($page=1){
		$work_list = $this->work_model->get_list(array(
			'page' => $page
		));
		$this->layout->set_view('gallery/listing_view', $work_list)->render();
	}

	/**
	 * 작품의 개별 정보를 불러들인다.
	 * @param  string $work_id [description]
	 * @return [type]          [description]
	 */
	function info($work_id=''){
		$work = $this->work_model->get_info($work_id);
		$this->layout->set_view('gallery/info_view', $work)->render();
	}


	function create(){
		$work = $this->work_model->post_info(); // 비어있는 값으로 생성하고
		$this->form($work);
	}
	function upload(){ // 기존의 주소를 보전하기 위하여
		$this->create();
	}
	function update($work_id=''){
		$work = $this->work_model->get_info($work_id); // 비어있는 값으로 생성하고
		$this->form($work);
	}
	
	function form($work=NULL){
		$this->load->helper('form');
		if($work==NULL)
			return;
		$this->layout->set_view('gallery/form_view', $work)->render();
	}

	function save(){
		$input = $this->input->post();
		$data = $this->work_model->put_info($input);
		$this->layout->set_json($data)->render();
	}



	function delete($work_id=''){
		$work = $this->work_model->get_info($work_id);
		exit(print_r($work));
		
	}


}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */