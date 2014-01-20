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
	}
	
	/**
	 * get image and do process
	 * 
	 * @param file $file
	 * @return no retun
	 */
	function image($file=null){
		if ($filename = $this->input->get_post('qqfile')) {
		    // XMLHttpRequest stream'd upload

		    include_once(APPPATH.'libraries/qqUploadedFileXhr.php');

		    $xhrUpload = new qqUploadedFileXhr();
		    $file = $xhrUpload->makeTempFile()->toFileArray();
		} elseif (count($_FILES)) {
		    // Normal file upload
		    //$file = array_shift($_FILES);
			$file = $_FILES['qqfile'];
		} else {
		    throw new Exception("Did not receive uploaded file.");
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
			
			$filename = $this->_save('image', $file);
		exit(var_export($filename,true));
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
	        	'fileurl' => $filename['uri'],
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

	/**
	 * save file to disk
	 * 
	 * @param string $type
	 * @param file $file
	 * @return array/bool-false
	 */
	function _save($type=false, $file=false){
		if($file){
			$filename = $this->_make_filename($type, $file['name']);

			switch($type){
				case "image":
					$this->_make_thumbnail($file['tmp_name'], $filename['path'].$filename['large'], 'large');
					$this->_make_thumbnail($file['tmp_name'], $filename['path'].$filename['medium'], 'medium');
				break;
				case "cover":
				break;
				default:
				break;
			}

			$output = (rename(
								$file['tmp_name'], 
								$filename['path'].$filename['original']))?
							$filename : false;
		}

		return $output;
	}

	/**
	 * make file name using hash(sha256)
	 * 
	 * @param string $type
	 * @param string $name
	 * @return array
	 */
	function _make_filename($type=false, $name=false){
		if($name){
			$path_text = pathinfo($name);
			$o_name = $path_text['filename'];
			$ext = strtolower($path_text['extension']);
		}
		else {
			$o_name = '';
			$ext = '';
		}
		if(in_array($type, array('image'))){
			$salt = $this->config->item('encryption_key')
					.'NOTEFOLIO'
					.microtime()
					.$this->tank_auth->get_username();
			$hashed_name = hash('sha256', $salt.$o_name);
			$hashed_path = substr($hashed_name, 0, 2).'/'.substr($hashed_name, 2, 2).'/';
		}
		switch($type){
			case 'image':
				$path = $this->config->item('img_upload_path', 'upload');
				$uri  = $this->config->item('img_upload_uri',  'upload');
				$output = array('original' =>$hashed_name.'.'.$ext,
								'large'    =>$hashed_name.'_v1.jpg',
								'medium'   =>$hashed_name.'_v2.jpg',
								'path'     =>$path.$hashed_path,
								'uri'      =>$uri.$hashed_path,
								'ext'	   =>($ext!='')?$ext:'jpg'
								);
			break;
			case 'cover':
				$path = $this->config->item('cover_upload_path', 'upload');
				$uri  = $this->config->item('cover_upload_uri',  'upload');
				$output = array('original' =>$o_name,
								'wide'     =>$o_name.'_t3.jpg',
								'single'   =>$o_name.'_t2.jpg',
								'small'    =>$o_name.'_t1.jpg',
								'path'     =>$path,
								'uri'      =>$uri,
								'ext'	   =>($ext!='')?$ext:'jpg'
								);
			break;
			default:
				$path = $this->config->item('upload_path', 'upload');
				$output = array('original'	   =>$name,
								'path'	       =>$path,
								'ext'		   =>($ext!='')?$ext:'jpg'
									);
			break;
		}
		
		if(!is_dir($output['path'])) //create the folder if it's not already exists
		{
			mkdir($output['path'],0777,TRUE);
		}

		return $output;
	}

	/**
	 * make thumbnail
	 * 
	 * @param file $file
	 * @param string $name
	 * @param string $type
	 * @param array $opt
	 * @return array
	 */
	function _make_thumbnail($tmp_name='', $name=false, $type=false, $opt=array()){
		if($tmp_name){
			$maxsize = $this->config->item('thumbnail_'.$type, 'upload');
			$max_width = $maxsize['max_width'];
			$max_height = $maxsize['max_height'];
			switch($type){
				case "large":
					$todo = array('resize');
				break;
				case "medium":
					$todo = array('resize');
				break;
				case "wide":
					$todo = array('resize', 'crop');
				break;
				case "single":
					$todo = array('resize', 'crop');
				break;
				default:
				break;
			}


			if(class_exists('Imagick')){
				// assign ImageMagick
				$image = new Imagick($tmp_name);
				//$image->setImageColorspace(Imagick::COLORSPACE_SRGB); // color is inverted
				if ($image->getImageColorspace() == Imagick::COLORSPACE_CMYK) { 
				    $profiles = $image->getImageProfiles('*', false); 
				    // we're only interested if ICC profile(s) exist 
				    $has_icc_profile = (array_search('icc', $profiles) !== false);
				    // if it doesnt have a CMYK ICC profile, we add one 
				    if ($has_icc_profile === false) { 
				        $icc_cmyk = file_get_contents(APPPATH.'libraries/colorspace/USWebUncoated.icc');  
				        $image->profileImage('icc', $icc_cmyk); 
				        unset($icc_cmyk); 
				    } 
				    // then we add an RGB profile 
				    $icc_rgb = file_get_contents(APPPATH.'libraries/colorspace/sRGB_v4_ICC_preference.icc'); 
				    $image->profileImage('icc', $icc_rgb); 
				    unset($icc_rgb); 
				}

		    	$image->resampleImage(150,150,imagick::FILTER_LANCZOS,1);

				if(in_array('crop', $todo)){
					// Crop Image. Resize is next block.
					$crop_to = $opt['crop_to'];
					$image->cropImage($crop_to['width'], $crop_to['height'], $crop_to['pos_x'], $crop_to['pos_y']);
				}

				if(in_array('resize', $todo)){
			    	if($image->getImageWidth() > $max_width){
					// Resize image using the lanczos resampling algorithm based on width
						$image->resizeImage($max_width,$max_height,Imagick::FILTER_LANCZOS,1);
					}
				}

				// Set Image format n quality
				$image->setImageFormat((isset($opt['ext'])&&$opt['ext']!='')?$opt['ext']:'jpg');
				//$image->setImageFormat('jpeg');
	        	$image->setImageCompressionQuality((isset($opt['ext'])&&$opt['ext']!='jpg')?0:90);
				
				// Clean & Save
				$image->stripImage();
				$image->writeImage($name);
				$image->destroy();

				return true;
			}
			else {

				// GD 라이브러리를 이용하여 고전적인 방법으로 생성한다.
				$size = getimagesize($tmp_name);

				if ($size[2] == 1)
					$source = imagecreatefromgif($tmp_name);
				else if ($size[2] == 2){
					$source = imagecreatefromjpeg($tmp_name);
					if(function_exists('exif_read_data')){
						$exif = exif_read_data($tmp_name);
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
								@imagejpeg($source, $tmp_name, 100);			        	
					        }
					    }
					    imagedestroy($source);
					}
				}else if ($size[2] == 3)
					$source = imagecreatefrompng($tmp_name);
				else
					;

				// Set Image format n quality
				$target = @imagecreatetruecolor($w, $h); // target 사이즈를 이곳에 도달하기 전에 미리 결정하여야 함
				if($size[2] == 3){
					imagesavealpha($target, true); 
					$color = imagecolorallocatealpha($target,0x00,0x00,0x00,127); 
					imagefill($target, 0, 0, $color); 					
				}
				@imagecopyresampled($target, $source, 0, 0, 0, 0, $w, $h, $size[0], $size[1]);
				if ($size[2] == 3)
					@imagepng($name);
				else
					@imagejpeg($name, 90);
				@chmod($name, 0777); // 추후 삭제를 위하여 파일모드 변경
				imagedestroy($source);
				imagedestroy($target);
			}
		}

		return false;
	}
		//echo json_encode(array('status' => $status, 'msg' => $msg, 'upload_id' => '', 'src' =>'', 'org_filename' => '' ));		
	
}
