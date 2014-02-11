<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class work extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
		$this->nf->admin_check();

		$this->load->helper('form');
    }
	
    /**
     * index
	 *
	 */
	public function index()
	{
		redirect('/acp/work/works');
	}
    
    
    /*
     * @brief return access log page
     * 
     * @param string $mode
     * 
     * @return null
     */
    function works($mode='list')
    {
        $args = $this->uri->ruri_to_assoc(4);
        //var_export($args);
                
        switch($mode) {
            case "list":
                if(!isset($args['page'])) $args['page'] = 1;
                if(!isset($args['delimiter'])) $args['delimiter'] = 30;
                if(!isset($args['order'])) $args['order'] ="id=desc";
                parse_str(str_replace(array(":",'+'), array("=", "&"), $args['order']), $args['order']);
                if(!isset($args['search'])) $args['search'] ="";
                parse_str(str_replace(array(":",'+'), array("=", "&"), $args['search']), $args['search']);

                if(is_array($args['search'])&&count($args['search'])>0){
                    foreach($args['search'] as $q_key=>$q_val){
                        $this->db->where($q_key, $q_val);
                    }
                }

                $page_info = $this->db
                    ->select('count(*) as count, ceil(count(*)/'.$args['delimiter'].') as all_page')
                    ->get('works')->result_array();
                error_log($this->db->last_query());


                if(is_array($args['search'])&&count($args['search'])>0){
                    foreach($args['search'] as $q_key=>$q_val){
                        $this->db->where($q_key, $q_val);
                    }
                }

                if(is_array($args['order'])&&count($args['order'])>0){
                    foreach($args['order'] as $o_key=>$o_val){
                        $this->db->order_by($o_key,$o_val);
                    }
                }

                $limit = array($args['page'], $args['delimiter']);

                $data['list'] = $this->db
                    ->select('works.*')
                    ->limit($limit[1],($limit[0]-1)*$limit[1])
                    ->join('users', 'works.user_id=users.id', 'left')
                    ->join('user_profiles', 'users.id=user_profiles.user_id', 'left')
                    ->get('works')->result_array();
                error_log($this->db->last_query());

                $data['all_count'] = isset($page_info[0])?$page_info[0]['count']:0;
                $data['all_page'] = isset($page_info[0])?$page_info[0]['all_page']:1;
                $data['now_page'] = isset($args['page'])?$args['page']:1;
                $data['delimiter'] = isset($args['page'])?$args['delimiter']:30;

                $data['page'] = $args['page'];
                break;
            case "view":
                if (isset($args['id'])) {
                    $data['view'] = $this->log_db->_get_list('access', array('id'=>$args['id']), array(), array(1,1));
                } else {
                   redirect('/acp/work/works/list/');
                }
                break;
            case "write":
                $data['field'] = array();
                
                break;
            case "modify":
                if (isset($args['id'])) {
                    $form_data = $this->log_db->_get_list('access', array('id'=>$args['id']), array(), array(1,1));
                    $data['field'] = $form_data[0];
                } else {
                   redirect('/acp/work/works/list/');
                }
                break;
            case "delete":
                if (isset($args['id'])) {
                    $form_data = $this->log_db->_get_list('access', array('id'=>$args['id']), array(), array(1,1));
                    $data['field'] = $form_data[0];
                } else {
                   redirect('/acp/work/works/list/');
                }
                break;
            case "proc":
                return $this->_works_proc($this->input->post('mode'));
                break;
            default:
                break;
        }

        $data['form_attr'] = array('class' => 'form', 'id' => 'works_'.$mode.'_form');
        $this->layout->set_header('title', '작품 목록')->set_view('acp/work_works_'.$mode,$data)->render();
    }

    
    /*
     * @brief process for access log
     * 
     * @param string $mode
     * 
     * @return null
     */
    function _works_proc($mode)
    {
        switch($mode) {
            case "write":
                $prefix = $this->input->post('prefix');
                $length = $this->input->post('length');
                $amount = $this->input->post('amount');
                $comment = $this->input->post('comment');
                
                if (!$prefix) {
                    alert('접두어가 필요합니다.');
                    redirect('/acp/work/works/write/');                    
                } else if (!$length>0) {
                    alert('길이가 지나치게 작습니다.');
                    redirect('/acp/work/works/write/');                    
                } else if (!$amount>1) {
                    alert('수량은 적어도 1개 이상입니다.');
                    redirect('/acp/work/works/write/');                    
                }
                
        
                $this->load->library('codegen');
                
                for($i=0;$i<$amount;$i++) {
                    $code = $this->codegen->get_code($length, 0, 'time');
                    if($this->invite_code_db->_get_list(array('code'=>$prefix.$code), array(), array(1,1))==array()){
                        $this->invite_code_db->_insert(array('code'=>$prefix.$code, 'comment'=>$comment));
                    } else {
                        //log_message('debug', var_export(($this->invite_code_db->_get_list(array('code'=>$prefix.$code), '', array(1,1))!=array()), true));
                        $i--;
                    }
                }
                
                redirect('/acp/work/works/list/');
                
                break;
            case "modify":
                
                $param = array();
                if($this->input->post('code')) $param['code']=$this->input->post('code');
                if($this->input->post('regdate')) $param['regdate']=$this->input->post('regdate');
                if($this->input->post('moddate')) $param['moddate']=$this->input->post('moddate');
                if($this->input->post('user_id')) $param['user_id']=$this->input->post('user_id');
                if($this->input->post('generate_user_id')) $param['generate_user_id']=$this->input->post('generate_user_id');
                if($this->input->post('comment')) $param['comment']=$this->input->post('comment');
                
                //var_export($this->input->post());

                $this->invite_code_db->_update($this->input->post('id'),$param);
                redirect('/acp/work/works/view/id/'.$this->input->post('id'));
                
                break;
            case "delete":
                
                $this->invite_code_db->_delete($this->input->post('id'));
                redirect('/acp/work/works/list/');
                
                break;
        }
    }
}

/* End of file dashboard.php */
/* Location: ./application/controllers/acp/dashboard.php */