<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Gallery extends CI_Controller {


    public function __construct()
    {
        parent::__construct();
        $this->load->model('work_model');
		$this->nf->_member_check(array('create','update','delete'));
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
		$work = $this->work_model->get_info(array(
			'work_id' => $work_id,
			'folder'  => ''
		));
		if($work->status==='fail') alert('작품이 존재하지 않습니다.');
		$this->layout->set_view('gallery/info_view', $work)->render();
	}


	function create(){
		$work_id = $this->work_model->post_info(); // 비어있는 값으로 생성하고
		if(emptY($work_id)) alert('작품이 존재하지 않습니다.');
		redirect($this->session->userdata('username').'/'.$work_id.'/update');
	}
	function upload(){ // 기존의 주소를 보전하기 위하여
		redirect('gallery/create');
	}



	function update($work_id=''){
		$work = $this->work_model->get_info(array('work_id'=>$work_id)); 
		if($work->status==='fail') alert('작품이 존재하지 않습니다.');
		if($work->row->user_id!==USER_ID) alert('본인의 작품만 수정할 수 있습니다.');

		$this->form($work);
	}
	
	function form($work=NULL){
		$this->load->helper('form');
		$this->layout->set_view('gallery/form_view', $work)->render();
	}

	function save(){
		$input = $this->input->post();
		$data = $this->work_model->put_info((object)$input);
		$this->layout->set_json($data)->render();
	}



	function delete($work_id=''){
		$result = $this->work_model->delete_info(array('work_id'=>$work_id));
		if($result->status==='fail')
			alert($result->message);

		redirect('/mypage');
		
		// 삭제가 완료되면 어디로 가는가?
		// 몰라 -> 3루수였던가...(?!)
	}



	function note(){
		$params = $this->input->post();
		//$result = $this->work_model->note($params);
		$params->user_id = USER_ID;
		if(!empty($params->work_id) && $params->work_id>0){
			$note = $params->note;
			unset($params->note);
			switch($note){
				case 'n':
					$result = $this->work_model->delete_note($params);
				break;
				case 'y':
				default:
					$result = $this->work_model->post_note($params);
				break;
			}
		}
		else {
			$result = (object)array(
					'status' => 'fail',
					'message' => 'no_work_id'
				);
		}	

		//$this->layout->set_json($result)->render();
	}

	function collect(){
		$params = (object)$this->input->post();
		//$result = $this->work_model->collect($params);
		if(USER_ID>0){
			$params->user_id = USER_ID;
			if(!empty($params->work_id) && $params->work_id>0){
				$collect = $params->collect;
				unset($params->collect);
				switch($collect){
					case 'n':
						$result = $this->work_model->delete_collect($params);
					break;
					case 'y':
					default:
						$result = $this->work_model->post_collect($params);
					break;
				}
			}
			else {
				$result = (object)array(
						'status' => 'fail',
						'message' => 'no_work_id'
					);
			}	
		}
		else{
			$result = (object)array(
					'status' => 'fail',
					'message' => 'not_logged_id'
				);
		}

		$this->layout->set_json($result)->render();
	}



}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */