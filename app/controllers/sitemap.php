<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sitemap extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
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
				'user' => date('c',time()),
				),
			);
		$this->load->view('sitemap/index_view', $data);
	}
	
    /**
     * urlset of sitemap
	 *
	 */
	public function root()
	{	
		$resource = array(
			(object)array(
				'loc'=>'/',
				'lastmod'=>time(),
				'changefreq'=>'always',
				'priority'=>0.7
				),
			(object)array(
				'loc'=>'/auth/login',
				'lastmod'=>time(),
				'changefreq'=>'monthly',
				'priority'=>0.3
				),
			(object)array(
				'loc'=>'/auth/setting',
				'lastmod'=>time(),
				'changefreq'=>'monthly',
				'priority'=>0.3
				),
			);

		$data = array(
			'list'=>array();
			);

		foreach($resource as $key=>$val){
			$data['list'][] = (object)array(
				'loc'			=> $val->loc,
        		'lastmod'		=> date('c',$val->lastmod),
        		'changefreq'	=> $val->changefreq,
        		'priority'		=> $val->priorty,
				);
		}

		$this->load->view('sitemap/urlset_view', $data);
	}
}

/* End of file sitemap.php */
/* Location: ./application/controllers/sitemap.php */