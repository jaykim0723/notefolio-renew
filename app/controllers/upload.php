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

		$to_crop = $this->_get_crop_opt($size, $o_crop);

		$result = $this->_make_thumbnail(
			$this->config->item('img_upload_path', 'upload').$filename,
			$this->config->item('profile_upload_path', 'upload').$username.'_face.jpg',
			'profile_face', 
			array('crop_to'=>$to_crop)
			);


		//upload_id=111&x=98&y=0&w=293&h=293
		$json = array(
			'status'=>($result)?'done':'fail',
			'to_crop'=>$o_crop,
			'cropped'=>$to_crop
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
			
			$filename = $this->_save('image', $file);

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
					$this->_make_thumbnail($file['tmp_name'], $filename['path'].$filename['wide'], 'wide', array('autocrop'=>true));
					$this->_make_thumbnail($file['tmp_name'], $filename['path'].$filename['single'], 'single', array('autocrop'=>true));
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
								'wide'     =>$hashed_name.'_t3.jpg',
								'single'   =>$hashed_name.'_t2.jpg',
								'small'    =>$hashed_name.'_t1.jpg',
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
	function _make_thumbnail($tmp_name=false, $name=false, $type=false, $opt=array()){
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
				case "profile_face":
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
					if(isset($opt['autocrop'])&&$opt['autocrop']){
						$image_size = array(
							'width'  => $image->getImageWidth(),
							'height' => $image->getImageHeight(),
							);
						$crop_to = $this->_get_auto_crop_opt($image_size, $type);
					}else{
						$crop_to = $opt['crop_to'];
					}

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
				log_message('debug','--------- getimagezie ( params : '.print_r($size,TRUE)).')';
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
					   //      if(isset($modified)){
								// @imagejpeg($source, $tmp_name, 100);			        	
					   //      }
					    }
					    // imagedestroy($source);
					}
				}else if ($size[2] == 3)
					$source = imagecreatefrompng($tmp_name);
				else
					;

				// Set Image format n quality
				// $max_height 는 임의로 계산을 한다.
				$max_height = floor($size[1] * ($max_width / $size[0]));
				$target = @imagecreatetruecolor($max_width, $max_height); // target 사이즈를 이곳에 도달하기 전에 미리 결정하여야 함
				if($size[2] == 3){
					imagesavealpha($target, true); 
					$color = imagecolorallocatealpha($target,0x00,0x00,0x00,127); 
					imagefill($target, 0, 0, $color); 					
				}
				log_message('debug','--------- imagecopyresampled ( params : '.print_r(array($target, $source, 0, 0, 0, 0, $max_width, $max_height, $size[0], $size[1]),TRUE)).')';
				@imagecopyresampled($target, $source, 0, 0, 0, 0, $max_width, $max_height, $size[0], $size[1]);
				if ($size[2] == 3)
					@imagepng($target, $name);
				else
					@imagejpeg($target, $name, 90);
				@chmod($name, 0777); // 추후 삭제를 위하여 파일모드 변경
				imagedestroy($source);
				imagedestroy($target);
			}
		}

		return false;
	}

	/**
	 * get auto crop rect opt
	 * 
	 * @param Imagck $image
	 * @param string $type
	 * @return array/bool-false
	 */
	function _get_auto_crop_opt($size=array(), $type=false){
		$maxsize = $this->config->item('thumbnail_'.$type, 'upload');
		$max_width = $maxsize['max_width'];
		$max_height = $maxsize['max_height'];

		//-- get TO_MAKE_THUMBNAIL ratio
		$ratio = $max_width/$max_height;

		$width = $size['width'];
		$height = $size['height'];

		//-- get image ratio
		$image_ratio = $width/$height;

		if($ratio>$image_ratio){ //이미지가 기준 가로폭보다 작다
			$crop_width = $width;
			$crop_height = $width / $ratio;
			$pos_x = 0;
			$pos_y = round(($height-$crop_height)/2);
		}
		else if($ratio<$image_ratio){
			$crop_width = $height / $ratio;
			$crop_height = $height;
			$pos_x = round(($width-$crop_width)/2);
			$pos_y = 0;
		}
		else{
			$crop_width = $width;
			$crop_height = $height;
			$pos_x = 0;
			$pos_y = 0;
		}

		return array('width'=>$crop_width, 'height'=>$crop_height, 
			'pos_x'=>$pos_x, 'pos_y'=>$pos_y);
	}

	/**
	 * get crop rect opt
	 * 
	 * @param Imagck $image
	 * @param string $type
	 * @return array/bool-false
	 */
	function _get_crop_opt($size=array(), $crop=array()){
		$maxsize = $this->config->item('thumbnail_medium', 'upload');
		$max_width = $maxsize['max_width'];

		//-- get ratio
		$ratio = $size['width']/$max_width;
		var_export($ratio);


		return array(
			'width'  =>round($crop['width']*$ratio), 
			'height' =>round($crop['height']*$ratio), 
			'pos_x'  =>round($crop['pos_x']*$ratio), 
			'pos_y'  =>round($crop['pos_y']*$ratio)
			);
	}
	
}
