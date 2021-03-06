<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sitemap extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->output->set_content_type('text/xml');
    }
	
    /**
     * index of sitemap
	 *
	 */
	public function index()
	{	
		$data = array(
			'list'=> array(
				'root' => date('c',time()),
				),
			);

        $user_list = $this->db
        	->select('users.id, users.username')
            ->order_by('last_login', 'desc')
        	->from('users')
        	->get();

        foreach($user_list->result() as $row){
        	$data['list']['user/'.$row->username] = date('c',time());
        }
		$this->load->view('sitemap/index_view', $data);
	}
	
    /**
     * urlset of sitemap
	 *
	 */
	public function root()
	{	
		$resource = array(
			//-- main
			(object)array(
				'loc'=>'',
				'lastmod'=>time(),
				'changefreq'=>'always',
				'priority'=>0.7
				),
			(object)array(
				'loc'=>'main',
				'lastmod'=>time(),
				'changefreq'=>'always',
				'priority'=>0.7
				),

			//-- auth
			(object)array(
				'loc'=>'auth/login',
				'lastmod'=>time(),
				'changefreq'=>'monthly',
				'priority'=>0.3
				),
			(object)array(
				'loc'=>'auth/setting',
				'lastmod'=>time(),
				'changefreq'=>'monthly',
				'priority'=>0.3
				),

			//-- info
			(object)array(
				'loc'=>'info/about_us',
				'lastmod'=>time(),
				'changefreq'=>'monthly',
				'priority'=>0.3
				),
			(object)array(
				'loc'=>'info/contact_us',
				'lastmod'=>time(),
				'changefreq'=>'monthly',
				'priority'=>0.3
				),
			(object)array(
				'loc'=>'info/faq',
				'lastmod'=>time(),
				'changefreq'=>'monthly',
				'priority'=>0.3
				),
			(object)array(
				'loc'=>'info/privacy',
				'lastmod'=>time(),
				'changefreq'=>'monthly',
				'priority'=>0.3
				),
			(object)array(
				'loc'=>'info/terms',
				'lastmod'=>time(),
				'changefreq'=>'monthly',
				'priority'=>0.3
				),

			//-- gallery
			(object)array(
				'loc'=>'gallery/listing',
				'lastmod'=>time(),
				'changefreq'=>'always',
				'priority'=>0.7
				),
			(object)array(
				'loc'=>'gallery/create',
				'lastmod'=>time(),
				'changefreq'=>'always',
				'priority'=>0.3
				),
			(object)array(
				'loc'=>'gallery/update',
				'lastmod'=>time(),
				'changefreq'=>'always',
				'priority'=>0.3
				),

			//-- feed
			(object)array(
				'loc'=>'feed/listing',
				'lastmod'=>time(),
				'changefreq'=>'always',
				'priority'=>0.3
				),
			);

		$data = array(
			'list'=>$this->_make_url_list($resource),
			);

		$this->load->view('sitemap/urlset_view', $data);
	}
	
    /**
     * urlset of sitemap
	 *
	 * @param string $username
	 */
	public function user($username='')
	{
		if(empty($username)){
            $user_list = $this->db
            	->select('users.id, users.username')
            	->from('users')
            	->get();

            $resource = array();
            foreach($user_list->result() as $row){
            	$resource = array_merge(
					(array)$resource, 
					(array)$this->make_user_resource($row->username)
					);
            }

		} else {
			$resource = $this->make_user_resource($username);
		}


		$data = array(
			'list'=>$this->_make_url_list($resource),
			);

		$this->load->view('sitemap/urlset_view', $data);
	}
	
    /**
     * make user resource
	 *
	 * @param string $username
	 * @return array $resource (incl. object)
	 */
	public function make_user_resource($username='')
	{
		$this->load->model('profile_model');
		$resource = array();

		function make_resource_profile($username){
			$output = array();

			$output[] = (object)array(
				'loc'=>$username.'/myworks',
				'lastmod'=>time(),
				'changefreq'=>'always',
				'priority'=>0.8
				);
			$output[] = (object)array(
				'loc'=>$username.'/about',
				'lastmod'=>time(),
				'changefreq'=>'always',
				'priority'=>0.8
				);
			$output[] = (object)array(
				'loc'=>$username.'/collect',
				'lastmod'=>time(),
				'changefreq'=>'always',
				'priority'=>0.8
				);

			return $output;
		}

		function make_resource_works($username, $user_id, $total){
			$output = array();

			$CI =& get_instance();
	        $CI->load->model('work_model');

			$work_list = $CI->work_model->get_list(array(
				'page' => 1,
				'delimiter' => $total,
				'user_id' => $user_id,
	            'exclude_disabled'   => true, // disabled 태그된 작품 제외
	            'exclude_deleted'   => true, // deleted 태그된 작품 제외
	            'order_by'  => 'nofol_rank',
			));

			if($work_list->status=="done"){
				foreach($work_list->rows as $row){
					$output[] = (object)array(
						'loc'=>$username.'/'.$row->work_id,
						'lastmod'=>strtotime($row->moddate),
						'changefreq'=>'daily',
						'priority'=>1.0
						);
				}
			}

			return $output;
		}

		if(!empty($username)){
			$user_id = $this->profile_model->get_user_id_from_username($username);
			if($user_id>0){
				$resource = array_merge(
					(array)$resource, 
					(array)make_resource_profile($username)
					);

				$total = $this->profile_model->get_statistics_total(array('user_id'=>$user_id))->row;

				$resource = array_merge(
					(array)$resource, 
					(array)make_resource_works($username, $user_id, $total->work_cnt)
					);
			}
		}

		return $resource;
	}

	
    /**
     * make url list for sitemap
     *
     * @param array $resource (incl object)
	 * @return array (incl object)
	 */
	public function _make_url_list($resource)
	{
		$list = array();

		if(count($resource)>0){
			foreach($resource as $key=>$val){
				$list[] = (object)array(
					'loc'			=> $val->loc,
	        		'lastmod'		=> date('c',$val->lastmod),
	        		'changefreq'	=> $val->changefreq,
	        		'priority'		=> $val->priority,
					);
			}
		}

		return $list;
	}

	/**
	 * get user info
	 * 
	 * @param string $username
	 * 
	 * @return object
	 */
	function _get_user_info($username){
		$this->load->model('user_model');
		$user = $this->user_model->get_info(array(
			'username'=>$username,
			'get_profile' => TRUE
		));
		if($user->status=='fail'||count($user->row)<1)
			$user = false;

		return $user;
	}
}

/* End of file sitemap.php */
/* Location: ./application/controllers/sitemap.php */