<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class work_model extends CI_Model {


    public function __construct()
    {
        parent::__construct();
        
    }

    function get_list($params=array()){
    	$params = (object)$params;
    	$default_params = (object)array(
    		'page' => 1,
    		'order_by' => 'newest',
    		'keywords' => '',
    	);
    	foreach($default_params as $key => $value){
    		if(!isset($params->{$key}))
    			$params->{$key} = $value;
    	}

		/* 가짜 data를 생성, 모델과 연결하여야 함 */
		$data = (object)array(
			'page' => $params->page,
			'rows' => array()
		);
		for($i=0; $i<12; $i++){
			$data->rows[] = array(
				'work_id' => 1,
				'title' => 'Lorem Ipsum',
				'user' => (object)array(
					'realname' => '정미나',
					'hit_cnt' => rand(0,234),
					'comment_cnt' => rand(0,234),
					'like_cnt' => rand(0,234)
				)
			);
		}
		// 가짜 데이터 끝
		return $data;
    }


    function get_info($work_id=''){
    	$result = $this->db
    		->select('works.id as work_id, title, realname as user, regdate, keywords, tags, user_id, folder, contents, moddate, hit_cnt, note_cnt, comment_cnt, collect_cnt, ccl, discoverbility')
    		->join('users', 'users.id = works.user_id', 'left')
    		->get_where('works', array('works.id' => $work_id), 0, 1)->result(); //set table

    	return $result;

		// 현재는 가짜, work info model에서 가지고 와야함
		/*$data = (object)array(
			'row' => (object)array(
				'work_id' => 1,
				'title' => 'aonethun',
				'user' => '',
				'regdate' => '',
				'keywords' => '',
				'tags' => '',
				'user_id' => '',
				'folder' => '',
				'contents' => array(
				),
				'moddate' => '',
				'hit_cnt' => '',
				'note_cnt' => '',
				'collect_cnt' => '',
				'comment_cnt' => '',
				'ccl' => '',
				'discoverbility' => ''
			)
		);
		return $data;*/
    }


    function post_info($data=array()){

    }
    function put_info($work_id, $data=array()){

    }
    function delete_info($work_id){

    }

}

/* End of file work_model.php */
/* Location: ./application/models/work_model.php */