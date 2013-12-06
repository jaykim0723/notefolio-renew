<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Upload extends CI_Controller
{
	
	function __construct()
	{
		parent::__construct();
	}

	function index()
	{
		if(USER_ID==0)
			exit(json_encode(array("status"=>"fail", "message"=>"not_signed_user")));

		$file_element_name = 'qqfile';	
		if($this->input->get('c') && $this->input->get('c')!=''){
			$category_config = $this->db->select('type, extensions,thumbnail_width, thumbnail_height,thumbnail_style')->where('category', $this->input->get('c'))->get('gi_categories')->row_array();
		}else{
			$category_config = array(
				'type' => '',
				'extensions' => $this->config->item('gi_extensions'),
				'thumbnail_width' => $this->config->item('gi_thumbnail_width'),
				'thumbnail_height' => $this->config->item('gi_thumbnail_height'),
				'thumbnail_style' => $this->config->item('gi_thumbnail_style')
			);
		}
		$config['allowed_types'] = $category_config['extensions'];
		if($this->gumo->is_admin()){
			$config['allowed_types'] = '*';
		}
		log_message('debug','------- config --'.print_r($config,TRUE));
		
		$config['upload_path'] = BASEPATH.'../www'.$this->config->item('upload_path');
		$config['max_size']	= $this->config->item('max_size');
		$this->upload->initialize($config);
		
		$status = 'error';
		$msg = '';

		if ($this->input->get($file_element_name))
		{ // 웹표준 브라우저, 크롬, 파이어폭스등
			$this->upload->orig_name = $this->input->get($file_element_name);
			$this->upload->file_ext = pathinfo($this->upload->orig_name, PATHINFO_EXTENSION);			

			$input = fopen('php://input', 'r');
			$temp = tmpfile();
			$this->upload->file_size = stream_copy_to_stream($input, $temp);
			$temp_meta = stream_get_meta_data($temp);
			$imagesize = getimagesize($temp_meta['uri']);


			$this->upload->file_temp = $temp_meta['uri'];
			fclose($input);
			
			$this->upload->file_mime_type(array(
				'tmp_name' => $temp_meta['uri'],
				'type' => $imagesize['mime']
			));
			$this->upload->file_type = preg_replace("/^(.+?);.*$/", "\\1", $this->upload->file_type);
			$this->upload->file_type = strtolower(trim(stripslashes($this->upload->file_type), '"'));
			$this->upload->file_name = $this->upload->prep_filename($this->upload->orig_name);
			$this->upload->file_ext	 = $this->upload->get_extension($this->upload->file_name);
			$this->upload->client_name = $this->upload->file_name;
			

			if(!(isset($_SERVER['CONTENT_LENGTH']) && $this->upload->file_size === (int)$_SERVER['CONTENT_LENGTH'])){
				$msg = 'invalid_file';
			}

			if($msg==''){
				// Is the upload path valid?
				if ( ! $this->upload->validate_upload_path())
				{
					// errors will already be set by validate_upload_path() so just return FALSE
					$msg = 'upload_destination_error';
				}
			}
			
			if($msg==''){
				// Is the file type allowed to be uploaded?
				if ( ! $this->upload->is_allowed_filetype(TRUE)){
					$msg = 'upload_invalid_filetype';
				}
			}

			if($msg==''){
				// Convert the file size to kilobytes
				if ($this->upload->file_size > 0)
				{
					$this->upload->file_size = round($this->upload->file_size/1024, 2);
				}
				// Is the file size within the allowed maximum?
				if ( ! $this->upload->is_allowed_filesize())
				{
					$msg = 'upload_invalid_filesize';
				}
				
			}
			
		
			if($msg==''){
				// Sanitize the file name for security
				$this->upload->file_name = $this->upload->clean_file_name($this->upload->file_name);
		
				// Truncate the file name if it's too long
				if ($this->upload->max_filename > 0)
				{
					$this->upload->file_name = $this->upload->limit_filename_length($this->upload->file_name, $this->upload->max_filename);
				}
		
				// Remove white spaces in the name
				if ($this->upload->remove_spaces == TRUE)
				{
					$this->upload->file_name = preg_replace("/\s+/", "_", $this->upload->file_name);
				}
		
				/*
				 * Validate the file name
				 * This function appends an number onto the end of
				 * the file if one with the same name already exists.
				 * If it returns false there was a problem.
				 */
				$this->upload->orig_name = $this->upload->file_name;
		
				if ($this->upload->overwrite == FALSE)
				{
					$this->upload->file_name = $this->upload->set_filename($this->upload->upload_path, $this->upload->file_name);
		
					if ($this->upload->file_name === FALSE)
					{
						$msg = 'upload_bad_filename';
					}
				}
			}
			
			if($msg==''){
				if ($this->upload->xss_clean)
				{
					if ($this->upload->do_xss_clean() === FALSE)
					{
						$msg = 'upload_unable_to_write_file';
					}
				}
			}
			if($msg==''){
				/*
				 * Move the file to the final destination
				 * To deal with different server configurations
				 * we'll attempt to use copy() first.  If that fails
				 * we'll use move_uploaded_file().  One of the two should
				 * reliably work in most environments
				 */
				if ( ! @copy($this->upload->file_temp, $this->upload->upload_path.$this->upload->file_name))
				{
					if ( ! @move_uploaded_file($this->upload->file_temp, $this->upload->upload_path.$this->upload->file_name))
					{
						$msg = 'upload_destination_error';
					}
				}
			}	
			/*
			 * Set the finalized image dimensions
			 * This sets the image width/height (assuming the
			 * file was an image).  We use this information
			 * in the "data" function.
			 */
			$this->upload->set_image_properties($this->upload->upload_path.$this->upload->file_name);

			$data = $this->upload->data();
			
			
		}else
		{ // form or IE8 이하
			if ( ! $this->upload->do_upload($file_element_name))
			{
				$msg = $this->upload->display_errors('', '');
			}
			else
			{
				$data = $this->upload->data();
				
			}
		}
		if($msg==''){
			log_message('debug','--- data : '.print_r($data,TRUE));				
			
			// DB에 가짜파일 하나 삽입하여 임시아이디 받아오기
			$current_time = date('Y-m-d H:i:s');
			if(mb_strlen($data['orig_name'])>100){
				$data['orig_name'] = mb_substr($data['orig_name'], 0, 50, 'utf-8') . '...' . mb_substr($data['orig_name'], -50, 50, 'utf-8');
			}
			$this->db->insert('gi_upload', array(
				'upload_id' => NULL,
				'type' => $category_config['type'],
				'entry_id' => 0,
				'user_id' => USER_ID,
				'created' => $current_time,
				'modified' => $current_time,
				'is_image' => ($data['is_image']==1 ? 'y' : 'n'),
				'filename' => $data['orig_name'],
				'size' => $data['file_size']
			));
			$ins_id = $this->db->insert_id();
			$server_filename = $ins_id.'-'.USER_ID.'-'.substr($current_time,-2);
			
			// 폴더를 하나 생성한다.
			$target_path = $this->upload->upload_path.'/'.substr($ins_id, -2);
			if(!is_dir($target_path)){
				mkdir($target_path, 0777);
			}
			if($data['is_image']==1){
				// fix orientation
				$size = getimagesize($data['full_path']);
				if ($size[2] == 2){
					$source = imagecreatefromjpeg($data['full_path']);
					if(function_exists('exif_read_data')){
						$exif = exif_read_data($data['full_path']);
					    if (!empty($exif['Orientation'])) {
					        switch ($exif['Orientation']) {
					            case 3:
					                $source = imagerotate($source, 180, 0);
					                $modified = true;
					                break;
					            case 6:
					                $source = imagerotate($source, -90, 0);
					                $modified = true;
					                break;
					            case 8:
					                $source = imagerotate($source, 90, 0);
					                $modified = true;
					                break;
					        }
					        if(isset($modified)){
								@imagejpeg($source, $data['full_path'], 100);			        	
					        }
					    }
					    imagedestroy($source);
					}
				}


				// 썸네일 이미지를 생성하기
				$this->load->library('thumbnail');
				$this->thumbnail->createThumb(
					$category_config['thumbnail_width'],
					$category_config['thumbnail_height'],
					$data['full_path'],
					$category_config['thumbnail_style'],
					$target_path.'/'.$server_filename.'_'
				);
			}
			if($data['is_image']==1 && $data['image_width'] > $this->config->item('gi_cont_width')){
				// 이미지의 해상도를 조정해준다.
				$size = getimagesize($data['full_path']);
				if ($size[2] == 1)
					$source = imagecreatefromgif($data['full_path']);
				else if ($size[2] == 2)
					$source = imagecreatefromjpeg($data['full_path']);
				else if ($size[2] == 3)
					$source = imagecreatefrompng($data['full_path']);
				else
					;
				
				if($data['image_width'] > $this->config->item('gi_max_width')){
					$w = $this->config->item('gi_max_width');
					$h = $size[1] * ($this->config->item('gi_max_width')/$size[0]);
					
					$target = @imagecreatetruecolor($w, $h);
					if($size[2] == 3){
						imagesavealpha($target, true); 
						$color = imagecolorallocatealpha($target,0x00,0x00,0x00,127); 
						imagefill($target, 0, 0, $color); 					
					}
					@imagecopyresampled($target, $source, 0, 0, 0, 0, $w, $h, $size[0], $size[1]);
					if ($size[2] == 3)
						@imagepng($target, $target_path.'/'.$server_filename);
					else
						@imagejpeg($target, $target_path.'/'.$server_filename, 90);
					@chmod($target_path.'/'.$server_filename, 0777); // 추후 삭제를 위하여 파일모드 변경
					imagedestroy($target);
				}else{
					copy($data['full_path'], $target_path.'/'.$server_filename);
				}

				$w = $this->config->item('gi_cont_width');
				$h = $size[1] * ($this->config->item('gi_cont_width')/$size[0]);
				
				$target = @imagecreatetruecolor($w, $h);
				if($size[2] == 3){
					imagesavealpha($target, true); 
					$color = imagecolorallocatealpha($target,0x00,0x00,0x00,127); 
					imagefill($target, 0, 0, $color); 					
				}
				@imagecopyresampled($target, $source, 0, 0, 0, 0, $w, $h, $size[0], $size[1]);
				if ($size[2] == 3)
					@imagepng($target, $target_path.'/'.$server_filename.'_cont');
				else
					@imagejpeg($target, $target_path.'/'.$server_filename.'_cont', 90);
				@chmod($target_path.'/'.$server_filename, 0777); // 추후 삭제를 위하여 파일모드 변경
				imagedestroy($source);
				imagedestroy($target);

				@unlink($data['full_path']);
			}else{
				// 파일을 옮겨주기. 
				if($data['is_image']==1)
					copy($data['full_path'], $target_path.'/'.$server_filename.'_cont');
				rename($data['full_path'], $target_path.'/'.$server_filename);
			}
			
			$status = 'success';
			$msg = substr($ins_id, -2).'/'.$server_filename;
		}
		echo json_encode(array('status' => $status, 'msg' => $msg));		
	}
	
	function image($file=null){


		if($file=null){
			$json = array(
				'status' => 'fail',
				'message' => 'not_file_received'
				);
		}
		else{
			$json = array(
				'status' => 'done',
				'upload_id' => 0,
				'filename' => 'QWERTYUIOP1234567890ASDFGHJKL.png'
				);
		}

		$this->layout->set_json($json)->render();
	}
	
}
