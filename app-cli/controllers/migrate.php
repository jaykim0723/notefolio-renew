<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @brief migrate(original) Controller
 *
 * @author Yoon, Seongsu(soplel@snooey.net)
 */
 
class migrate extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 */
	public function index($to='null')
	{
		echo "Hello {$to}!".PHP_EOL;
	}
    
	/**
	 * migrate info for user list
	 *
	 * @param int $user_id
	 */
	public function get_user(){
        $this->load->config('upload', TRUE);
        $this->load->model('upload_model');
        $this->load->library('file_save');

        $default_cmd = 'php '.$this->input->server('DOCUMENT_ROOT').'../../notefolio-web/app_cli/cli.php migrate';
        $errmsg = 'eAccelerator: Unable to change cache directory /var/cache/eaccelerator permissions';
        
        $cmd = $default_cmd.' user_list';

        $this->load->database();

        //$this->db->trans_start();

        $sql = "TRUNCATE `users`;";
        $this->db->query($sql);
        $sql = "TRUNCATE `user_profiles`;";
        $this->db->query($sql);
        $sql = "TRUNCATE `user_sns_fb`;";
        $this->db->query($sql);
        $sql = "TRUNCATE `user_follows`;";
        $this->db->query($sql);
        
        $response = @json_decode(exec($cmd));
        foreach($response->rows as $key=>$val){
            $cmd = $default_cmd.' user '.$val->id;

            $data = @json_decode(exec($cmd));

            echo('User ID "'.$data->user_id.'" - Migrating');
            $data->keyword = $this->convert_keyword($data->keyword);
            echo('.');
            //var_export($data);

            $sql = "INSERT INTO `users`
                (`id`,
                `username`,
                `password`,
                `email`,
                `level`,
                `activated`,
                `banned`,
                `ban_reason`,
                `realname`,
                `new_password_key`,
                `new_password_requested`,
                `new_email`,
                `new_email_key`,
                `last_ip`,
                `last_login`,
                `created`,
                `modified`)
                VALUES
                (".$this->db->escape($data->info->id).",
                ".$this->db->escape($data->info->username).",
                ".$this->db->escape($data->info->password).",
                ".$this->db->escape($data->info->email).",
                ".$this->db->escape($data->info->level).",
                ".$this->db->escape($data->info->activated).",
                ".$this->db->escape($data->info->banned).",
                ".$this->db->escape($data->info->ban_reason).",
                ".$this->db->escape($data->info->realname).",
                ".$this->db->escape($data->info->new_password_key).",
                ".$this->db->escape($data->info->new_password_requested).",
                ".$this->db->escape($data->info->new_email).",
                ".$this->db->escape($data->info->new_email_key).",
                ".$this->db->escape($data->info->last_ip).",
                ".$this->db->escape($data->info->last_login).",
                ".$this->db->escape($data->info->created).",
                ".$this->db->escape($data->info->modified).");";
            $this->db->query($sql);
            echo('.');

            $sql = "INSERT INTO `user_profiles`
                (`id`,
                `user_id`,
                `keywords`,
                `location`,
                `website`,
                `facebook_id`,
                `twitter_id`,
                `gender`,
                `phone`,
                `birth`,
                `description`,
                `mailing`,
                `following_cnt`,
                `follower_cnt`,
                `moddate`,
                `regdate`,
                `point`)
                VALUES
                (".$this->db->escape($data->info->id).",
                ".$this->db->escape($data->info->user_id).",
                ".$this->db->escape($data->keyword).",
                ".$this->db->escape($data->info->location).",
                ".$this->db->escape($data->info->website).",
                ".$this->db->escape($data->info->facebook_id).",
                ".$this->db->escape($data->info->twitter_id).",
                ".$this->db->escape($data->info->gender).",
                ".$this->db->escape($data->info->phone).",
                ".$this->db->escape($data->info->birth).",
                ".$this->db->escape($data->info->description).",
                ".$this->db->escape($data->info->mailing).",
                ".$this->db->escape($data->info->following_cnt).",
                ".$this->db->escape($data->info->follower_cnt).",
                ".$this->db->escape($data->info->moddate).",
                ".$this->db->escape($data->info->regdate).",
                ".$this->db->escape($data->info->point).");
                ";
            $this->db->query($sql);
            echo('.');

            $sql = "INSERT INTO `user_about`
                (`user_id`,
                `contents`,
                `moddate`)
                VALUES
                (".$this->db->escape($data->info->id).",
                ".$this->db->escape($data->info->description).",
                ".$this->db->escape($data->info->moddate).");
                ";
            $this->db->query($sql);
            echo('.');

            if(count($data->sns_fb)>0){
                $sql = "INSERT INTO `notefolio-renew`.`user_sns_fb`
                        (`id`,
                        `fb_num_id`,
                        `access_token`,
                        `post_work`,
                        `post_comment`,
                        `post_note`,
                        `regdate`,
                        `moddate`)
                        VALUES
                        (".$this->db->escape($data->user_id).",
                        ".$this->db->escape($data->sns_fb->fb_num_id).",
                        ".$this->db->escape($data->sns_fb->access_token).",
                        ".$this->db->escape($data->sns_fb->post_work).",
                        ".$this->db->escape($data->sns_fb->post_comment).",
                        ".$this->db->escape($data->sns_fb->post_note).",
                        ".$this->db->escape($data->sns_fb->regdate).",
                        ".$this->db->escape($data->sns_fb->moddate).");
                        ";
                $this->db->query($sql);
            }
            echo('.');

            $sql = '';
            foreach($data->follow as $param){
                $sql .= (empty($sql)?'':',')."
                    (".$this->db->escape($data->user_id).",
                    ".$this->db->escape($param->follow_id).",
                    ".$this->db->escape($param->regdate).")
                    ";
            }
            if(!empty($sql)){
                $sql = "INSERT INTO `user_follows`
                    (`follower_id`,
                    `follow_id`,
                    `regdate`)
                    VALUES ".$sql.";";
                $this->db->query($sql);
            }
            echo('.');

            $sql = '';
            foreach($data->collect as $param){
                $sql .= (empty($sql)?'':',')."
                    (".$this->db->escape($data->user_id).",
                    ".$this->db->escape($param->work_id).",
                    ".$this->db->escape($param->comment).",
                    ".$this->db->escape($param->regdate).")
                    ";
            }
            if(!empty($sql)){
                $sql = "INSERT INTO `user_work_collect`
                    (`user_id`,
                    `work_id`,
                    `comment`,
                    `regdate`)
                    VALUES ".$sql.";";
                $this->db->query($sql);
            }
            echo('.');
            
            if(!empty($data->pic)){
                $filename = $data->pic;
    
                $this->file_save->make_thumbnail(
                    $filename,
                    $this->config->item('profile_upload_path', 'upload').$data->info->username.'_face.jpg',
                    'profile_face', 
                    array('crop_to'=>array( 'width'  => 100, 'height' => 100, 'pos_x'  => 0, 'pos_y'  => 0), 'spanning'=>true)
                    );
            }
            echo('.');

            echo(' done.'.PHP_EOL);
    
                    //$sql = "INSERT INTO table (title) VALUES(".$this->db->escape($title).")";
                    //$this->db->query($sql);

        }
        //$this->db->trans_complete();

        echo $this->db->trans_status().PHP_EOL;
	}

	/**
	 * migrate info for work list
	 *
	 */
	public function get_work($start=0){
        $this->load->config('upload', TRUE);
        $this->load->model('upload_model');
        $this->load->library('file_save');

        ignore_user_abort(true);
        set_time_limit(0);

        $default_cmd = 'php '.$this->input->server('DOCUMENT_ROOT').'../../notefolio-web/app_cli/cli.php migrate';
        $errmsg = 'eAccelerator: Unable to change cache directory /var/cache/eaccelerator permissions';
        
        $cmd = $default_cmd.' work_list';

        $this->load->database();

        //$this->db->trans_start();
        if($start==0){
            $this->clear_work();
        }

        $response = @json_decode(exec($cmd));
        foreach($response->rows as $key=>$val){
            if($start>0 && $val->id!=$start){
                echo('Work ID "'.$val->id.'" - skip'.PHP_EOL);
                continue;
            } else{
                unset($start);
                echo('Work ID "'.$val->id.'" - Migrating');
            }


            $this->get_work_one($val->id);

            echo(' done.'.PHP_EOL);
    
                    //$sql = "INSERT INTO table (title) VALUES(".$this->db->escape($title).")";
                    //$this->db->query($sql);

        }
        //$this->db->trans_complete();

        echo $this->db->trans_status().PHP_EOL;
	}

    public function clear_work(){
        $this->load->database();

        $sql = "TRUNCATE `works`;";
        $this->db->query($sql);
        $sql = "TRUNCATE `work_comments`;";
        $this->db->query($sql);
        $sql = "TRUNCATE `work_tags`;";
        $this->db->query($sql);
        $sql = "TRUNCATE `log_work_view`;";
        $this->db->query($sql);
        $sql = "TRUNCATE `log_work_note`;";
        $this->db->query($sql);
        $sql = "TRUNCATE `uploads`;";
        $this->db->query($sql);

        $default_cmd = 'rm -rf '.$this->input->server('DOCUMENT_ROOT').'../www/data/img/*';
        
        $cmd = $default_cmd;
        
        $response = @json_decode(exec($cmd));
    }

    public function get_work_one($work_id){
        $this->load->config('upload', TRUE);
        $this->load->model('upload_model');
        $this->load->library('file_save');

        $default_cmd = 'php '.$this->input->server('DOCUMENT_ROOT').'../../notefolio-web/app_cli/cli.php migrate';
        $errmsg = 'eAccelerator: Unable to change cache directory /var/cache/eaccelerator permissions';
        
        $cmd = $default_cmd.' work '.$work_id;

        $this->load->database();

        $data = @json_decode(exec($cmd));

        echo('Work ID "'.$data->work_id.'" - Migrating');
        $data->keyword = $this->convert_keyword($data->keyword);
        echo('.');

        if(!empty($data->tag)){
            $data->tags = $this->convert_tags($data->tag);
        }
        echo('.');
        
        if(!empty($data->content)){
            $data->content = $this->convert_content($data->work_id, $data->info->user_id, $data->content);
        }
        echo('.');
        $data->info->ccl = $this->convert_license($data->info->license);
        echo('.');

        $sql = "INSERT INTO `notefolio-renew`.`works`
            (`work_id`,
            `regdate`,
            `status`,
            `keywords`,
            `title`,
            `tags`,
            `user_id`,
            `folder`,
            `moddate`,
            `contents`,
            `nofol_rank`,
            `hit_cnt`,
            `note_cnt`,
            `collect_cnt`,
            `comment_cnt`,
            `ccl`,
            `discoverbility`)
            VALUES
            (".$this->db->escape($data->work_id).",
            ".$this->db->escape($data->info->regdate).",
            'enabled',
            ".$this->db->escape($data->keyword).",
            ".$this->db->escape($data->info->title).",
            ".$this->db->escape($data->tags).",
            ".$this->db->escape($data->info->user_id).",
            '',
            ".$this->db->escape($data->info->moddate).",
            ".$this->db->escape(serialize($data->content)).",
            0,
            ".$this->db->escape($data->count->hit_cnt).",
            ".$this->db->escape($data->count->note_cnt).",
            ".$this->db->escape($data->count->collect_cnt).",
            ".$this->db->escape($data->count->comment_cnt).",
            ".$this->db->escape($data->info->ccl).",
            100);";
        $this->db->query($sql);
        echo('.');

        $sql = '';
        foreach($data->tag as $param){
            $sql .= (empty($sql)?'':',')."
                (".$this->db->escape($data->work_id).",
                ".$this->db->escape($param->text).")
                ";
        }
        if(!empty($sql)){
            $sql = "INSERT INTO `work_tags`
                (`work_id`,
                `text`)
                VALUES ".$sql.";";
            $this->db->query($sql);
        }
        echo('.');

        $sql = '';
        foreach($data->comment as $param){
            $sql .= (empty($sql)?'':',')."
                (".$this->db->escape($param->id).",
                ".$this->db->escape($data->work_id).",
                ".$this->db->escape($param->parent_id).",
                ".$this->db->escape($param->user_id).",
                ".$this->db->escape(trim(preg_replace(
                                    array('/&amp;/', '/&lt;/', '/&gt;/', '/&quot;/', '/&nbsp;/'), 
                                    array('&', '<', '>', '"', ' '), 
                                    $param->content
                                ))).",
                ".$this->db->escape($param->regdate).",
                ".$this->db->escape($param->moddate).",
                0)
                ";
        }
        if(!empty($sql)){
            $sql = "INSERT INTO `work_comments`
                (`id`,
                `work_id`,
                `parent_id`,
                `user_id`,
                `content`,
                `regdate`,
                `moddate`,
                `children_cnt`)
                VALUES ".$sql.";";
            $this->db->query($sql);
            $sql = "UPDATE `work_comments` as o
                        inner join (
                            select parent_id, ifnull(count(*),0) as count
                                from `work_comments` as s
                                where `work_id` = {$this->db->escape($data->work_id)}
                                and parent_id != 0
                                group by parent_id
                            ) as c on o.id = c.parent_id
                set o.`children_cnt` = c.count
                where `work_id` = {$this->db->escape($data->work_id)}
                and o.parent_id = 0;";
            $this->db->query($sql);
        }
        echo('.');

        $sql = '';
        foreach($data->view as $param){
            $sql .= (empty($sql)?'':',')."
                (".$this->db->escape($data->work_id).",
                ".$this->db->escape($param->user_id).",
                ".$this->db->escape($param->remote_addr).",
                'migrate',
                ".$this->db->escape($param->regdate).",
                0)
                ";
        }
        if(!empty($sql)){
            $sql = "INSERT INTO `log_work_view`
                (`work_id`,
                `user_id`,
                `remote_addr`,
                `phpsessid`,
                `regdate`)
                VALUES ".$sql.";";
            $this->db->query($sql);
        }
        echo('.');

        $sql = '';
        foreach($data->note as $param){
            $sql .= (empty($sql)?'':',')."
                (".$this->db->escape($data->work_id).",
                ".$this->db->escape($param->user_id).",
                ".$this->db->escape($param->remote_addr).",
                'migrate',
                ".$this->db->escape($param->regdate).",
                0)
                ";
        }
        if(!empty($sql)){
            $sql = "INSERT INTO `log_work_note`
                (`work_id`,
                `user_id`,
                `remote_addr`,
                `phpsessid`,
                `regdate`)
                VALUES ".$sql.";";
            $this->db->query($sql);
        }
        echo('.');

        if(!empty($data->pic)){
            $filename = $data->pic;

            list($width, $height) = getimagesize($filename);

            $size = array('width'=> $width, 'height'=> $height);
            
            $crop_param_t2 = $this->input->get_post('t2');
            $crop_param_t3 = $this->input->get_post('t3');

            $result_t1 = $this->file_save->make_thumbnail(
                $filename,
                $this->config->item('cover_upload_path', 'upload').$data->work_id.'_t1.jpg', 'small');
            $result_t2 = $this->file_save->make_thumbnail(
                $filename,
                $this->config->item('cover_upload_path', 'upload').$data->work_id.'_t2.jpg', 'single',
                array('autocrop'=>true, 'spanning'=>true));
            $result_t3 = $this->file_save->make_thumbnail(
                $filename,
                $this->config->item('cover_upload_path', 'upload').$data->work_id.'_t3.jpg', 'wide', 
                array('autocrop'=>true, 'spanning'=>true));

        }
        
        echo(' done.'.PHP_EOL);
    }

    /**
     * convert keyword to new keyword
     *
     */
    public function convert_keyword($old){
        $new = array();

        $keywordset = array(
                'A7'  //'공예'
                    => array(
                        'pottery'              , //'도예/유리',
                        'handicraft'           , //'공예',
                        'formative_art_design' , //'조형디자인',
                    ),
                'B7' //'디지털 아트'
                    => array(
                        'graphic_design'       , //'그래픽디자인',
                        'digital_art'          , //'디지털아트',
                        'typography_photo'     , //'타이포그라피',
                    ),
                'C7' //'영상/모션그래픽'
                    => array(
                        'motion_graphic'       , //'모션그래픽',
                        'cinematography'       , //'시네마토그라피',
                        'animation'            , //'애니메이션',
                        'actual_film'          , //'실사영상',
                        '3d'                   , //'3D',
                    ),
                'D7' //'브랜딩/편집'
                    => array(
                        'editorial_design'     , //'편집디자인',
                        'brand_design'         , //'브랜드디자인',
                        'advertising'          , //'광고',
                        'package_design'       , //'패키지디자인',
                    ),
                'E7' //'산업디자인'
                    => array(
                        'furniture_design'     , //'가구디자인',
                        'industrial_design'    , //'산업디자인',
                        'interior_design'      , //'실내디자인',
                        'product_design'       , //'제품디자인',
                        'architect_design'     , //'건축디자인',
                        'metal_design'         , //'금속디자인',
                        'textile_design'       , //'섬유디자인',
                        'fashion_design'       , //'패션디자인',
                    ),
                'F7' //'UI/UX'
                    => array(
                        'web_design'           , //'웹디자인',
                        'ui_ux'                , //'UI/UX',
                    ),
                'G7' //'일러스트레이션'
                    => array(
                        'character_design'     , //'캐릭터디자인',
                        'illustration'         , //'일러스트',
                    ),
                'H7' //'파인아트'
                    => array(
                        'painting'             , //'페인팅',
                        'installation'         , //'설치',
                        'fine_art'             , //'파인아트',
                        'drawing'              , //'드로잉',
                        'caligraphy'           , //'캘리그라피',
                    ),
                'I7' //'포토그래피'
                    => array(
                        'photography'          , //'포토그래피',
                    ),
            );

        foreach($old as $val){
            foreach($keywordset as $keyword_code=>$keyword_list){
                if(in_array($val->category, $keyword_list))
                    $new_keyword = $keyword_code;
            }

            $new[] = $new_keyword;
        }
        sort($new);
        $new = array_unique($new);

        return implode('', $new);
    }

    /**
     * convert tags array to string(seperated by comma)
     *
     */
    public function convert_tags($old){
        $new = array();

        foreach($old as $val){
            $new[] = $val->text;
        }
        $new = array_unique($new);

        return implode(',', $new);
    }

    /**
     * convert license array to string(seperated by -)
     *
     */
    public function convert_license($old){
        $new = array();

        if(!is_array($old)){
            $old = array($old);
        }

        if($old[0]=="y"){
            $new[0]="BY";
        }
        else{
            return 'x';
        }

        if($old[1]=="n"){
            $new[]="NC";
        }

        switch($old[2]){

            case 2:
                $new[]="SA";
                break;
            case 1:
                $new[]="ND";
                break;
            case 0:
            default;
                break;
        }

        return implode('-', $new);
    }

    /**
     * convert contents for new system
     *
     */
    public function convert_content($work_id, $user_id, $old){
        $new = array();

        foreach($old as $val){
            if(empty($val)) continue;

            $data = array('t'=>$val->type);
            switch($val->type){
                case "video":
                    $val->content = explode('|', $val->content);
                    if(is_array($val->content)){
                        $val->content = $val->content[0];
                    }
                case "text":
                    $data['c'] = trim(preg_replace(
                                    array('/&amp;/', '/&lt;/', '/&gt;/', '/&quot;/', '/&nbsp;/'), 
                                    array('&', '<', '>', '"', ' '), 
                                    $val->content
                                )); //-- tag...
                break;
                case "image":
                    $path = '/home/web/notefolio-web/www/img/'
                        .date('ym', strtotime($val->regdate)).'/'.$val->id.'_r';
                    if(file_exists($path)){
                        $result = $this->migrate_image($work_id, $user_id, $path, $val->filename, $val->filesize);
                        echo('*');
    
                        $data['i'] = $result['upload_id'];
                        $data['c'] = $result['src'];
                    }
                    else{
                        unset($data);
                        continue;
                    }
                break;
                default:
                    continue;
                break;
            }
            $new[] = (object)$data; //-- object
        }

        return $new;
    }

    /**
     * migrate image (get->upload->return)
     *
     */
    public function migrate_image($work_id, $user_id, $path, $org_filename, $filesize){
        $this->load->config('upload', TRUE);
        $this->load->model('upload_model');
        $this->load->library('file_save');
            
        $filename = $this->make_filename('image', urlencode($org_filename));

        $this->file_save->make_thumbnail(
            $path,
            $filename['path'].$filename['large'],
            'large' );
        $this->file_save->make_thumbnail(
            $path,
            $filename['path'].$filename['medium'],
            'medium');
        $this->file_save->make_thumbnail(
            $path,
            $filename['path'].$filename['small'],
            'small' );
        $this->file_save->make_thumbnail(
            $path,
            $filename['path'].$filename['wide'],
            'wide',
            array('autocrop'=>true));
        $this->file_save->make_thumbnail(
            $path,
            $filename['path'].$filename['single'],
            'single',
            array('autocrop'=>true));

        copy($path, $filename['path'].$filename['original']);

        $upload_id = $this->upload_model->post(array(
            'user_id' => $user_id,
            'work_id' => $work_id,
            'type' => 'work',
            'filename' => $filename['original'],
            'org_filename' => $org_filename,
            'filesize' => $filesize,
            'comment' => ''
        ));

        $output = array(
            'status' => 'done',
            'message'   => 'successed',
            'upload_id' => $upload_id,
            'src' => $filename['uri'].$filename['medium'],
            'org_filename' => $org_filename,
            'data' => $this->upload_model->get(array('id'=>$upload_id))->row
            );

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
            $salt = $this->config->item('encryption_key')
                    .'NOTEFOLIO'
                    .microtime();
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
                                'ext'      =>($ext!='')?$ext:'jpg'
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
                                'ext'      =>($ext!='')?$ext:'jpg'
                                );
            break;
            default:
                $path = $this->config->item('upload_path', 'upload');
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

}

/* End of file migrate.php */
/* Location: ./application/controllers/migrate.php */