<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
		$this->nf->admin_check();
    }
	
    /**
     * index
	 *
	 */
	public function index()
	{
		redirect('/acp/user/member');
	}
	
    /**
     * index
	 *
	 */
	public function member()
	{
		$this->layout->set_header('title', '회원')->set_view('acp/member_list_view')->render();
	}
}

/* End of file dashboard.php */
/* Location: ./application/controllers/acp/dashboard.php */