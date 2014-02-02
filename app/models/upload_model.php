<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class upload_model extends CI_Model {


    public function __construct()
    {
        parent::__construct();
        
    }

    /**
     * 리스트를 불러온다.
     * @param  array  $params 
     * @return object          상태와 데이터값을 반환한다
     */
    function get_list($params=array()){
    	$params = (object)$params;
    	$default_params = (object)array(
            'page'      => 1, // 불러올 페이지
            'delimiter' => 24, // 한 페이지당 작품 수
            'order_by'  => 'newest', // newest, oldest
            'user_id'   => '' // 프로필 등 특정 작가의 작품만/을 조회할 때
    	);
    	foreach($default_params as $key => $value){
    		if(!isset($params->{$key}))
    			$params->{$key} = $value;
    	}

    	$this->db
            ->select('uploads.*, users.id, users.username, users.email, users.level, users.realname, users.last_ip, users.last_login, users.created, users.modified')
    		// ->select('upload_id, title, realname, regdate, keywords, tags, user_id, folder, contents, moddate, hit_cnt, note_cnt, comment_cnt, collect_cnt, ccl, discoverbility')
    		->from('uploads')
    		->join('users', 'users.id = uploads.user_id', 'left')
    		->limit($params->delimiter, ((($params->page)-1)*$params->delimiter)); //set

        if(!empty($params->user_id))
            $this->db->where('user_id', $params->user_id);
        
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

    	$uploads = $this->db->get();

    	$rows = array();
    	foreach ($uploads->result() as $row)
		{
            // 값을 조작해야할 필요가 있을 때에는 여기에서 한다
            $user = (object)array(
                'id'         => $row->id,
                'username'   => $row->username,
                'email'      => $row->email,
                'level'      => $row->level,
                'realname'   => $row->realname,
                'last_ip'    => $row->last_ip,
                'last_login' => $row->last_login,
                'created'    => $row->created,
                'modified'   => $row->modified
            );
            foreach($user as $key=>$value){
                unset($row->{$key});
            }
            $row->user = $user;
		    $rows[] = $row;
		}
        $data = (object)array(
            'status' => 'done',
            'page'   => $params->page,
            'rows'   => $rows
        );
        if(sizeof($rows)==0){
            $data->status = 'fail';
            return $data;
        }
        return $data;
    }

    /**
     * 정보를 불러들인다.
     * @param  array $params   (id, folder)
     * @return object          상태와 데이터값을 반환한다
     */
    function get($params=array()){
        $params = (object)$params;
        $default_params = (object)array(
            'id' => ''
        );
        foreach($default_params as $key => $value){
            if(!isset($params->{$key}))
                $params->{$key} = $value;
        }

    	$this->db
            ->select('*')
    		->from('uploads')
    		->where('uploads.id', $params->id)
    		->limit(1); //set
        $upload = $this->db->get()->row();
        $data = (object)array(
            'status' => 'done',
            'row' => $upload
        );
        
    	return $data;
    }


    /**
     * add file upload record
     * @param  array $params   
     * @return object       (upload content data)
     */
    function post($params=array()){
        $params = (object)$params;
        $default_params = (object)array(
            'user_id' => USER_ID,
            'work_id' => 0,
            'type' => 'work',
            'filename' => '',
            'org_filename' => '',
            'filesize' => 0,
            'regdate' => date('Y-m-d H:i:s'),
            'moddate' => date('Y-m-d H:i:s'),
            'comment' => ''
        );
        foreach($default_params as $key => $value){
            if(!isset($params->{$key}))
                $params->{$key} = $value;
        }

        try{
            $this->db->insert('uploads', $params);
            $upload_id = $this->db->insert_id();
        }
        catch(Exception $e){
            $upload_id = 0;
        }
        
        return $upload_id;
    }

    /**
     * put upload data.
     *
     * @param  array  $params (depend by field in table `uploads`)
     * @return object       (status return object. status=[done|fail])
     */
    function put($data=array()){
        // null > return fail
        if($data == array()){
            $data = (object)array(
                'status' => 'fail',
                'message' => 'no_input_data'
            );

            return $data;
        }

        $input->moddate = date('Y-m-d H:i:s'); // 무조건 수정이 발생하게 하기 위하여 현재 타임스탬프로 임의로 찍어준다.
        
        //-- upload id is not for update
        $upload_id = $input->id;
        unset($input->id);

        $this->db->where('id', $upload_id)
                 ->where('user_id', USER_ID)
                 ->update('uploads', $input);

        $data = (object)array(
            'status' => 'done'
        );
        if($this->db->affected_rows()==0){
            $data->status = 'fail';
        }
        return $data;
    }

    /**
     * delete upload record.
     * cannot undo after run this code, so you must be careful to use.
     *
     * @param  array  $data (depend by field in table `uploads` but only use `uploads.id`)
     * @return object       (status return object. status=[done|fail])
     */
    function delete($data=array()){
        // null > return fail
        if($data == array()){
            $data = (object)array(
                'status' => 'fail',
                'message' => 'no_input_data'
            );

            return $data;
        }

        $upload_id = @$data['id'];

        if($this->nf->admin_is_elevated()){ // 관리자는 전지전능하심. 
            $can_delete = true;
        }
        else { // 본인것인지 여부에 따라 message다르게 하기
            $upload = $this->db->where('uploads.id', $upload_id)->get('uploads')->row();
            $can_delete = ($upload->user_id == USER_ID)?true:false; 
        }

        if($can_delete){
            $this->db->flush_cache(); //clear active record

            $this->db->trans_start();
            $this->db->where('id', @$data['id'])->delete('works'); 
            $this->db->trans_complete();

            if($this->db->trans_status()){
                $this->ci->load->config('upload', TRUE);

                exec(
                    preg_replace(
                        '/^(..)(..)([^\.]+)(\.[a-zA-Z]+)/', 
                        'rm -f '.$this->ci->config->item('img_upload_uri','upload').'$1/$2/$1$2$3*', 
                        $attachment->filename
                        )
                    );
                $data = (object)array(
                    'status' => 'done'
                );
            } else {
                $data = (object)array(
                    'status' => 'fail',
                    'message' => 'cannot_run_delete_sel'
                );
            }
        } else {
            $data = (object)array(
                'status' => 'fail',
                'message' => 'no_permission_to_delete'
            );
        }

        return $data;
    }


}

/* End of file upload_model.php */
/* Location: ./application/models/upload_model.php */