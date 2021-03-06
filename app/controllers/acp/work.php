<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class work extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
		$this->nf->admin_check();

		$this->load->helper('form');
        $this->load->model('work_model');
    }
	
    /**
     * index
	 *
	 */
	public function index()
	{
		redirect('/acp/work/works/list');
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
        if($this->uri->segment(4)==FALSE) redirect('/acp/work/works/list');
        $args = $this->uri->ruri_to_assoc(4);
        //var_export($args);
                
        switch($mode) {
            case "list":
                $data['args'] = $args;
                if(!isset($args['page'])) $args['page'] = 1;
                if(!isset($args['delimiter'])) $args['delimiter'] = 30;
                if(!isset($args['keywords'])) $args['keywords'] = array();
                if(!isset($args['order'])) $args['order'] = 'idlarger';
                if(!isset($args['allow_enabled'])) $args['allow_enabled'] = true;
                if(!isset($args['allow_disabled'])) $args['allow_disabled'] = true;
                if(!isset($args['allow_deleted'])) $args['allow_deleted'] = false;
                if(!isset($args['exclude_enabled'])) $args['exclude_enabled'] = false;
                if(!isset($args['exclude_disabled'])) $args['exclude_disabled'] = false;
                if(!isset($args['exclude_deleted'])) $args['exclude_deleted'] = false;
                if(!isset($args['sp_nz'])) $args['sp_nz'] = false;
                if(!isset($args['period'])) $args['period'] = 'all';
                if(!isset($args['q'])) $args['q'] = '';

                foreach(array('enabled', 'disabled', 'deleted') as $val){
                    if(isset($args['only_'.$val]) && $args['only_'.$val] == true){
                        $args['allow_enabled'] = false;
                        $args['allow_disabled'] = false;
                        $args['allow_deleted'] = false;

                        $args['allow_'.$val] = true;
                    }
                }

                //-- category
                $this->load->config('keyword', TRUE);
                $keyword_list = $this->config->item('keyword', 'keyword');

                foreach ($keyword_list as $key => $val) {
                    if(isset($args['cat_'.$key]) && filter_var($args['cat_'.$key], FILTER_VALIDATE_BOOLEAN)){
                        $args['keywords'][] = $key;
                    }
                }
                //-- end category

                $page_info = $this->work_model->get_list_count(array(
                    'delimiter' => $args['delimiter'],
                    'allow_enabled'=> $args['allow_enabled'],
                    'allow_disabled'=> $args['allow_disabled'],
                    'allow_deleted'=> $args['allow_deleted'],
                    'exclude_deleted'=> $args['exclude_deleted'],
                    'keywords' => $args['keywords'],
                    'order_by' => $args['order'],
                    'from' => $args['period'],
                    'q' => urldecode($args['q']),
                    'view_rank_point' => true,
                    'only_staffpoint_not_zero' => $args['sp_nz']
                ));

                $this->load->config('activity_point', TRUE);

                
                $works = $this->work_model->get_list(array(
                    'page' => $args['page'],
                    'delimiter' => $args['delimiter'],
                    'allow_enabled'=> $args['allow_enabled'],
                    'allow_disabled'=> $args['allow_disabled'],
                    'allow_deleted'=> $args['allow_deleted'],
                    'exclude_deleted'=> $args['exclude_deleted'],
                    'keywords' => $args['keywords'],
                    'order_by' => $args['order'],
                    'from' => $args['period'],
                    'q' => urldecode($args['q']),
                    'view_rank_point' => true,
                    'only_staffpoint_not_zero' => $args['sp_nz']
                ));

                $data['list'] = $works->rows;

                $data['all_count'] = isset($page_info->row->count)?$page_info->row->count:0;
                $data['all_page'] = isset($page_info->row->all_page)?$page_info->row->all_page:1;
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
                    $work = $this->work_model->get_info(array(
                        'work_id' => $args['id'],
                    ));
                    $data['field'] = $work->row;
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
        $this->layout->set_header('title', '작품 관리')->set_view('acp/work_works_'.$mode,$data)->render();
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
            case "staffpoint":
                $result =  $this->work_model->put_info(array(
                    'work_id' => $this->input->post('work_id'),
                    'staffpoint' => $this->input->post('staffpoint'),
                    ));

                exit(json_encode($result));

                break;
            case "delete":
                $this->work_model->delete_info(array(
                    'work_id' => $this->input->post('id'),
                    'force_delete' => ($this->input->post('force_delete')=='y'),
                    ));
                redirect('/acp/work/works/list/');
                
                break;
        }
    }
}

/* End of file dashboard.php */
/* Location: ./application/controllers/acp/dashboard.php */