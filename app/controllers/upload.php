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
		$row = $this->db
			->where('type', 'cover')
			->where('work_id', $work_id)
			->get('uploads');

		$data = array(
			'upload_id' =>  $row->id,
			'src' => preg_replace(
                        '/^(..)(..)([^\.]+)(\.[a-zA-Z]+)/', 
                        $this->config->item('img_upload_uri','upload')'$1/$2/$1$2$3_v2.jpg', 
                        $row->filename
                        );
		);
		$this->layout->set_json($data)->render();
	}

	/**
	 * get image and do process
	 * 
	 * @param file $file
	 * @return no retun
	 */
	function image($file=null){
		sleep(2);

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
