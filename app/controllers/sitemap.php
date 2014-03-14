<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class sitemap extends CI_Controller {

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
		exit('a');

		$data = array(
			'list'=>array(
				'default' => date('c',time()),
				'user' => date('c',time()),
				),
			);
		$this->load->view('sitemap/index_view', $data);
	}
	
    /**
     * urlset of sitemap
	 *
	 */
	public function default()
	{	
		$data = array(
			'list'=>array(
				'default' => date('c',time()),
				'user' => date('c',time()),
				),
			);
		$this->load->view('sitemap/urlset_view', $data);
	}
}

/* End of file sitemap.php */
/* Location: ./application/controllers/sitemap.php */