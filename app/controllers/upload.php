<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Upload extends CI_Controller
{
	
	function __construct()
	{
		parent::__construct();

		if(USER_ID==0) // not signend in
			exit(json_encode(array("status"=>"fail", "message"=>"not_signed_user")));

		$this->load->config('upload', TRUE);
		$this->load->model('upload_model');
		$this->load->library('file_save');
	}
	
	function get_upload_id_by_work_id($work_id){
		$data = array(
			'upload_id' =>  '',
			'src' => ''
		);
		$this->layout->set_json($data)->render();
	}

	/**
	 * crop image for profile face
	 * 
	 * @param int $upload_id
	 * @return no retun
	 */
	function profile_face($upload_id=0, $username=''){
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

        list($width, $height) = getimagesize($this->config->item('img_upload_path', 'upload').$filename);

        $size = array('width'=> $width, 'height'=> $height);
        $o_crop = array(
				'width'=>$this->input->get_post('w'),
				'height'=>$this->input->get_post('h'),
				'pos_x'=>$this->input->get_post('x'),
				'pos_y'=>$this->input->get_post('y')
			);

		$to_crop = $this->file_save->get_crop_opt($size, $o_crop);

		$result = $this->file_save->make_thumbnail(
			$this->config->item('img_upload_path', 'upload').$filename,
			$this->config->item('profile_upload_path', 'upload').$username.'_face.jpg',
			'profile_face', 
			array('crop_to'=>$to_crop)
			);


		//upload_id=111&x=98&y=0&w=293&h=293
		$json = array(
			'status'=>($result)?'done':'fail'
			);
		$this->layout->set_json($json)->render();
	}

	/**
	 * crop image for profile background
	 * 
	 * @param int $upload_id
	 * @return no retun
	 */
	function profile_background($upload_id=0, $username=''){
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
			'large'
			);

		//upload_id=111&x=98&y=0&w=293&h=293
		$json = array(
			'status'=>($result)?'done':'fail'
			);
		$this->layout->set_json($json)->render();
	}

	/**
	 * get image and do process
	 * 
	 * @param file $file
	 * @return no retun
	 */
	function image($file=null){
		if(empty($file)){
			if ($filename = $this->input->get_post('qqfile')) {
			    // XMLHttpRequest stream'd upload

			    include_once(APPPATH.'libraries/qqUploadedFileXhr.php');

			    $xhrUpload = new qqUploadedFileXhr();
			    $file = $xhrUpload->makeTempFile()->toFileArray();
			} elseif (count($_FILES)) {
			    // Normal file upload
			    //$file = array_shift($_FILES);
				$file = $_FILES['qqfile'];
			}
		}

		$error = true;
		
		if($file=='debug'){
			$error = false;

			$json = array(
				'status' => 'done',
				'upload_id' => 0,
				'filename' => 'QWERTYUIOP1234567890ASDFGHJKL.png'
				);
		}
		else if($file!=null){
			$error = false;
			
			$filename = $this->file_save->save('image', $file);

			$upload_id = $this->upload_model->post(array(
	            'work_id' => $this->input->get_post('work_id'),
	            'type' => 'work',
	            'filename' => $filename['original'],
	            'org_filename' => $file['name'],
	            'filesize' => $file['size'],
	            'comment' => ''
	        ));

	        $json = array(
	        	'status' => 'done',
	        	'message'	=> 'successed',
	        	'upload_id' => $upload_id,
	        	'src' => $filename['uri'].$filename['medium'],
	            'org_filename' => $file['name'],
	        	'data' => $this->upload_model->get(array('id'=>$upload_id))->row
	        	);
		}

		if($error){
			$json = array(
				'status' => 'fail',
				'message' => 'no_file_received'
				);
		}

		$this->layout->set_json($json)->render();
	}
	
}
