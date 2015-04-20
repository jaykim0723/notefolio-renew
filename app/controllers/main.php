<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {


    public function __construct()
    {
        parent::__construct();
        $this->load->model('work_model');
		$this->nf->_member_check(array('update','delete'));
    }

	
	public function index()
	{
		$this->listing(1);
	}
	
	function listing($page=1){

		$work_list = $this->work_model->get_list(array(
			'allow_enabled' => true,
			'page'      => $page,
			'delimiter' => $page==1 ? 17 : 16 , // 처음일 때에는 하나를 따로 뺀다
			'correct_count' => 1,
			'order_by' => 'nofol_rank'
		));
		
		if($page==1){ // 처음 로딩될 때에
			// 첫번째 작품을 하나 불러들인다.
			$work_list->first = array_shift($work_list->rows);
			$work_list->first->key = 4; // 와이드를 위해

			// 뜨거운 작가들을 불러들인다.
			$work_list->creators = $this->work_model->get_hot_creators();
		}
		$this->layout->set_view('main/listing_view', $work_list)->render();
	}



}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */