<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class work_model extends CI_Model {


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
            'id_before'  => 0, // call by...
            'id_after'  => 0, // call by...
            'page'      => 1, // 불러올 페이지
            'delimiter' => 24, // 한 페이지당 작품 수
            'order_by'  => 'newest', // newest, oldest
            'keywords'  => '', 
            'folder'    => '', // ''면 전체
            'user_id'   => '', // 프로필 등 특정 작가의 작품만을 조회할 때
            'only_enable'   => false, // enable된 작품만
    	);
    	foreach($default_params as $key => $value){
    		if(!isset($params->{$key}))
    			$params->{$key} = $value;
    	}

        if($params->only_enable){
            $this->db->where('works.status', 'enabled');
        }

    	$this->db
            ->select('works.*, users.id, users.username, users.email, users.level, users.realname, users.last_ip, users.last_login, users.created, users.modified')
    		// ->select('work_id, title, realname, regdate, keywords, tags, user_id, folder, contents, moddate, hit_cnt, note_cnt, comment_cnt, collect_cnt, ccl, discoverbility')
    		->from('works')
    		->join('users', 'users.id = works.user_id', 'left')
    		->limit($params->delimiter, ((($params->page)-1)*$params->delimiter)); //set

        if(!empty($params->user_id))
            $this->db->where('user_id', $params->user_id);
        if(!empty($params->id_before)   &&$params->id_before!=0)
            $this->db->where('works.work_id <', $params->id_before);

        if(!empty($params->id_after)    &&$params->id_after!=0)
            $this->db->where('works.work_id >', $params->id_after);
        
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

    	$works = $this->db->get();

    	$rows = array();
    	foreach ($works->result() as $row)
		{
            // 값을 조작해야할 필요가 있을 때에는 여기에서 한다
            if(substr($row->contents, 0, 2)=='a:')
                $row->contents = unserialize($row->contents);

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
     * 작품의 자세한 정보를 불러들인다.
     * @param  array $params   (work_id, folder)
     * @return object          상태와 데이터값을 반환한다
     */
    function get_info($params=array()){
        $params = (object)$params;
        $default_params = (object)array(
            'work_id' => '',
            'folder'  => '' // ''면 모든 작품
        );
        foreach($default_params as $key => $value){
            if(!isset($params->{$key}))
                $params->{$key} = $value;
        }

    	$this->db
            ->select('works.*, users.id, users.username, users.email, users.level, users.realname, users.last_ip, users.last_login, users.created, users.modified, user_profiles.keywords as user_keywords')
    		// ->select('work_id, title, realname as user, regdate, keywords, tags, user_id, folder, contents, moddate, hit_cnt, note_cnt, comment_cnt, collect_cnt, ccl, discoverbility')
    		->from('works')
            ->join('users', 'users.id = works.user_id', 'left')
    		->join('user_profiles', 'user_profiles.user_id = works.user_id', 'left')
    		->where('works.work_id', $params->work_id)
    		->limit(1); //set
        $work = $this->db->get()->row();

        // 여기에서 값을 조작한다.
        # do stuff
        $work->keywords = 'A7B7'; // temporary
        $work->tags = @explode(')(', trim(trim($work->tags, '('),')'));
        if(substr($work->contents, 0, 2)=='a:')
            $work->contents = unserialize($work->contents);

        $data = (object)array(
            'status' => 'done',
            'row' => $work
        );
        if(!$work){
            $data->status = 'fail';
            return $data;
        }
        // 값을 조작해야할 필요가 있을 때에는 여기에서 한다
        $data->row->noted = $data->row->collected = $data->row->is_follow = 'n';
        if(USER_ID>0){
            # 로그인한 사용자라면 이 사람이 어떻게 했는지 쿼리를 여기에서 하나 날리고 아래 값을 할당한다.
            $data->row->noted = ($this->get_note(array('work_id'=> $params->work_id,'user_id'=>USER_ID)))? 'y': 'n';
            $data->row->collected = ($this->get_collect(array('work_id'=> $params->work_id,'user_id'=>USER_ID)))? 'y': 'n';
            # do stuff
            $data->row->is_follow = rand(0,9)>5 ? 'y' : 'n';
        }

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
            'user_keywords'   => $data->row->user_keywords,
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
     * @return object       (work content data)
     */
    function post_info(){
        $this->db->insert('works', array(
            'work_id' => NULL, // 자동 생성
            'user_id' => USER_ID
        ));
        $work_id = $this->db->insert_id();
        return $work_id;
    }

    /**
     * post work data when create/update.
     * only use UPDATE query; so you must run $this->post_info() before run this func.
     *
     * @param  array  $data (depend by field in table `works`)
     * @return object       (status return object. status=[done|fail])
     */
    function put_info($input=array()){
        log_message('debug','--------- work_model > put_info ( params : '.print_r(get_defined_vars(),TRUE)).')';
        
        // 값을 정규식으로 검사한다.
        
        $input->moddate = date('Y-m-d H:i:s'); // 무조건 수정이 발생하게 하기 위하여 현재 타임스탬프로 임의로 찍어준다.
        
        //-- work id is not for update
        $work_id = $input->work_id;
        unset($input->work_id);

        $this->db
            ->where('work_id', $work_id)
            ->where('user_id', USER_ID)
            ->update('works', $input);

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


        $work_id = @$data['work_id'];

        // 본인것인지 여부에 따라 message다르게 하기
        $work = $this->db->where('work_id', $work_id)->get('works')->row(); 
        if($work->user_id == USER_ID){
            $this->db->flush_cache(); //clear active record
            
            $this->db->trans_start();
            $this->db->where('work_id', $work_id)->delete('works'); 
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






    function get_hot_creators(){
        log_message('debug','--------- work_model > get_hot_creators ( params : '.print_r(get_defined_vars(),TRUE)).')';
        $result = $this->db
            ->select('users.*, user_profiles.keywords as user_keywords')
            ->from('hot_creators')
            ->join('users', 'hot_creators.user_id=users.id', 'left')
            ->join('user_profiles', 'hot_creators.user_id=user_profiles.user_id', 'left')
            ->order_by('hot_id', 'desc')
            ->limit(4)
            ->get()
            ->result();
        
        foreach($result as &$row){
            $row->user_keywords = 'A7B7'; // dummy
        }
        return $result;
    }
    
    /**
     * post view for work
     * 
     * @param  array  $params 
     * @return object          상태와 데이터값을 반환한다
     */
    function post_view($params=array()){
        $params = (object)$params;
        $default_params = (object)array(
            'user_id'   => USER_ID,
            'work_id'   => '',
            'remote_addr'   => $this->input->server('REMOTE_ADDR'),
            'phpsessid'   => $this->input->cookie('PHPSESSID'),
            'regdate'   => date('Y-m-d H:i:s')
        );
        foreach($default_params as $key => $value){
            if(!isset($params->{$key}))
                $params->{$key} = $value;
        }

        $data = (object)array(
                'status' => 'fail',
                'message' => 'no_process'
            );

        if($params->user_id>0){
            $query = $this->db
                ->where(array(
                    'user_id'=>$params->user_id,
                    'work_id'=>$params->work_id
                    ))
                ->get('log_work_view');
        }
        else{
            $query = $this->db
                ->where(array(
                    'user_id'=>0,
                    'work_id'=>$params->work_id
                    ))
                ->where("(
                    phpsessid like '$params->phpsessid'
                    OR
                    remote_addr like '$params->remote_addr'
                    )")
                ->get('log_work_view');

        }
        if($query->num_rows()>0){
            $data->status = 'fail';
            $data->message = 'already_viewd';

            return $data;
        }
        else if($params->user_id==0){
            $query->free_result();
            $query = $this->db
                ->where(array(
                    'user_id'=>0,
                    'work_id'=>$params->work_id,
                    'remote_addr'=>$params->remote_addr
                    ))
                ->where('regdate >= SUBDATE(now(),INTERVAL 5 minute)')
                ->get('log_work_view');
            if($query->num_rows()>10){
                $data->status = 'fail';
                $data->message = 'too_many_with_same_ip';

                return $data;
            }
        }
        $query->free_result();

        $this->db->trans_start();
        try{ 
            $this->db->insert('log_work_view', $params);
        }
        catch(Exception $e){
            $data->status = 'fail';
            $data->message = 'no_db_insert';

            return $data;
        } 
        $affected = $this->db->affected_rows();
        $this->db->trans_complete();

        if($this->db->trans_status()){
            $this->db->query("UPDATE works 
                set hit_cnt = hit_cnt + {$affected} 
                where work_id = {$params->work_id};
                ");
            $data->status = 'done';
            $data->message = 'successed';
        }
        return $data;
    }
    
    /**
     * get note status for work
     * 
     * @param  array  $params 
     * @return bool
     */
    function get_note($params=array()){
        $params = (object)$params;
        $default_params = (object)array(
            'user_id'   => USER_ID,
            'work_id'   => '',
            'remote_addr'   => $this->input->server('REMOTE_ADDR'),
            'phpsessid'   => $this->input->cookie('PHPSESSID'),
        );
        foreach($default_params as $key => $value){
            if(!isset($params->{$key}))
                $params->{$key} = $value;
        }

        if($params->user_id>0){
            $query = $this->db
                ->where(array(
                    'user_id'=>$params->user_id,
                    'work_id'=>$params->work_id
                    ))
                ->get('log_work_note');
        }
        else{
            $query = $this->db
                ->where(array(
                    'user_id'=>0,
                    'work_id'=>$params->work_id
                    ))
                ->where("(
                    phpsessid like '{$params->phpsessid}'
                    OR
                    remote_addr like '{$params->remote_addr}'
                    )")
                ->get('log_work_note');

        }
        $status = ($query->num_rows()>0)?true:false;
        $query->free_result();

        return $status;
    }

    /**
     * post note for work
     * 
     * @param  array  $params 
     * @return object          상태와 데이터값을 반환한다
     */
    function post_note($params=array()){
        $params = (object)$params;
        $default_params = (object)array(
            'user_id'   => USER_ID,
            'work_id'   => '',
            'remote_addr'   => $this->input->server('REMOTE_ADDR'),
            'phpsessid'   => $this->input->cookie('PHPSESSID'),
            'regdate'   => date('Y-m-d H:i:s')
        );
        foreach($default_params as $key => $value){
            if(!isset($params->{$key}))
                $params->{$key} = $value;
        }

        $data = (object)array(
                'status' => 'fail',
                'message' => 'no_process'
            );

        if($this->get_note($params)){
            $data->status = 'fail';
            $data->message = 'already_noted';

            return $data;
        }
        else if($params->user_id==0){
            $query = $this->db
                ->where(array(
                    'user_id'=>0,
                    'work_id'=>$params->work_id,
                    'remote_addr'=>$params->remote_addr
                    ))
                ->where('regdate >= SUBDATE(now(),INTERVAL 5 minute)')
                ->get('log_work_note');
            if($query->num_rows()>10){
                $data->status = 'fail';
                $data->message = 'too_many_with_same_ip';

                return $data;
            }
            $query->free_result();
        }

        
        $this->db->trans_start();
        try{ 
            $this->db->insert('log_work_note', $params);
        }
        catch(Exception $e){
            $data->status = 'fail';
            $data->message = 'no_db_insert';

            return $data;
        } 
        $affected = $this->db->affected_rows();
        $this->db->trans_complete();

        if($this->db->trans_status()){
            $this->db->query("UPDATE works 
                set note_cnt = note_cnt + {$affected} 
                where work_id = {$params->work_id};
                ");
            $data->status = 'done';
            $data->message = 'successed';
        }
        return $data;
    }

    
    /**
     * delete note for work
     * 
     * @param  array  $params 
     * @return object          상태와 데이터값을 반환한다
     */
    function delete_note($params=array()){
        $params = (object)$params;
        $default_params = (object)array(
            'user_id'   => USER_ID,
            'work_id'   => ''
        );
        foreach($default_params as $key => $value){
            if(!isset($params->{$key}))
                $params->{$key} = $value;
        }

        $data = (object)array(
                'status' => 'fail',
                'message' => 'no_process'
            );

        if(!empty($params->user_id) && $params->user_id>0
            && !empty($params->work_id) && $params->work_id>0
            ){
            
            $query = $this->db
                ->where(array(
                    'user_id'=>$params->user_id,
                    'work_id'=>$params->work_id
                    ))
                ->get('log_work_note');
            if($query->num_rows()==0){
                $data->status = "fail";
                $data->message = 'no_noted';

                return $data;
            }
            $query->free_result();
            
            $this->db->trans_start();
            try{ 
                $this->db
                    ->where(array(
                        'user_id'=>$params->user_id,
                        'work_id'=>$params->work_id
                        ))
                    ->delete('log_work_note');
            }
            catch(Exception $e){
                $data->status = "fail";
                $data->message = 'no_db_delete';

                return $data;
            } 
            $affected = $this->db->affected_rows();
            $this->db->trans_complete();

            if($this->db->trans_status()){
                $this->db->query("UPDATE works 
                    set note_cnt = note_cnt - {$affected} 
                    where work_id = {$params->work_id};
                    ");
                $data->status = 'done';
                $data->message = 'successed';
            }

        }
        else{

        }
        return $data;
    }
    
    /**
     * post collect for work
     * 
     * @param  array  $params 
     * @return object          상태와 데이터값을 반환한다
     */
    function get_collect($params=array()){
        $params = (object)$params;
        $default_params = (object)array(
            'user_id'   => USER_ID,
            'work_id'   => '',
        );
        foreach($default_params as $key => $value){
            if(!isset($params->{$key}))
                $params->{$key} = $value;
        }

        $data = (object)array(
                'status' => 'fail',
                'message' => 'no_process'
            );

        if(!empty($params->user_id) && $params->user_id>0){
            
            $query = $this->db
                ->where(array(
                    'user_id'=>$params->user_id,
                    'work_id'=>$params->work_id
                    ))
                ->get('user_work_collect');
            $status = ($query->num_rows()>0)? true: false;
            $query->free_result();

            return $status;
        }

        return false;
    }
    
    /**
     * post collect for work
     * 
     * @param  array  $params 
     * @return object          상태와 데이터값을 반환한다
     */
    function post_collect($params=array()){
        $params = (object)$params;
        $default_params = (object)array(
            'user_id'   => USER_ID,
            'work_id'   => '',
            'comment'   => '',
            'regdate'   => date('Y-m-d H:i:s')
        );
        foreach($default_params as $key => $value){
            if(!isset($params->{$key}))
                $params->{$key} = $value;
        }

        $data = (object)array(
                'status' => 'fail',
                'message' => 'no_process'
            );

        if(!empty($params->user_id) && $params->user_id>0){

            if($this->get_collect($params)){
                $data->status = 'fail';
                $data->message = 'already_collected';

                return $data;
            }

            $this->db->trans_start();
            try{ 
                $this->db->insert('user_work_collect', $params);
            }
            catch(Exception $e){
                $data->status = 'fail';
                $data->message = 'no_db_insert';

                return $data;
            } 
            $affected = $this->db->affected_rows();
            $this->db->trans_complete();

            if($this->db->trans_status()){
                $this->db->query("UPDATE works 
                    set collect_cnt = collect_cnt + {$affected} 
                    where work_id = {$params->work_id};
                    ");
                $data->status = 'done';
                $data->message = 'successed';
            }

        }
        else{

        }
        return $data;
    }

    
    /**
     * delete collect for work
     * 
     * @param  array  $params 
     * @return object          상태와 데이터값을 반환한다
     */
    function delete_collect($params=array()){
        $params = (object)$params;
        $default_params = (object)array(
            'user_id'   => USER_ID,
            'work_id'   => ''
        );
        foreach($default_params as $key => $value){
            if(!isset($params->{$key}))
                $params->{$key} = $value;
        }

        $data = (object)array(
                'status' => 'fail',
                'message' => 'no_process'
            );

        if(!empty($params->user_id) && $params->user_id>0
            && !empty($params->work_id) && $params->work_id>0
            ){
            
            $query = $this->db
                ->where(array(
                    'user_id'=>$params->user_id,
                    'work_id'=>$params->work_id
                    ))
                ->get('user_work_collect');
            if($query->num_rows()==0){
                $data->status = "fail";
                $data->message = 'no_collected';

                return $data;
            }
            $query->free_result();
            
            $this->db->trans_start();
            try{ 
                $this->db
                    ->where(array(
                        'user_id'=>$params->user_id,
                        'work_id'=>$params->work_id
                        ))
                    ->delete('user_work_collect');
            }
            catch(Exception $e){
                $data->status = "fail";
                $data->message = 'no_db_delete';

                return $data;
            } 
            $affected = $this->db->affected_rows();
            $this->db->trans_complete();

            if($this->db->trans_status()){
                $this->db->query("UPDATE works 
                    set collect_cnt = collect_cnt - {$affected} 
                    where work_id = {$params->work_id};
                    ");
                $data->status = 'done';
                $data->message = 'successed';
            }

        }
        else{

        }
        return $data;
    }

}

/* End of file work_model.php */
/* Location: ./application/models/work_model.php */