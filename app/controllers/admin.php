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
        $last_record = $this->db->select('hot_id, user_id')->order_by('hot_id', 'desc')->limit(1)->get('hot_creators')->row();
        if($last_record && $last_record->user_id == $user_id){
            // 가장 마지막에 추가된 작가가 다시 들어오는 것이라면 취소다.
            $msg = $username.'님이 핫작가에서 제외되었습니다.';
            $this->db->where('hot_id', $last_record->hot_id)->delete('hot_creators');
        }else{
            $msg = $username.'님이 핫작가에 등록되었습니다.';
            $this->db->insert('hot_creators', array(
                'hot_id' => NULL,
                'user_id' => $user_id
            ));
        }
        $this->db->trans_complete();
        $this->layout->set_json(array('msg' => $msg, 'status' => $this->db->trans_status() ? 'done' : 'fail'))->render();
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */