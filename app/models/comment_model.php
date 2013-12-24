<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class comment_model extends CI_Model {


    public function __construct()
    {
        parent::__construct();
        
    }

    /**
     * 작품의 리스트를 불러온다.
     * @param  array  $params 
     * @return object          상태와 데이터값을 반환한다
     */
    function get_list($params=array()){
    	$params = (object)$params;
    	$default_params = (object)array(
            'work_id'   => 0, // get by work_id
            'parent_id' => 0, // for recomment
            'id_before'  => 0, // call by...
            'id_after'  => 0, // call by...
            'page'      => 1, // 불러올 페이지
            'delimiter' => 10, // 한 페이지당 작품 수
            'order_by'  => 'newest', // newest, oldest
            'user_id'   => 0 // 프로필 등 특정 작가의 댓만을 조회할 때
    	);
    	foreach($default_params as $key => $value){
    		if(!isset($params->{$key}))
    			$params->{$key} = $value;
    	}

    	$this->db
            ->select('work_comments.*, users.id, users.username, users.email, users.level, users.realname, users.last_ip, users.last_login, users.created, users.modified')
    		->from('work_comments')
    		->join('users', 'users.id = work_comments.user_id', 'left')
    		->limit($params->delimiter, ((($params->page)-1)*$params->delimiter)); //set
        
        if(!empty($params->work_id)     &&$params->work_id!=0)
            $this->db->where('work_id', $params->work_id);

        if(!empty($params->parent_id)     &&$params->parent_id!=0)
            $this->db->where('parent_id', $params->parent_id);
        else
            $this->db->where('parent_id', 0);

        if(!empty($params->user_id)     &&$params->user_id!=0)
            $this->db->where('user_id', $params->user_id);

        if(!empty($params->id_before)   &&$params->id_before!=0)
            $this->db->where('id<', $params->id_before);

        if(!empty($params->id_after)    &&$params->id_after!=0)
            $this->db->where('id<', $params->id_after);


    	switch($params->order_by){
    		case "newest":
    			$this->db->order_by('regdate', 'desc');
    		break;
    		case "oldest":
    			$this->db->order_by('regdate', 'asc');
    		break;
    		default:
    			if(is_array($params->order_by))
    				$this->db->order_by($params->order_by);
    		break;
    	}

    	$comments = $this->db->get();

    	$rows = array();
    	foreach ($comments->result() as $row)
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
     * call comment seperately
     * @param  array $params   (work_id)
     * @return object          상태와 데이터값을 반환한다
     */
    function get_info($params=array()){
        $params = (object)$params;
        $default_params = (object)array(
            'comment_id' => ''
        );
        foreach($default_params as $key => $value){
            if(!isset($params->{$key}))
                $params->{$key} = $value;
        }

        $this->db
            ->select('work_comments.*, users.id, users.username, users.email, users.level, users.realname, use')
            ->from('work_comments')
            ->where('id', $params->comment_id);
            ->limit(1); //set

        $comment = $this->db->get()->row();

        $data = (object)array(
            'status' => 'done',
            'row' => $comment
        );
        if(!$comment){
            $data->status = 'fail';
            return $data;
        }
        // 값을 조작해야할 필요가 있을 때에는 여기에서 한다
        $user = (object)array(
            'id'         => $data->row->id,
            'username'   => $data->row->username,
            'email'      => $data->row->email,
            'level'      => $data->row->level,
            'realname'   => $data->row->realname,
            'last_ip'    => $data->row->last_ip,
            'last_login' => $data->row->last_login,
            'created'    => $data->row->created,
            'modified'   => $data->row->modified,
            'keywords'   => array('파인아트', '동영상'), // temporary
            'sns'   => (object)array(// temporary
                'facebook' => 'maxzidell',
                'twitter' => 'maxzidell'
            ) 
        );
        foreach($user as $key=>$value){
            unset($data->row->{$key});
        }
        $data->row->user = $user;

    	return $data;
    }


    /**
     * 업로드할 때에 해당 유저에 대해서 비어 있는 work_id를 생성한다.
     * @param  array $params   
     * @return object       (upload content data)
     */
    function post ($params=array()){
        $params = (object)$params;
        $default_params = (object)array(
            'user_id' => USER_ID,
            'work_id' => 0,
            'parent_id' => 0,
            'content' => '',
            'regdate' => date('Y-m-d H:i:s'),
            'moddate' => date('Y-m-d H:i:s'),
        );
        foreach($default_params as $key => $value){
            if(!isset($params->{$key}))
                $params->{$key} = $value;
        }

        $this->db->insert('work_comments', $params);

        $comment_id = $this->db->insert_id();
        return $comment_id;
    }

    /**
     * post work data when create/update.
     * only use UPDATE query; so you must run $this->post_info() before run this func.
     *
     * @param  array  $data (depend by field in table `works`)
     * @return object       (status return object. status=[done|fail])
     */
    function put_info($input=array()){
        $allowed_field = array('comment_id', 'user_id', 'work_id', 'parent_id', 'content');

        // 값을 정규식으로 검사한다.
        foreach($input as $key=>$val){
            if(!in_array($key, $allowed_field)      //-- 허용 필드가 아닐때
                &&!$this->nf->admin_is_elevated()){ //-- 관리자가 아닐때
                unset($input->$key);                //-- 필드 unset
            }
        }

        $input->moddate = date('Y-m-d H:i:s'); // 무조건 수정이 발생하게 하기 위하여 현재 타임스탬프로 임의로 찍어준다.
        
        //-- work id is not for update
        $comment_id = $input->comment_id;
        unset($input->comment_id);

        $this->db->flush_cache(); //clear active record
        
        $this->db->trans_start();
        $this->db->where('id', $comment_id)->where('user_id', USER_ID)->update('works', $input);
        $this->db->trans_complete();

        $data = (object)array(
            'status' => 'done'
        );
        if($this->db->affected_rows()==0){
            $data->status = 'fail';
        }
        return $data;
    }

    /**
     * delete work record.
     * cannot undo after run this code, so you must be careful to use.
     *
     * @param  array  $data (depend by field in table `works` but only use `work_id`)
     * @return object       (status return object. status=[done|fail])
     */
    function delete_info($data=array()){
        // null > return fail
        if($data == array()){
            $data = (object)array(
                'status' => 'fail',
                'message' => 'no_input_data'
            );

            return $data;
        }

        $comment_id = @$data['comment_id'];
        
        if($this->nf->admin_is_elevated()){ // 관리자는 전지전능하심. 
            $can_delete = true;
        }
        else { // 본인것인지 여부에 따라 message다르게 하기
            $comment = $this->db->where('work_comments.id', $comment_id)->get('work_comments')->row();
            $can_delete = ($comment->user_id == USER_ID)?true:false; 
        }

        if($can_delete){ // 삭제가능 여부에 따라 message다르게 하기
            $this->db->flush_cache(); //clear active record
            
            $this->db->trans_start();
            $this->db->where('id', $comment_id)->delete('work_comments'); 
            $this->db->trans_complete();

            if($this->db->trans_status()){
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

/* End of file comment_model.php */
/* Location: ./application/models/comment_model.php */