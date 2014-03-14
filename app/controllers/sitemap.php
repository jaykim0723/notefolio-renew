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
}

/* End of file sitemap.php */
/* Location: ./application/controllers/sitemap.php */