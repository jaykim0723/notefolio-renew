<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class work_model extends CI_Model {


    public function __construct()
    {
        parent::__construct();
        
    }

    function get_random_work_info(){
        return $this->db
            ->select('works.work_id, users.username')
            ->from('works')
            ->join('users', 'works.user_id=users.id', 'left')
            ->where('works.status', 'enabled')
            ->where('works.user_id !=', '0')
            ->order_by('works.work_id', 'random')
            ->limit(1)
            ->get()
            ->row();
    }

    /**
     * 미리 설정하는 환경설정.
     * @param object $params
     */
    function get_list_prep($params){
        $allows = array();
        $excludes = array();
        foreach(array('enabled', 'disabled', 'deleted') as $type){
            if($params->{'allow_'.$type}){
                $allows[] = $type;
            }

            if($params->{'exclude_'.$type}){
                $excludes[] = $type;
            }
        }

        if(count($allows)>0){
            $this->db->where_in('works.status', $allows);
        }
        if(count($excludes)>0){
            $this->db->where_not_in('works.status', $excludes);
        }

        switch($params->from){
            case 'day':
                $from = date('Y-m-d');
                break;
            case 'week':
                $from = date('Y-m-d', strtotime('-1 week'));
                break;
            case 'month':
                $from = date('Y-m-d', strtotime('-1 month'));
                break;
            case 'month3':
                $from = date('Y-m-d', strtotime('-3 month'));
                break;
            case 'all':
            default:
                $params->from = 'all';
                break;
        }
        if($params->from!='all'){
            $this->db->having("(works.regdate >= ".$this->db->escape($from).")", NULL, FALSE); // 모든 기준이 regdate로 하기 때문에
            // $this->db->having("(works.regdate >= ".$this->db->escape($from)." or works.moddate >= ".$this->db->escape($from).")", NULL, FALSE);
        }

        if(count($params->keywords)>0){
            $this->db->where('( works.keywords like "%'.implode('%" or works.keywords like "%', $params->keywords).'%" )', NULL, FALSE);
        }

        if(!empty($params->q)){
            $this->db->where('(MATCH (works.title, works.tags) AGAINST ('.$this->db->escape($params->q).') or users.username like \'%'.$this->db->escape_str($params->q).'%\' or users.realname like \'%'.$this->db->escape_str($params->q).'%\'  )', NULL, FALSE);
        }

        if(!empty($params->user_id))
            $this->db->where('user_id', $params->user_id);
        if(!empty($params->id_before)   &&$params->id_before!=0)
            $this->db->where('works.work_id <', $params->id_before);

        if(!empty($params->id_after)    &&$params->id_after!=0)
            $this->db->where('works.work_id >', $params->id_after);
        
        switch($params->order_by){
            case "idlarger":
                $this->db->order_by('works.work_id', 'desc');
            break;
            case "idsmaller":
                $this->db->order_by('works.work_id', 'asc');
            break;
            case "newest":
                $this->db->order_by('works.regdate', 'desc');
            break;
            case "oldest":
                $this->db->order_by('works.regdate', 'asc');
            break;
            case "noted":
                $this->db->order_by('works.note_cnt', 'desc');
            break;
            case "viewed":
                $this->db->order_by('works.hit_cnt', 'desc');
            break;
            case "featured":
                $this->db->order_by('works.nofol_rank', 'desc');
            break;
            case "comment_desc":
                $this->db->order_by('works.comment_cnt', 'desc');
            break;
            case "nofol_rank":
                $params->view_rank_point = true;
                $this->db->order_by('rank_point', 'desc');
                $this->db->order_by('works.regdate', 'desc');
            break;
            default:
                if(is_array($params->order_by))
                    $this->db->order_by($params->order_by);
            break;
        }

        if($params->view_rank_point){
            $this->load->config('activity_point', TRUE);

            $period = $this->config->item('period', 'activity_point');

            $this->db->join('(SELECT
                ref_id as work_id,
                ifnull(sum(point_get), 0) as point 
                FROM `notefolio-renew`.log_activity
                where area=\'work\' 
                and regdate >= '.$this->db->escape($period['feedback']).'
                group by work_id) feedbacks', 'works.work_id = feedbacks.work_id', 'left');
            $this->db->select('feedbacks.point as feedback_point');
            $this->db->select('(works.discoverbility + ifnull(feedbacks.point, 0) + works.staffpoint) as rank_point', FALSE);
        }
        
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
            'from'  => 'all', // 조회기간
            'order_by'  => 'newest', // newest, oldest
            'keywords'  => array(), 
            'q'  => '', 
            'folder'    => '', // ''면 전체
            'user_id'   => '', // 프로필 등 특정 작가의 작품만을 조회할 때
            'allow_enabled'      => false, // enable된 작품만
            'allow_disabled'     => false, // disabled된 작품만
            'allow_deleted'      => false, // deleted된 작품만
            'exclude_enabled'   => false, // enabled 태그된 작품 제외
            'exclude_disabled'   => false, // disabled 태그된 작품 제외
            'exclude_deleted'   => true, // deleted 태그된 작품 제외
            'view_rank_point'   => false, // deleted 태그된 작품 제외
        );
        foreach($default_params as $key => $value){
            if(!isset($params->{$key}))
                $params->{$key} = $value;
        }

        $this->db->start_cache();
        $this->get_list_prep($params);

        $this->db
            ->select('works.*, users.id, users.username, users.email, users.level, users.realname, users.last_ip, users.last_login, users.created, users.modified')
            // ->select('work_id, title, realname, regdate, keywords, tags, user_id, folder, contents, moddate, hit_cnt, note_cnt, comment_cnt, collect_cnt, ccl, discoverbility')
            ->from('works')
            ->join('users', 'users.id = works.user_id', 'left')
            ->limit($params->delimiter, ((($params->page)-1)*$params->delimiter)); //set

        $this->db->stop_cache();
        $works = $this->db->get();
        $this->db->flush_cache();

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
     * 작품의 리스트 카운트를 불러온다.
     * @param  array  $params 
     * @return object          상태와 데이터값을 반환한다
     */
    function get_list_count($params=array()){
    	$params = (object)$params;
    	$default_params = (object)array(
            'id_before'  => 0, // call by...
            'id_after'  => 0, // call by...
            'page'      => 1, // 불러올 페이지
            'delimiter' => 24, // 한 페이지당 작품 수
            'from'  => 'all', // 조회기간
            'order_by'  => 'newest', // newest, oldest
            'keywords'  => array(), 
            'q'  => '', 
            'folder'    => '', // ''면 전체
            'user_id'   => '', // 프로필 등 특정 작가의 작품만을 조회할 때
            'allow_enabled'      => false, // enable된 작품만
            'allow_disabled'     => false, // disabled된 작품만
            'allow_deleted'      => false, // deleted된 작품만
            'exclude_enabled'   => false, // enabled 태그된 작품 제외
            'exclude_disabled'   => false, // disabled 태그된 작품 제외
            'exclude_deleted'   => true, // deleted 태그된 작품 제외
            'view_rank_point'   => false, // deleted 태그된 작품 제외
    	);
    	foreach($default_params as $key => $value){
    		if(!isset($params->{$key}))
    			$params->{$key} = $value;
    	}

        $this->db->start_cache();
        $this->get_list_prep($params);

        $this->db
            ->select('count(*) as count, ceil(count(*)/'.$params->delimiter.') as all_page')
            ->from('works')
            ->join('users', 'users.id = works.user_id', 'left'); //set

        $this->db->stop_cache();
    	$works = $this->db->get();
        $this->db->flush_cache();

    	$row = $works->row();

        $data = (object)array(
            'status' => 'done',
            'page'   => $params->page,
            'row'   => $row
        );
        if(!isset($row)){
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
            'folder'  => '', // ''면 모든 작품
            'get_next_prev'=>false,
        );
        foreach($default_params as $key => $value){
            if(!isset($params->{$key}))
                $params->{$key} = $value;
        }

    	$this->db
            ->select('works.*')
            ->select('users.id, users.username, users.email, users.level, users.realname, users.last_ip, users.last_login, users.created, users.modified')
            ->select('user_profiles.keywords as user_keywords, user_profiles.website as user_website, user_profiles.face_color as user_face_color')
            ->select('user_profiles.facebook_id as user_facebook_id, user_profiles.twitter_id as user_twitter_id,user_profiles.pinterest_id as user_pinterest_id, user_profiles.tumblr_id as user_tumblr_id, user_profiles.vimeo_id as user_vimeo_id')
    		// ->select('work_id, title, realname as user, regdate, keywords, tags, user_id, folder, contents, moddate, hit_cnt, note_cnt, comment_cnt, collect_cnt, ccl, discoverbility')
    		->from('works')
            ->join('users', 'users.id = works.user_id', 'left')
    		->join('user_profiles', 'user_profiles.user_id = works.user_id', 'left')
    		->where('works.work_id', $params->work_id)
    		->limit(1); //set
        $work = $this->db->get()->row();

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
        $data->row->tags = @explode(',', $data->row->tags);

        if(USER_ID>0){
            # 로그인한 사용자라면 이 사람이 어떻게 했는지 쿼리를 여기에서 하나 날리고 아래 값을 할당한다.
            $data->row->noted = ($this->get_note(array('work_id'=> $params->work_id,'user_id'=>USER_ID)))? 'y': 'n';
            $data->row->collected = ($this->get_collect(array('work_id'=> $params->work_id,'user_id'=>USER_ID)))? 'y': 'n';

            $followed = $this->db
                ->where(array(
                    'follower_id'=>USER_ID,
                    'follow_id'=>$data->row->id
                    ))
                ->get('user_follows');

            $data->row->is_follow = ($followed->num_rows()>0) ? 'y' : 'n';
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
            'sns'   => (object)array(
                'website' => $data->row->user_website,
                'facebook' => $data->row->user_facebook_id,
                'twitter' => $data->row->user_twitter_id,
                'pinterest' => $data->row->user_pinterest_id,
                'tumblr' => $data->row->user_tumblr_id,
                'vimeo' => $data->row->user_vimeo_id,
            ),
            'face_color'   => $data->row->user_face_color,
        );
        foreach($user as $key=>$value){
            unset($data->row->{$key});
        }
        $data->row->user = $user;

        if($params->get_next_prev){
            $this->db->flush_cache();

            try{
                $first = $this->db
                    ->select('work_id')
                    ->where('user_id', $user->id)
                    ->where_not_in('works.status', array('disabled', 'deleted'))
                    ->order_by('work_id', 'desc')
                    ->limit(1)
                    ->get('works')->row();
                if(isset($first->work_id)){
                    $first = $first->work_id;
                }else{
                    $first = 0;
                }
            }
            catch(Exception $e){
                $first = 0;
            }
            $data->row->first_work_id = $first;
            $this->db->flush_cache();

            try{
                $next = $this->db
                    ->select('work_id')
                    ->where('work_id >', $data->row->work_id)
                    ->where('user_id', $user->id)
                    ->where_not_in('works.status', array('disabled', 'deleted'))
                    ->order_by('work_id', 'asc')
                    ->limit(1)
                    ->get('works')->row();
                if(isset($next->work_id)){
                    $next = $next->work_id;
                }else{
                    $next = 0;
                }
            }
            catch(Exception $e){
                $next = 0;
            }
            $data->row->next_work_id = $next;
            $this->db->flush_cache();

            try{
                $prev = $this->db
                    ->select('work_id')
                    ->where('work_id <', $data->row->work_id)
                    ->where('user_id', $user->id)
                    ->where_not_in('works.status', array('disabled', 'deleted'))
                    ->order_by('work_id', 'desc')
                    ->limit(1)
                    ->get('works')->row();
                if(isset($prev->work_id)){
                    $prev = $prev->work_id;
                }else{
                    $prev = 0;
                }
            }
            catch(Exception $e){
                $prev = 0;
            }
            $data->row->prev_work_id = $prev;
            $this->db->flush_cache();

            try{
                $last = $this->db
                    ->select('work_id')
                    ->where('user_id', $user->id)
                    ->where_not_in('works.status', array('disabled', 'deleted'))
                    ->order_by('work_id', 'asc')
                    ->limit(1)
                    ->get('works')->row();
                if(isset($last->work_id)){
                    $last = $last->work_id;
                }else{
                    $last = 0;
                }
            }
            catch(Exception $e){
                $last = 0;
            }
            $data->row->last_work_id = $last;
            $this->db->flush_cache();


        }

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
        $input = (object)$input;
        // 값을 정규식으로 검사한다.

        $input->moddate = date('Y-m-d H:i:s'); // 무조건 수정이 발생하게 하기 위하여 현재 타임스탬프로 임의로 찍어준다.
        
        //-- work id is not for update
        $work_id = $input->work_id;
        unset($input->work_id);

        if(!$this->nf->admin_is_elevated()){
            $this->db->where('user_id', USER_ID);
        }

        $this->db
            ->where('work_id', $work_id)
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
        $force_delete = (isset($data['force_delete'])?$data['force_delete']:false);

        if($this->nf->admin_is_elevated()){ // 관리자는 전지전능하심. 
            $can_delete = true;
        }
        else { // 본인것인지 여부에 따라 message다르게 하기
            $work = $this->db->where('works.work_id', $work_id)->get('works')->row();
            $can_delete = ($work->user_id == USER_ID)?true:false; 
        }

        if($can_delete){
            $this->db->flush_cache(); //clear active record

            $this->db->trans_start();
            if($this->nf->admin_is_elevated() && $force_delete)
                $this->db->where('work_id', $work_id)->delete('works'); //delete
            else
                $this->db->where('work_id', $work_id)->set('status', 'deleted')->update('works'); //mark
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
            ->distinct()
            ->select('users.*, user_profiles.keywords as user_keywords')
            ->from('hot_creators')
            ->join('users', 'hot_creators.user_id=users.id', 'left')
            ->join('user_profiles', 'hot_creators.user_id=user_profiles.user_id', 'left')
            ->order_by('hot_id', 'desc')
            ->limit(4)
            ->get()
            ->result();
        
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
            $data->message = '이미 추천되었습니다. ';

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