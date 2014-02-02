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
			'page' => $page,
			'only_enable'=> true
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

		$work->row->hit_cnt++;
		$this->layout->set_view('gallery/info_view', $work)->render();

		//-- view count up
		$params = new stdClass();
		$params->user_id = USER_ID;
		$params->work_id = $work_id;
		if(!empty($params->work_id) && $params->work_id>0){
			$result = $this->work_model->post_view($params);
		}
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

	function save_cover($work_id=0, $upload_id=0){
		// 커버사진을 각 work_id에 임시폴더를 할당해서 저장한다.
		// 그리고 아래의 폼이 전송완료되었을 때에 대체한다.
		// upload_id:184
		// t2[x]:0
		// t2[y]:0
		// t2[w]:800
		// t2[h]:400
		// t3[x]:0
		// t3[y]:0
		// t3[w]:400
		// t3[h]:400

		# do stuff
		$this->load->config('upload', TRUE);
		$this->load->model('upload_model');
		$this->load->library('file_save');
		
		if(empty($upload_id)){
			$upload_id = $this->input->get_post('upload_id');
		}
		if(empty($work_id)){
			$work_id = $this->input->get_post('work_id');
		}

		$upload = $this->upload_model->get(array('id'=>$upload_id));
		if($upload->status=='done')
			$upload = $upload->row;

		$filename = $upload->filename;
		$filename = substr($filename, 0,2).'/'.substr($filename, 2, 2).'/'.$filename;

        list($width, $height) = getimagesize($this->config->item('img_upload_path', 'upload').$filename);

        $size = array('width'=> $width, 'height'=> $height);
        
        $crop_param_t2 = $this->input->get_post('t2');
        $crop_param_t3 = $this->input->get_post('t3');

		$to_crop_t2 = $this->file_save->get_crop_opt($size, array(
					'width'=>$crop_param_t2['w'],
					'height'=>$crop_param_t2['h'],
					'pos_x'=>$crop_param_t2['x'],
					'pos_y'=>$crop_param_t2['y']
				)
			);

		$to_crop_t3 = $this->file_save->get_crop_opt($size, array(
					'width'=>$crop_param_t3['w'],
					'height'=>$crop_param_t3['h'],
					'pos_x'=>$crop_param_t3['x'],
					'pos_y'=>$crop_param_t3['y']
				)
			);

        $result_t1 = $this->file_save->make_thumbnail(
			$this->config->item('img_upload_path', 'upload').$filename,
			$this->config->item('temp_upload_path', 'upload').$work_id.'_t1.jpg', 'small');
        $result_t2 = $this->file_save->make_thumbnail(
			$this->config->item('img_upload_path', 'upload').$filename,
			$this->config->item('temp_upload_path', 'upload').$work_id.'_t2.jpg', 'single',
			array('crop_to'=>$to_crop_t2, 'spanning'=>true));
        $result_t3 = $this->file_save->make_thumbnail(
			$this->config->item('img_upload_path', 'upload').$filename,
			$this->config->item('temp_upload_path', 'upload').$work_id.'_t3.jpg', 'wide', 
			array('crop_to'=>$to_crop_t3, 'spanning'=>true));

		$json = array(
			'status'=>($result_t1&&$result_t2&&$result_t3)?'done':'fail',
			'src'=> array(
				$this->config->item('temp_upload_uri', 'upload').$work_id.'_t1.jpg?_='.time(),
				$this->config->item('temp_upload_uri', 'upload').$work_id.'_t2.jpg?_='.time(),
				$this->config->item('temp_upload_uri', 'upload').$work_id.'_t3.jpg?_='.time()
			)
		);
		$this->layout->set_json($json)->render();
	}



	function save(){
		$input = $this->input->post();
		$work = $this->work_model->get_info(array(
			'work_id' => $input['work_id']
		));
		$input['contents'] = json_decode($input['contents']);
		$input['created_images'] = $input['deleted_images'] = array();
		$work_images = $input_images = array();
		if($work->row->contents=='')
			$work->row->contents = array();
		foreach($work->row->contents as $row){ // 기존 contents의 이미지 정보들을 수집
			if($row->t=='image' && $row->i!=''){
				$work_images[] = $row->i;
			}
		}
		foreach($input['contents'] as $row){ // 새로 들어온 것들을 비교하면서 최종 작업진행
			if($row->t=='image' && $row->i!=''){
				$input_images[] = $row->i;
				if(in_array($row->i, $work_images)!==FALSE)
					$input['created_images'][] = $row->i; // 기존에 없던 것이라면 이것은 추가된 것이다.
			}
		}
		foreach($work_images as $i){
			if(in_array($i, $input_images)==FALSE)
				$input['deleted_images'][] = $i; // 기존에는 있었지만 새로운 것에 없다면 삭제된 것이다.
		}
		$input['keywords'] = implode('', $input['keywords']);
		
		//var_export($input);

		$data = $this->work_model->put_info($input);
		$this->layout->set_json($data)->render();
	}



	function delete($work_id=''){
		$result = $this->work_model->delete_info(array('work_id'=>$work_id));
		if($result->status==='fail')
			alert($result->message);

		redirect('/'.$this->tank_auth->get_username());
	}



	function note(){
		$params = (object)$this->input->post();
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

		$this->layout->set_json($result)->render();
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