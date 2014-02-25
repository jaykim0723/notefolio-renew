<?php
/**
 * Notefolio File Save Management Library
 *
 *
 * @author Yoon, Seongsu(soplel@snooey.net)
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class file_save {
    
    var $last_error = '';
    
    function __construct($config=null) {
        $this->ci =& get_instance();

        $this->ci->load->config('upload', TRUE);
    }

    /**
     * save from uri
     * 
     * @param string $uri
     * @return array/bool-false
     */
    function save_from_url($uri=false, $filename=''){
        $output = array(
          'type' => null,
          'size' => null,
          'name' => null,
          'tmp_name' => null
        );

        if($uri){
            $tmpfile = tmpfile();
            $tmpfile_info = stream_get_meta_data($tmpfile);
            $ch = curl_init($uri);
            $fp = fopen($tmpfile_info['uri'], 'wb');
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_exec($ch);
            curl_close($ch);
            fclose($fp);
            list($width, $height, $type, $attr) = getimagesize($tmpfile_info['uri']);
            $output  = array(
              'type' => $type,
              'size' => null,
              'name' => $filename,
              'tmp_name' => $tmpfile_info['uri']
            );
        }
        
        return $this->save('image', $output);
    }

    /**
     * save file to disk
     * 
     * @param string $type
     * @param array $file
     * @return array/bool-false
     */
    function save($type=false, $file=false){
        if($file){
            $filename = $this->make_filename($type, $file['name']);

            switch($type){
                case "image":
                    $this->make_thumbnail($file['tmp_name'], $filename['path'].$filename['large'], 'large');
                    $this->make_thumbnail($file['tmp_name'], $filename['path'].$filename['medium'], 'medium');
                    $this->make_thumbnail($file['tmp_name'], $filename['path'].$filename['small'], 'small');
                    $this->make_thumbnail($file['tmp_name'], $filename['path'].$filename['wide'], 'wide', array('autocrop'=>true));
                    $this->make_thumbnail($file['tmp_name'], $filename['path'].$filename['single'], 'single', array('autocrop'=>true));
                break;
                case "cover":
                break;
                case "acp":
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
    function make_filename($type=false, $name=false){
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
            $salt = $this->ci->config->item('encryption_key')
                    .'NOTEFOLIO'
                    .microtime()
                    .$this->ci->tank_auth->get_username();
            $hashed_name = hash('sha256', $salt.$o_name);
            $hashed_path = substr($hashed_name, 0, 2).'/'.substr($hashed_name, 2, 2).'/';
        }
        switch($type){
            case 'image':
                $path = $this->ci->config->item('img_upload_path', 'upload');
                $uri  = $this->ci->config->item('img_upload_uri',  'upload');
                $output = array('original' =>$hashed_name.'.'.$ext,
                                'large'    =>$hashed_name.'_v1.jpg',
                                'medium'   =>$hashed_name.'_v2.jpg',
                                'wide'     =>$hashed_name.'_t3.jpg',
                                'single'   =>$hashed_name.'_t2.jpg',
                                'small'    =>$hashed_name.'_t1.jpg',
                                'path'     =>$path.$hashed_path,
                                'uri'      =>$uri.$hashed_path,
                                'ext'      =>($ext!='')?$ext:'jpg'
                                );
            break;
            case 'cover':
                $path = $this->ci->config->item('cover_upload_path', 'upload');
                $uri  = $this->ci->config->item('cover_upload_uri',  'upload');
                $output = array('original' =>$o_name,
                                'wide'     =>$o_name.'_t3.jpg',
                                'single'   =>$o_name.'_t2.jpg',
                                'small'    =>$o_name.'_t1.jpg',
                                'path'     =>$path,
                                'uri'      =>$uri,
                                'ext'      =>($ext!='')?$ext:'jpg'
                                );
            break;
            default:
                $path = $this->ci->config->item('upload_path', 'upload');
                $output = array('original'     =>$name,
                                'path'         =>$path,
                                'ext'          =>($ext!='')?$ext:'jpg'
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
    function make_thumbnail($tmp_name=false, $name=false, $type=false, $opt=array()){
        if($tmp_name){
            $maxsize = $this->ci->config->item('thumbnail_'.$type, 'upload');
            $max_width = $maxsize['max_width'];
            $max_height = $maxsize['max_height'];
            switch($type){
                case "exlarge":
                    $todo = array('resize');
                break;
                case "large":
                    $todo = array('resize');
                break;
                case "medium":
                    $todo = array('resize');
                break;
                case "small":
                    $todo = array('resize');
                    $opt['spanning'] = true;
                    
                    list($o_width, $o_height) = getimagesize($tmp_name);
                    $size_data = $this->get_wh_ratio(array(
                        'width'=>$o_width,
                        'height'=>$o_height
                        ));
                    
                    if($size_data['width']==0){
                        $max_width  = $o_width *($max_height/$o_height);
                    }
                    else if($size_data['height']==0){
                        $max_height = $o_height*($max_width /$o_width );
                    }
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
                $image = new Imagick();
                $image->setResolution(300,300); 
                $image->readImage($tmp_name);

                $format = $image->getImageFormat();
                if ($format == 'GIF') {
                    $image = $image->coalesceImages();
                }

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

                if(in_array('crop', $todo)){
                    // Crop Image. Resize is next block.
                    if(isset($opt['autocrop'])&&$opt['autocrop']){
                        $image_size = array(
                            'width'  => $image->getImageWidth(),
                            'height' => $image->getImageHeight(),
                            );
                        $crop_to = $this->get_auto_crop_opt($image_size, $type);
                    }else{
                        $crop_to = $opt['crop_to'];
                    }
                    
                    if ($format == 'GIF') {
                        foreach ($image as $frame) { 
                            $frame->setImageBackgroundColor('none'); //This is important!
                            $frame->cropImage($crop_to['width'], $crop_to['height'], $crop_to['pos_x'], $crop_to['pos_y']);
                        }
                    }
                    else{
                        $image->cropImage($crop_to['width'], $crop_to['height'], $crop_to['pos_x'], $crop_to['pos_y']);
                    }

                }

                //$image->resampleImage(200,200,imagick::FILTER_LANCZOS,1);

                if(in_array('resize', $todo)){
                    if(($image->getImageWidth() > $max_width)||(isset($opt['spanning'])&&$opt['spanning'])){
                    // Resize image using the lanczos resampling algorithm based on width

                        if ($format == 'GIF') {
                            foreach ($image as $frame) {
                                $frame->setImageBackgroundColor('none'); //This is important!
                                $frame->resizeImage($max_width,$max_height,Imagick::FILTER_LANCZOS,1);
                            }
                        }
                        else{
                            $image->resizeImage($max_width,$max_height,Imagick::FILTER_LANCZOS,1);
                        }
                    }
                }

                if ($format == 'GIF') {
                    $image->setImageBackgroundColor('none'); //This is important!
                    $image = $image->deconstructImages();
                }
                else{
                    //-- transparent background to white
                    $image->setImageBackgroundColor('white'); 
                    $image = $image->flattenImages(); 
                    //-- end

                    // Set Image format n quality
                    $image->setImageFormat((isset($opt['ext'])&&$opt['ext']!='')?$opt['ext']:'jpg');
                    //$image->setImageFormat('jpeg');
                    $image->setImageCompressionQuality((isset($opt['ext'])&&$opt['ext']!='jpg')?0:90);
                    // Clean
                    $image->stripImage();
                }

                // Save
                $image->writeImage($name);
                $image->destroy();
                unset($image);

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
                
                if($size[0]<$max_width)
                    $max_width = $size[0];
                if($size[1]<$max_height)
                    $max_height = $size[1];

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
                
                return true;
            }
        }

        return false;
    }

    /**
     * get auto crop rect opt
     * 
     * @param array $size
     * @param string $type
     * @return array/bool-false
     */
    function get_auto_crop_opt($size=array(), $type=false){
        $maxsize = $this->ci->config->item('thumbnail_'.$type, 'upload');
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
     * @param array $size
     * @param array $crop
     * @return array
     */
    function get_crop_opt($size=array(), $crop=array(), $opt=array()){
        $maxsize = $this->ci->config->item('thumbnail_medium', 'upload'); //기준크기
        if(isset($opt['width'])){
            $max_width = $opt['width'];
        } else {
            $max_width = $maxsize['max_width'];
        }

        //-- get ratio
        $ratio = $size['width']/$max_width;
        if($ratio<1) $ratio = 1;

        return array(
            'width'  =>round($crop['width']*$ratio), 
            'height' =>round($crop['height']*$ratio), 
            'pos_x'  =>round($crop['pos_x']*$ratio), 
            'pos_y'  =>round($crop['pos_y']*$ratio),
            'ratio'  =>$ratio
            );
    }

    /**
     * get width/height ratio
     * 
     * @param array $size
     * @return array/bool-false
     */
    function get_wh_ratio($size=array()){

        //-- get image ratio
        $image_ratio = $size['width']/$size['height'];

        if($image_ratio<1){ //이미지가 기준 가로폭보다 작다
            $size['width'] = 0;
        }
        else if($image_ratio>1){
            $size['height'] = 0;
        }
        else{
        }

        return array('width'=>$size['width'], 'height'=>$size['height'], 'ratio'=>$image_ratio);
    }
}