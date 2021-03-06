<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class comment_model extends CI_Model {


    public function __construct()
    {
        parent::__construct();
        
    }

    /**
     * 코멘트의 리스트를 불러온다.
     * @param  array  $params 
     * @return object          상태와 데이터값을 반환한다
     */
    function get_list($params=array()){
        log_message('debug','--------- comment_model > get_list ( params : '.print_r(get_defined_vars(),TRUE)).')';
        $params = (object)$params;
        $new_params = (object)array();
        foreach((object)array(
            'work_id'   => 0, // get by work_id
            'parent_id' => 0, // for recomment
            'id_before'  => 0, // call by...
            'id_after'  => 0, // call by...
            'page'      => 1, // 불러올 페이지
            'delimiter' => 11, // 한 페이지당 코멘트 수
            'order_by'  => 'newest', // newest, oldest
            'user_id'   => 0 // 프로필 등 특정 작가의 댓만을 조회할 때
        ) as $key => $default_value){
            $new_params->{$key} = isset($params->{$key}) ? $params->{$key} : $default_value;
        }
        $params = $new_params;

    	$this->db
            ->select('work_comments.*, users.id as user_id, users.username, users.email, users.level, users.realname, users.last_ip, users.last_login, users.created, users.modified')
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
            $this->db->where('work_comments.id <', $params->id_before);

        if(!empty($params->id_after)    &&$params->id_after!=0)
            $this->db->where('work_comments.id >', $params->id_after);


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
            $row->comment_id = $row->id;
            unset($row->id);

            // 값을 조작해야할 필요가 있을 때에는 여기에서 한다
            $user = (object)array(
                'user_id'    => $row->user_id,
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

            $row->children = array();
            if($row->children_cnt>0){
                $children_params=$params;
                $children_params->parent_id = $row->comment_id;
                unset($children_params->id_before);
                unset($children_params->id_after);

                $row->children = $this->get_list($children_params)->rows;
            }

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
     * @param  array $params   (comment_id)
     * @return object          상태와 데이터값을 반환한다
     */
    function get_info($params=array()){
        log_message('debug','--------- comment_model > get_info ( params : '.print_r(get_defined_vars(),TRUE)).')';
        $params = (object)$params;
        $new_params = (object)array();
        foreach((object)array(
            'comment_id' => '',
        ) as $key => $default_value){
            $new_params->{$key} = isset($params->{$key}) ? $params->{$key} : $default_value;
        }
        $params = $new_params;

        $this->db
            ->select('work_comments.*, users.id as user_id, users.username, users.email, users.level, users.realname, users.last_ip, users.last_login, users.created, users.modified')
            ->join('users', 'work_comments.user_id=users.id', 'left')
            ->from('work_comments')
            ->where('work_comments.id', $params->comment_id)
            ->limit(1); //set

        $comment = $this->db->get()->row();

        $data = (object)array(
            'status' => 'done',
            'row' => $comment
        );

        $data->row->comment_id = $data->row->id;
        unset($data->row->id);

        if(!$comment){
            $data->status = 'fail';
            return $data;
        }
        // 값을 조작해야할 필요가 있을 때에는 여기에서 한다
        $user = (object)array(
            'user_id'    => $data->row->user_id,
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

        $data->row->children = array();
        if($data->row->children_cnt>0){
            $children_params=$params;
            $children_params->parent_id = $data->row->comment_id;
            $data->row->children = $this->get_list($children_params)->rows;
        }

    	return $data;
    }


    /**
     * 업로드할 때에 해당 유저에 대해서 비어 있는 work_id를 생성한다.
     * @param  array $params   
     * @return object       (upload content data)
     */
    function post_info($params=array()){
        log_message('debug','--------- comment_model > post_info ( params : '.print_r(get_defined_vars(),TRUE)).')';
        $params = (object)$params;
        $new_params = (object)array();
        foreach((object)array(
            'user_id' => USER_ID,
            'work_id' => 0,
            'parent_id' => 0,
            'content' => '',
            'regdate' => date('Y-m-d H:i:s'),
            'moddate' => date('Y-m-d H:i:s'),
        ) as $key => $default_value){
            $new_params->{$key} = isset($params->{$key}) ? $params->{$key} : $default_value;
        }
        $params = $new_params;

        $this->db->trans_start();
        $this->db->insert('work_comments', $params);
        $affected = $this->db->affected_rows();

        $comment_id = $this->db->insert_id();
        if($params->parent_id!=0){
            $this->db
                ->set('children_cnt', 'children_cnt+1', FALSE)
                ->where('id', $params->parent_id)
                ->update('work_comments');
        }
        $this->db->trans_complete();

        $data = (object)array(
            'status' => 'done',
            'comment_id' => $comment_id
        );
        if($this->db->trans_status()){
            $this->db->query("UPDATE works 
                set comment_cnt = comment_cnt + {$affected} 
                where work_id = {$params->work_id};
                ");

        }else{
            $data->status = 'fail';
            $data->message = 'inserting_failed';
        }
        return $data;
    }

    /**
     * post work data when create/update.
     * only use UPDATE query; so you must run $this->post_info() before run this func.
     *
     * @param  array  $data (depend by field in table `works`)
     * @return object       (status return object. status=[done|fail])
     */
    function put_info($params=array()){
        log_message('debug','--------- comment_model > put_info ( params : '.print_r(get_defined_vars(),TRUE)).')';

        $params = (object)$params;
        $new_params = (object)array();
        foreach((object)array(
            'comment_id' => '',
            'user_id' => USER_ID,
            'work_id' => '',
            'parent_id' => '',
            'content' => '',
        ) as $key => $default_value){
            $new_params->{$key} = isset($params->{$key}) ? $params->{$key} : $default_value;
        }
        $params = $new_params;

        $params->moddate = date('Y-m-d H:i:s'); // 무조건 수정이 발생하게 하기 위하여 현재 타임스탬프로 임의로 찍어준다.
        
        //-- work id is not for update
        $comment_id = $params->comment_id;
        unset($params->comment_id);

        $this->db->flush_cache(); //clear active record
        
        $this->db->trans_start();
        $this->db->where('id', $comment_id)->where('user_id', USER_ID)->update('work_comments', $params);
        $this->db->trans_complete();

        $data = (object)array(
            'status' => 'done'
        );
        if($this->db->trans_status()==FALSE){
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
        log_message('debug','--------- comment_model > delete_info ( params : '.print_r(get_defined_vars(),TRUE)).')';
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
            $comment = $this->db->where('work_comments.id', $comment_id)->get('work_comments')->row();
        }
        else { // 본인것인지 여부에 따라 message다르게 하기
            $comment = $this->db->where('work_comments.id', $comment_id)->get('work_comments')->row();
            $can_delete = ($comment->user_id == USER_ID)?true:false; 
        }

        if($can_delete){ // 삭제가능 여부에 따라 message다르게 하기
            $this->db->flush_cache(); //clear active record
            
            $this->db->trans_start();
            $this->db->where('id', $comment_id)->where('user_id', USER_ID)->delete('work_comments'); 
            $affected = $this->db->affected_rows();

            // 하위 리플들도 모두 지워지도록 처리해주세요.
            $this->db->where('parent_id', $comment_id)->delete('work_comments'); 
            $affected += $this->db->affected_rows();
            
            // parent_id의 children_cnt도 업데이트해주세요.
            $this->db
                ->set('children_cnt', 'children_cnt-1', FALSE)
                ->where('id', $comment->parent_id)
                ->update('work_comments');

            $this->db->trans_complete();

            if($this->db->trans_status()){
                $this->db->query("UPDATE works 
                    set comment_cnt = comment_cnt - {$affected} 
                    where work_id = {$comment->work_id};
                    ");
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