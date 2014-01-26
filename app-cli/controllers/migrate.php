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
        $default_cmd = 'php '.$this->input->server('DOCUMENT_ROOT').'../../notefolio-web/app_cli/cli.php migrate';
        $errmsg = 'eAccelerator: Unable to change cache directory /var/cache/eaccelerator permissions';
        
        $cmd = $default_cmd.' user_list';
        
        $response = @json_decode(exec($cmd));
        foreach($response->rows as $key=>$val){
            $cmd = $default_cmd.' user '.$val;

            $data = @json_decode(exec($cmd));
            var_export($data);


        }

        echo PHP_EOL;
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
            $cmd = $default_cmd.' work '.$val;

            $data = @json_decode(exec($cmd));

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

        foreach($old as $keyword){
            foreach($keywordset as $keyword_code=>$keyword_list){
                if(in_array($old, $keyword_list))
                    $new_keyword = $keyword_code;
            }

            $new[] = $new_keyword;
        }

        return implode('', $new);
    }


}

/* End of file migrate.php */
/* Location: ./application/controllers/migrate.php */