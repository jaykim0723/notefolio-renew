<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class profile_model extends CI_Model {


    public function __construct()
    {
        parent::__construct();
        
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
                order by user_follows.id desc
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
                'keywords' => array(
                    '파인아트', '동영상', 'UI/UX'
                ),
                'recent_works' => array( // 최근 4개의 작품을 아래에 첨부하되,
                    (object)array( //  각 객체는 work_list에서 쓰는 그 테이블을 그대로 쓴다. 단, 어차피 유저에 한하므로 user 정보는 필요없다.
                        'work_id' => 239,
                        'title' => 'Lorem ipsum natohe',
                        'modified' => '2013-08-01 11:11:11'
                    ),
                    (object)array(
                        'work_id' => 239,
                        'title' => 'Lorem ipsum natohe',
                        'modified' => '2013-08-01 11:11:11'
                    ),
                    (object)array(
                        'work_id' => 239,
                        'title' => 'Lorem ipsum natohe',
                        'modified' => '2013-08-01 11:11:11'
                    ),
                    (object)array(
                        'work_id' => 239,
                        'title' => 'Lorem ipsum natohe',
                        'modified' => '2013-08-01 11:11:11'
                    )
                ),
                'is_follow' => ($row->is_follow==1 ? 'y' : 'n') // 기존에 어떤명을 했는지 잘 기억이...
            );
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

        /* 성수씨 부탁해요~ 
        $this->db
            ->select('works.*, users.*, users.user_id as user_id')
            // ->select('work_id, title, realname, regdate, keywords, tags, user_id, folder, contents, moddate, hit_cnt, note_cnt, comment_cnt, collect_cnt, ccl, discoverbility')
            ->from('works')
            ->join('users', 'users.id = works.user_id', 'left')
            ->limit($params->delimiter, ((($params->page)-1)*$params->delimiter)); //set
        $works = $this->db->get();
        */

        $rows = array();
        // foreach ($works->result() as $row)
        for ($i=0; $i<$params->delimiter; $i++) // 일단 dummy 정보를 이용한다.
        {
            // 값을 조작해야할 필요가 있을 때에는 여기에서 한다
            $row = (object)array(
                'user_id'         => 234, 
                'username'   => 'maxzidell',
                'email'      => 'zidell@gmail.com',
                'level'      => 2,
                'realname'   => '이흥현',
                'created'    => '2012-01-02 10:20:10',
                'modified'   => '2013-08-01 11:11:11', // profile 사진 갱신을 위해서 필요하다.
                'keywords' => array(
                    '파인아트', '동영상', 'UI/UX'
                ),
                'recent_works' => array( // 최근 4개의 작품을 아래에 첨부하되,
                    (object)array( //  각 객체는 work_list에서 쓰는 그 테이블을 그대로 쓴다. 단, 어차피 유저에 한하므로 user 정보는 필요없다.
                        'work_id' => 239,
                        'title' => 'Lorem ipsum natohe',
                        'modified' => '2013-08-01 11:11:11'                        
                    ),
                    (object)array(
                        'work_id' => 239,
                        'title' => 'Lorem ipsum natohe',
                        'modified' => '2013-08-01 11:11:11'                        
                    ),
                    (object)array(
                        'work_id' => 239,
                        'title' => 'Lorem ipsum natohe',
                        'modified' => '2013-08-01 11:11:11'                        
                    ),
                    (object)array(
                        'work_id' => 239,
                        'title' => 'Lorem ipsum natohe',
                        'modified' => '2013-08-01 11:11:11'                        
                    )
                ),
                'is_follow' => (rand(0,1)==1 ? 'y' : 'n') // 기존에 어떤명을 했는지 잘 기억이...
            );
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




}

/* End of file work_model.php */
/* Location: ./application/models/work_model.php */