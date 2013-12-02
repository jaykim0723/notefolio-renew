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
	public function member($mode='list')
	{
		if($this->uri->segment(4)==FALSE) redirect('/acp/user/member/list');
		$args = $this->uri->uri_to_assoc(5);

		switch($mode){
			case "list":
				$data = $this->user_model->get_list($args);
			break;
			case "write":
				$data = new Object();
			case "modify":
			case "view":
			case "del":
				$args['get_profile'] = true;
				$args['get_sns_fb'] = true;
				$data = $this->user_model->get($args);
			break;
			default:
				exit('error');
			break;
		}

		$this->layout->set_header('title', '회원')->set_view('acp/user_member_'.$mode.'_view',$data)->render();
	}
}

/* End of file dashboard.php */
/* Location: ./application/controllers/acp/dashboard.php */