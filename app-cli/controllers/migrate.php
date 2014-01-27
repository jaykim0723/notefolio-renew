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

            $data->keyword = $this->convert_keyword($data->keyword);
            //var_export($data);
            echo('User ID "'.$data->user_id.'" - Working');

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
	public function get_work(){
        $default_cmd = 'php '.$this->input->server('DOCUMENT_ROOT').'../../notefolio-web/app_cli/cli.php migrate';
        $errmsg = 'eAccelerator: Unable to change cache directory /var/cache/eaccelerator permissions';
        
        $cmd = $default_cmd.' work_list';
        
        $response = @json_decode(exec($cmd));
        foreach($response->rows as $key=>$val){
            $cmd = $default_cmd.' work '.$val->id;

            $data = @json_decode(exec($cmd));

            $data->keyword = $this->convert_keyword($data->keyword);

        }

        echo PHP_EOL;
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


}

/* End of file migrate.php */
/* Location: ./application/controllers/migrate.php */