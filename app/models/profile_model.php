<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class profile_model extends CI_Model {


    public function __construct()
    {
        parent::__construct();
        
    }

    function set_change_color($user_id, $color){
        $this->db->set('face_color', $color)->where('user_id', $user_id)->update('user_profiles');
        $data = (object)array(
            'status' => 'done'
        );
        if($this->db->affected_rows() == 0){
            $data->status = 'fail';
        }
        return $data;
    }
    function get_user_id_from_username($username=''){
        return $this->db->select('id as user_id')->where('username', $username)->get('users')->row()->user_id;
    }


    function get_collection_list($params=array()){
    	$params = (object)$params;
    	$default_params = (object)array(
            'page'      => 1, // 불러올 페이지
            'delimiter' => 12, // 한 페이지당 작품 수
            'order_by'  => 'newest', // newest, oldest
            'folder'    => '', // ''면 전체
            'user_id'   => '' // 어느 작가의 콜렉션인지
    	);
    	foreach($default_params as $key => $value){
    		if(!isset($params->{$key}))
    			$params->{$key} = $value;
    	}

    	$this->db
            ->select('works.*, users.*, users.id as user_id')
    		// ->select('work_id, title, realname, regdate, keywords, tags, user_id, folder, contents, moddate, hit_cnt, note_cnt, comment_cnt, collect_cnt, ccl, discoverbility')
    		->from('user_work_collect')
            ->join('works', 'user_work_collect.work_id = works.work_id', 'left')
            ->join('users', 'users.id = works.user_id', 'left')
            ->where('user_work_collect.user_id', $params->user_id)
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

    	$works = $this->db->get();

        $rows = array();
        foreach ($works->result() as $row)
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








    function get_followings_list($params=array()){
        $params = (object)$params;
        $default_params = (object)array(
            'page'      => 1, // 불러올 페이지
            'delimiter' => 12, // 한 페이지당 작품 수
            'order_by'  => 'newest', // newest, oldest 기초는 newest
            'user_id'   => '' // 어느 작가의 리스트인지
        );
        foreach($default_params as $key => $value){
            if(!isset($params->{$key}))
                $params->{$key} = $value;
        }

        $sql = "SELECT list.follow_id,
                     if(isnull(now_following.follow_id), 0, 1) as is_follow,
                     following_users.*
                from user_follows list
                left join (
                    select users.id as user_id, users.username, users.email,
                     users.realname, users.created, users.modified,
                     user_profiles.keywords
                    from users
                    left join user_profiles on users.id = user_profiles.user_id
                ) following_users on list.follow_id = following_users.user_id
                left join (
                    select follow_id
                    from user_follows
                    where follower_id = ?
                ) now_following on list.follow_id = now_following.follow_id
                where list.follower_id = ?
                order by list.id desc
                limit ?, ?;
                "; // raw query :)
        $query = $this->db->query($sql, array(USER_ID, $params->user_id, ((($params->page)-1)*$params->delimiter), $params->delimiter));
        
        $rows = array();
        foreach($query->result() as $row)
        {
            // 값을 조작해야할 필요가 있을 때에는 여기에서 한다
            $row = (object)array(
                'user_id'    => $row->user_id,
                'username'   => $row->username,
                'email'      => $row->email,
                'realname'   => $row->realname,
                'created'    => $row->created,
                'modified'   => $row->modified, // profile 사진 갱신을 위해서 필요하다.
                'user_keywords' => $row->keywords,
                'recent_works' => array(), // 최근 4개의 작품을 아래에 첨부하되, 각 객체는 work_list에서 쓰는 그 테이블을 그대로 쓴다. 단, 어차피 유저에 한하므로 user 정보는 필요없다.
                'is_follow' => ($row->is_follow==1 ? 'y' : 'n') // 기존에 어떤명을 했는지 잘 기억이...
            );

            //-- user's work, recent 4, first page
            $work_query = $this->db->query(''
                .'SELECT 
                    user_id, work_id, title, moddate
                FROM
                    works
                WHERE
                    user_id = ?
                order by moddate desc
                LIMIT ?, ?;', array($row->user_id, 0, 4));
            foreach ($work_query->result() as $work_row){
                $row->recent_works[] = (object)array(
                        'work_id' => $work_row->work_id,
                        'title' => $work_row->title,
                        'modified' => $work_row->moddate                      
                    );
            }
            //-- end
            
            $rows[] = $row;
        }

        $data = (object)array(
            'status' => 'done',
            'mode'   => 'followings',
            'page'   => $params->page,
            'rows'   => $rows
        );
        if(sizeof($rows)==0){
            $data->status = 'fail';
            return $data;
        }
        return $data;
    }



    function get_followers_list($params=array()){
        $params = (object)$params;
        $default_params = (object)array(
            'page'      => 1, // 불러올 페이지
            'delimiter' => 12, // 한 페이지당 작품 수
            'order_by'  => 'newest', // newest, oldest 기초는 newest
            'user_id'   => '' // 어느 작가의 리스트인지
        );
        foreach($default_params as $key => $value){
            if(!isset($params->{$key}))
                $params->{$key} = $value;
        }

        $sql = "SELECT list.follower_id,
                     if(isnull(now_following.follow_id), 0, 1) as is_follow,
                     following_users.*
                from user_follows list
                left join (
                    select users.id as user_id, users.username, users.email,
                     users.realname, users.created, users.modified,
                     user_profiles.keywords
                    from users
                    left join user_profiles on users.id = user_profiles.user_id
                ) following_users on list.follower_id = following_users.user_id
                left join (
                    select follow_id
                    from user_follows
                    where follower_id = ?
                ) now_following on list.follower_id = now_following.follow_id
                where list.follow_id = ?
                order by list.id desc
                limit ?, ?;
                "; // raw query :)
        $query = $this->db->query($sql, array(USER_ID, $params->user_id, ((($params->page)-1)*$params->delimiter), $params->delimiter));
        
        $rows = array();
        foreach($query->result() as $row)
        {
            // 값을 조작해야할 필요가 있을 때에는 여기에서 한다
            $row = (object)array(
                'user_id'    => $row->user_id,
                'username'   => $row->username,
                'email'      => $row->email,
                'realname'   => $row->realname,
                'created'    => $row->created,
                'modified'   => $row->modified, // profile 사진 갱신을 위해서 필요하다.
                'user_keywords' => $row->keywords,
                'recent_works' => array(), // 최근 4개의 작품을 아래에 첨부하되, 각 객체는 work_list에서 쓰는 그 테이블을 그대로 쓴다. 단, 어차피 유저에 한하므로 user 정보는 필요없다.
                'is_follow' => ($row->is_follow==1 ? 'y' : 'n') // 기존에 어떤명을 했는지 잘 기억이...
            );

            //-- user's work, recent 4, first page
            $work_query = $this->db->query(''
                .'SELECT 
                    user_id, work_id, title, moddate
                FROM
                    works
                WHERE
                    user_id = ?
                order by moddate desc
                LIMIT ?, ?;', array($row->user_id, 0, 4));
            foreach ($work_query->result() as $work_row){
                $row->recent_works[] = (object)array(
                        'work_id' => $work_row->work_id,
                        'title' => $work_row->title,
                        'modified' => $work_row->moddate                      
                    );
            }
            //-- end

            $rows[] = $row;

        }

        $data = (object)array(
            'status' => 'done',
            'mode'   => 'followers',
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
     * post follow for work
     * 
     * @param  array  $params 
     * @return object          상태와 데이터값을 반환한다
     */
    function post_follow($params=array()){
        $params = (object)$params;
        $default_params = (object)array(
            'follower_id'   => USER_ID,
            'follow_id'   => '',
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

        if(!empty($params->follower_id) && $params->follower_id>0){
            
            $query = $this->db
                ->where(array(
                    'follower_id'=>$params->follower_id,
                    'follow_id'=>$params->follow_id
                    ))
                ->get('user_follows');
            if($query->num_rows()>0){
                $data->status = 'fail';
                $data->message = 'already_followed';

                return $data;
            }
            $query->free_result();

            $this->db->trans_start();
            try{ 
                $this->db->insert('user_follows', $params);
            }
            catch(Exception $e){
                $data->status = 'fail';
                $data->message = 'no_db_insert';

                return $data;
            } 
            $affected = $this->db->affected_rows();
            $this->db->trans_complete();

            if($this->db->trans_status()){
                $this->db->query("UPDATE user_profiles 
                    set following_cnt = following_cnt + {$affected} 
                    where user_id = {$params->follower_id};
                    ");
                $this->db->query("UPDATE user_profiles 
                    set follower_cnt = follower_cnt + {$affected} 
                    where user_id = {$params->follow_id};
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
     * delete follow for work
     * 
     * @param  array  $params 
     * @return object          상태와 데이터값을 반환한다
     */
    function delete_follow($params=array()){
        $params = (object)$params;
        $default_params = (object)array(
            'follower_id'   => USER_ID,
            'follow_id'   => '',
        );
        foreach($default_params as $key => $value){
            if(!isset($params->{$key}))
                $params->{$key} = $value;
        }

        $data = (object)array(
                'status' => 'fail',
                'message' => 'no_process'
            );

        if(!empty($params->follower_id) && $params->follower_id>0
            && !empty($params->follow_id) && $params->follow_id>0
            ){
            
            $query = $this->db
                ->where(array(
                    'follower_id'=>$params->follower_id,
                    'follow_id'=>$params->follow_id
                    ))
                ->get('user_follows');
            if($query->num_rows()==0){
                $data->status = "fail";
                $data->message = 'no_followed';

                return $data;
            }
            $query->free_result();
            
            $this->db->trans_start();
            try{ 
                $this->db
                    ->where(array(
                        'follower_id'=>$params->follower_id,
                        'follow_id'=>$params->follow_id
                        ))
                    ->delete('user_follows');
            }
            catch(Exception $e){
                $data->status = "fail";
                $data->message = 'no_db_delete';

                return $data;
            } 
            $affected = $this->db->affected_rows();
            $this->db->trans_complete();

            if($this->db->trans_status()){
                $this->db->query("UPDATE user_profiles 
                    set following_cnt = following_cnt - {$affected} 
                    where user_id = {$params->follower_id};
                    ");
                $this->db->query("UPDATE user_profiles 
                    set follower_cnt = follower_cnt - {$affected} 
                    where user_id = {$params->follow_id};
                    ");
                $data->status = 'done';
                $data->message = 'successed';
            }

        }
        else{

        }
        return $data;
    }







    function get_about($params){
        log_message('debug','--------- profile_model > get_about ( params : '.print_r(get_defined_vars(),TRUE)).')';
        $params = (object)$params;
        $default_params = (object)array(
            'user_id'   => ''
        );
        foreach($default_params as $key => $value){
            if(!isset($params->{$key}))
                $params->{$key} = $value;
        }

        $data = (object)array(
            'status' => 'done',
            'row' => (object)array()
        );
        $row = $this->db->where('user_id', $params->user_id)->get('user_about')->row();
        if(!$row){
            $row = (object)array(
                'contents'    => '',
                'attachments' => serialize(array())
            );
        }
        $data->row->attachments = unserialize($row->attachments);
        
        if(count($data->row->attachments)>0){
            $attachments = $this->db->select('id, filename')
                ->where_in('id', $data->row->attachments)
                ->where('user_id', $params->user_id)
                ->get('uploads')->result();
        }
        
        $data->row->attachments = array();
        if(!empty($attachments)){
            foreach($attachments as $attachment){
                $data->row->attachments[] = array(
                    'upload_id' => $attachment->id,
                    'src' => str_replace('.jpg', '_v2.jpg', $attachment->filename) # 성수씨 여기
                );
            }
        }
        $data->row->contents = $row->contents;
        return $data;
    }


    function put_about($params){
        log_message('debug','--------- profile_model > put_about ( params : '.print_r(get_defined_vars(),TRUE)).')';
        $params = (object)$params;
        $default_params = (object)array(
            'user_id'   => '',
            'contents' => '',
            'attachments' => array()
        );
        foreach($default_params as $key => $value){
            if(!isset($params->{$key}))
                $params->{$key} = $value;
        }

        $data = (object)array(
            'status' => 'done'
        );

        $this->db
            ->set('contents', $params->contents)
            ->set('attachments', serialize($params->attachments));


        $row = $this->db->where('user_id', $params->user_id)->get('user_about')->row();
        if($row){
            $this->db
            ->where('user_id', $params->user_id)
            ->update('user_about');
        } else {
            $this->db
                ->set('user_id', $params->user_id)
                ->insert('user_about');
        }
        return $data; 
    }






    /**
     * 특정 작가의 모든 누적수치 반환
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    function get_statistics_total($params){
        log_message('debug','--------- profile_model > get_statistics_total ( params : '.print_r(get_defined_vars(),TRUE)).')';
        $params = (object)$params;
        $default_params = (object)array(
            'user_id'   => ''
        );
        foreach($default_params as $key => $value){
            if(!isset($params->{$key}))
                $params->{$key} = $value;
        }
        $data = (object)array(
            'status' => 'done',
            'row' => (object)array(
                'work_cnt' => 0,
                'hit_cnt' => 0,
                'note_cnt' => 0,
                'collect_cnt' => 0,
                'following_cnt' => 0,
                'follower_cnt' => 0
            )
        );

        # do stuff
        # 성수씨 호출
        # ex) $data->row->work_cnt = 234;

        return $data; 

    }

    /**
     * 특정 기간내의 작가별 조회수, 노트수, 콜렉트 수 반환
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    function get_statistics_count($params){
        log_message('debug','--------- profile_model > get_statistics_count ( params : '.print_r(get_defined_vars(),TRUE)).')';
        $params = (object)$params;
        $default_params = (object)array(
            'user_id'   => '',
            'sdate' => '',
            'edate' => ''
        );
        foreach($default_params as $key => $value){
            if(!isset($params->{$key}))
                $params->{$key} = $value;
        }
        $data = (object)array(
            'status' => 'done',
            'sdate' => $params->sdate,
            'edate' => $params->edate,
            'row' => (object)array(
                'hit_cnt' => 12,
                'note_cnt' => 34,
                'collect_cnt' => 56,
            )
        );

        # do stuff
        # 성수씨 호출
        # ex) $data->row->hit_cnt = 2;
        # ex) $data->row->note_cnt = 2;
        # ex) $data->row->collect_cnt = 2;

        return $data; 
    } 


    /**
     * 특정 기간내의 작가의 일자별 조회수나 노트수나 등등..
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    function get_statistics_chart($params){
        log_message('debug','--------- profile_model > get_statistics_chart ( params : '.print_r(get_defined_vars(),TRUE)).')';
        $params = (object)$params;
        $default_params = (object)array(
            'user_id'   => '',
            'type' => 'hit',
            'sdate' => '',
            'edate' => ''
        );
        foreach($default_params as $key => $value){
            if(!isset($params->{$key}))
                $params->{$key} = $value;
        }


        $data = (object)array(
            'status' => 'done',
            'type' => $params->type,
            'sdate' => $params->sdate,
            'edate' => $params->edate,
            'rows' => array(
                // '2014-01-02' => 0,
            )
        );
        // 차트에서는 값이 0인 날도 누락되면 안되므로, 먼저 rows에 기간내의 모든 일자별을 셋팅을 만들어둔다.
        $start_timestamp = strtotime($params->sdate);
        for($i=0; $i<9999; $i++){
            $label_date = date('Y-m-d', strtotime('+'.$i.' days', $start_timestamp));
            $data->rows[$label_date] = 0;
            if($label_date == $params->edate)
                break;
        }
        // dummy
        foreach($data->rows as &$r)
            $r = rand(0,9999);


        # do stuff
        # 성수씨 호출
        # 관련된 쿼리를 호출하고 날짜별로 값을 대입한다.
        # ex) $data->rows['2013-01-02'] = $row->date;

        return $data; 
    }

    /**
     * 특정 기간내의 작가의 데이터테이블
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    function get_statistics_datatable($params){
        log_message('debug','--------- profile_model > get_statistics_datatable ( params : '.print_r(get_defined_vars(),TRUE)).')';
        $params = (object)$params;
        $default_params = (object)array(
            'user_id'   => '',
            'sdate' => '',
            'edate' => ''
        );
        foreach($default_params as $key => $value){
            if(!isset($params->{$key}))
                $params->{$key} = $value;
        }

        $data = (object)array(
            'status' => 'done',
            'sdate' => $params->sdate,
            'edate' => $params->edate,
            'rows' => array(
                // array( // 각 형식은 아래와 같이 전달해주면 됩니다.
                //     'work_id' => 23,
                //     'title' => '테스트작품',
                //     'regdate' => '2014-01-01',
                //     'hit_cnt' => 1,
                //     'note_cnt' => 1,
                //     'collect_cnt' => 1,
                // )
            )
        );

        // dummy
        $dummy = $this->db->select('work_id, title, LEFT(regdate, 10) as regdate, hit_cnt, note_cnt, collect_cnt', FALSE)->limit(200)->get('works')->result();
        foreach($dummy as $r)
            $data->rows[] = $r;

        # do stuff
        # 성수씨 호출

        return $data; 
    }

}

/* End of file work_model.php */
/* Location: ./application/models/work_model.php */