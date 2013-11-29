<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
		$this->nf->admin_check();
		$this->load->model('user_model');
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
     * get member list
	 *
	 */
	public function member()
	{
		$data = $this->user_model->get_list();

		$this->layout->set_header('title', '회원')->set_view('acp/user_member_list_view',$data)->render();
	}
}

/* End of file dashboard.php */
/* Location: ./application/controllers/acp/dashboard.php */