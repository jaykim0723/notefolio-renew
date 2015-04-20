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
	function read_list($work_id){
		log_message('debug','--------- comment.php > read_list ( params : '.print_r(get_defined_vars(),TRUE)).')';
		$comment_list = $this->comment_model->get_list(array(
			'work_id' => $work_id,
			'id_before' => $this->input->get('id_before'),
			'delimiter' => $this->input->get('delimiter')
		));
		if(!empty($comment_list)){
			$comment_list->rows = array_reverse($comment_list->rows);
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
	function read_info($work_id, $comment_id){
		$comment = $this->comment_model->get_info(array(
			'work_id' => $work_id,
			'comment_id' => $comment_id
		));
		$this->layout->set_view('comment/comment_block_view', $comment)->render();
	}
	

	function post($work_id){
		log_message('debug','--------- comment.php > post ( params : '.print_r(get_defined_vars(),TRUE)).')';
		if(USER_ID==0)
			alert('회원만이 코멘트를 남길 수 있습니다.');
		// 이곳에서 값을 가지고 알아서 분기한다.
		$data = $this->input->post();
		log_message('debug','------inputs : '.print_r($data,TRUE));
		$mode = $data['mode'];
		unset($data['mode']);
		switch($mode){
			case 'create' :
			case 'reply' :
				$this->create_info($work_id, $data);
				break;

			case 'update':
				$this->update_info($work_id, $data);
				break;
			
			case 'delete':
				$this->delete_info($work_id, $data);
				break;
		}
	}


	/**
	 * [put_info description] 새로 등록하느 것과 리플 다는 것을 모두 포함
	 * @param  [type] $work_id    [description]
	 * @param  [type] $comment_id [description]
	 * @return [type]             [description]
	 */
	function create_info($work_id, $params){
		$params['work_id'] = $work_id;
		$result = $this->comment_model->post_info($params);
		log_message('debug','---------'.print_r($result,TRUE));
		if($result->status=='fail'){
			alert($result->message);
        }

		log_message('debug','-----'.$result->comment_id);
		$comment = $this->comment_model->get_info(array(
			'work_id' => $work_id,
			'comment_id' => $result->comment_id
		));
		// 화면에 출력을 하도록 출력해주기
		$this->load->view('comment/comment_block_view', $comment, FALSE);

        if($result->status=="done"){
            //-- write activity
            $this->load->library('activity');
            $this->activity->post(array(
                'crud' => 'create',
                'area' => 'work',
                'type'  => 'comment',
                'user_A' => USER_ID,
                'work_id' => $params['work_id'],
                'parent_id' => $params['parent_id'],
                'comment' => $params['content'],
                ));
            if($params['parent_id']==0){
            	//-- facebook post 
                $fb_query = http_build_query(array(
                	'user_id'=>USER_ID, 
                	'post_type'=>'post_comment', 
                	'work_id'=>$params['work_id'], 
                	'base_url'=>$this->config->item('base_url')
                	));
                $cmd = 'php '.$this->input->server('DOCUMENT_ROOT').'/../app-cli/cli.php Fbconnect post "'.$fb_query.'"';
                exec($cmd . " > /dev/null &");  
                //error_log($cmd);
                //$this->fbsdk->post_data($this->tank_auth->get_user_id(), array('type'=>'post_comment', 'work_id'=>$this->input->post('work_id')));
            }
        }
	}

	/**
	 * [put_info description]
	 * @param  [type] $work_id    [description]
	 * @param  [type] $comment_id [description]
	 * @return [type]             [description]
	 */
	function update_info($work_id, $params){
		log_message('debug','--------- comment.php > update_info ( params : '.print_r(get_defined_vars(),TRUE)).')';
		$params['work_id'] = $work_id;
		$comment = $this->comment_model->get_info($params);
		if($comment->status==='fail') alert('코멘트가 존재하지 않습니다.');
		if($comment->row->user->user_id!==USER_ID) alert('본인의 코멘트만 수정할 수 있습니다.');

		$result = $this->comment_model->put_info($params);
		if($result->status==='fail')
			alert($result->message);

		$comment = $this->comment_model->get_info($params);
		// 화면에 출력을 하도록 출력해주기
		$this->load->view('comment/comment_block_view', $comment, FALSE);

        if($result->status=="done"){
            //-- write activity
            $this->load->library('activity');
            $this->activity->post(array(
                'crud' => 'update',
                'area' => 'work',
                'type'  => 'comment',
                'user_A' => USER_ID,
                'work_id' => $params['work_id'],
                'comment' => $params['content'],
                ));
        }
	}



	/**
	 * [delete_info description]
	 * @param  [type] $work_id    [description]
	 * @param  [type] $comment_id [description]
	 * @return [type]             [description]
	 */
	function delete_info($work_id, $params){
		log_message('debug','--------- comment.php > delete_info ( params : '.print_r(get_defined_vars(),TRUE)).')';
		$params['work_id'] = $work_id;
		$comment = $this->comment_model->get_info($params);
		if($comment->status==='fail') alert('코멘트가 존재하지 않습니다.');
		if($comment->row->user->user_id!==USER_ID) alert('본인의 코멘트만 삭제할 수 있습니다.');

		$result = $this->comment_model->delete_info($params);
		if($result->status==='fail')
			alert($result->message);

        if($result->status=="done"){
            //-- write activity
            $this->load->library('activity');
            $this->activity->post(array(
                'crud' => 'delete',
                'area' => 'work',
                'type'  => 'comment',
                'user_A' => USER_ID,
                'work_id' => $params['work_id'],
                ));
        }
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