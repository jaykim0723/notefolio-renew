<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {


    public function __construct()
    {
        parent::__construct();
		$this->nf->_member_check();
		// if($this->tank_auth->get_user_level()!=9)
			// exit('no authorized');
    }

    function index(){

    }

    function add_to_hot_creators(){
        echo 'aoesntuh';
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */