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
    		'delimiter' => 12,
    		'order_by' => 'newest',
    		'keywords' => '',
    	);
    	foreach($default_params as $key => $value){
    		if(!isset($params->{$key}))
    			$params->{$key} = $value;
    	}

    	$this->db
            ->select('works.*, users.*')
    		// ->select('work_id, title, realname, regdate, keywords, tags, user_id, folder, contents, moddate, hit_cnt, note_cnt, comment_cnt, collect_cnt, ccl, discoverbility')
    		->from('works')
    		->join('users', 'users.id = works.user_id', 'left')
    		->limit($params->delimiter, ((($params->page)-1)*$params->delimiter)); //set

    	switch($params->order_by){
    		case "newest":
    			$this->db->order_by('moddate', 'desc');
    		break;
    		case "oldest":
    			$this->db->order_by('moddate', 'asc');
    		break;
    		default:
    			if(is_array($params->order_by))
    				$this->db->order_by($params->order_by);
    		break;
    	}

    	$data = $this->db->get();

    	$rows = array();
    	foreach ($data->result() as $row)
		{
            // 값을 조작해야할 필요가 있을 때에는 여기에서 한다
            // do stuff
		    $rows[] = $row;
		}
        if(sizeof($rows)==0)
            return NULL;

    	return (object)array(
			'page' => $params->page,
			'rows' => $rows
		);

    }


    function get_info($work_id=''){
    	$this->db
            ->select('works.*, users.*')
    		// ->select('work_id, title, realname as user, regdate, keywords, tags, user_id, folder, contents, moddate, hit_cnt, note_cnt, comment_cnt, collect_cnt, ccl, discoverbility')
    		->from('works')
    		->join('users', 'users.id = works.user_id', 'left')
    		->where('works.work_id', $work_id)
    		->limit(1); //set
        $data = $this->db->get()->row();
        if(!$data)
            return NULL;
        // 값을 조작해야할 필요가 있을 때에는 여기에서 한다
        // do stuff

    	return $data;

    }


    /**
     * 업로드할 때에 해당 유저에 대해서 비어 있는 work_id를 생성한다.
     * @return [type] [description]
     */
    function post_info(){
        $this->db->insert('works', array(
            'work_id' => NULL
        ));
        $work_id = $this->db->insert_id();
        return $this->get_info($work_id);
    }


    function put_info($work_id, $data=array()){

    }
    function delete_info($work_id){

    }

}

/* End of file work_model.php */
/* Location: ./application/models/work_model.php */