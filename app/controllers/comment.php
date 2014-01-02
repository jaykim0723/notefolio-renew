<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Comment extends CI_Controller {


    public function __construct()
    {
        parent::__construct();
        $this->load->model('comment_model');
		$this->nf->_member_check(array('create','update','delete'));
    }

    /**
     * [get_list description]
     * @param  [type] $work_id [description]
     * @return [type]          [description]
     */
	function get_list($work_id){
		$comment_list = $this->comment_model->get_list(array(
			'work_id' => $work_id,
			'id_before' => $this->input->get('idBefore')
		));
		if(!empty($comment_list)){
			foreach ($comment_list->rows as $key => $row) {
				$this->load->view('comment/comment_block_view', array('row' => $row));
			}
		}
	}



	/**
	 * [get_info description]
	 * @param  [type] $work_id    [description]
	 * @param  [type] $comment_id [description]
	 * @return [type]             [description]
	 */
	function get_info($work_id, $comment_id){
		$comment = $this->comment_model->get_info(array(
			'work_id' => $work_id,
			'comment_id' => $comment_id
		));
		$this->layout->set_view('comment/comment_block_view', $comment)->render();
	}
	

	function post($work_id){
		// 이곳에서 값을 가지고 알아서 분기한다.
		$data = $this->input->post();
		$mode = $data['mode'];
		unset($data['mode']);
		switch($mode){
			case 'create' :
			case 'reply' :
				$this->post_info($work_id, $data);
				break;

			case 'update':
				$this->put_info($work_id, $data);
				break;
		}
	}


	/**
	 * [put_info description] 새로 등록하느 것과 리플 다는 것을 모두 포함
	 * @param  [type] $work_id    [description]
	 * @param  [type] $comment_id [description]
	 * @return [type]             [description]
	 */
	function post_info($work_id, $params){
		$params['work_id'] = $work_id;
		$result = $this->comment_model->post_info($params);
		if($result->status==='fail')
			alert($result->message);
		# what else?
	}

	/**
	 * [put_info description]
	 * @param  [type] $work_id    [description]
	 * @param  [type] $comment_id [description]
	 * @return [type]             [description]
	 */
	function put_info($work_id, $params){
		$params['work_id'] = $work_id;
		$comment = $this->comment_model->get_info($params);
		if($comment->status==='fail') alert('코멘트가 존재하지 않습니다.');
		if($comment->row->user_id!==USER_ID) alert('본인의 코멘트만 수정할 수 있습니다.');

		$params['content'] = $this->input->post('content');

		$result = $this->comment_model->put_info($params);
		if($result->status==='fail')
			alert($result->message);
		# what else?
	}



	/**
	 * [delete_info description]
	 * @param  [type] $work_id    [description]
	 * @param  [type] $comment_id [description]
	 * @return [type]             [description]
	 */
	function delete_info($work_id, $comment_id){
		$params = array(
			'work_id' => $work_id,
			'comment_id' => $comment_id
		);
		$comment = $this->comment_model->get_info($params);
		if($comment->status==='fail') alert('코멘트가 존재하지 않습니다.');
		if($comment->row->user_id!==USER_ID) alert('본인의 코멘트만 삭제할 수 있습니다.');

		$result = $this->comment_model->delete_info($params);
		if($result->status==='fail')
			alert($result->message);
		# what else?
	}













	function load_wrapper(){
		$this->layout->set_view('comment/comment_wrapper_view')->render();
	}
	function load_form(){
		$this->layout->set_view('comment/comment_form_view')->render();
	}









}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */