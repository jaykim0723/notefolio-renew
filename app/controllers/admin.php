<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {


    public function __construct()
    {
        parent::__construct();
		$this->nf->_member_check();
		if($this->tank_auth->get_user_level()!=9)
			exit('no authorized');
        $this->load->model(array('work_model','profile_model','user_model'));
    }

    function index(){

    }

    function add_to_hot_creators(){
        $username = $this->input->post('username');
        $user = $this->user_model->get_info(array('username'=>$username));
        $user_id = $user->row->id;

        $this->db->trans_start();
        $this->db->insert('hot_creators', array(
            'hot_id' => NULL,
            'user_id' => $user_id
        ))
        $this->db->trans_complete();

        $this->layout->set_json(array('status' => $this->db->trans_status() ? 'done' : 'fail'))->render();
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */