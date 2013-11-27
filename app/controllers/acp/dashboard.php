<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
		//$this->nf->_admin_check();
    }
	
    /**
     * lang:ko;실제 dashboard는 메인 페이지만 필요합니다.
	 *
	 */
	public function index()
	{
		$this->layout->set_view('acp/dashboard_main_view')->render();
	}
}

/* End of file dashboard.php */
/* Location: ./application/controllers/acp/dashboard.php */