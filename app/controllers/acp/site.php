<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class site extends CI_Controller {

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
		redirect('/acp/site/keywords');
	}
    
    /**
     * get keyword list
     *
     */
    public function keywords($method=null)
    {
        
        $data = array( 
            'form_attr' => array('class' => 'form-code', 'id' => 'keywords_form')
            );
        
        //-- get from /app/config/keyword.php
        $this->config->load('keyword', TRUE, TRUE);
        
        //-- set as default data
        $default_keyword     = $this->config->item('keyword',    'keyword');
        
        //-- save
        if ($method=='save') {
            //-- set as default data
            $default_keyword  =($this->input->post('keyword')!=false)?json_decode($this->input->post('keyword'),true):$default_keyword;
            
            //-- call config manage library
            $this->load->library('manage_config');
            
            $data['save_result'] = var_export( $this->manage_config->write("keyword", 
                array(  'keyword'=>$default_keyword,
                    )
            ), true );
        }
        
        $data['keyword_list'] = $default_keyword;
        $data['default_keyword'] = json_encode(($default_keyword!=false)?$default_keyword:array());

        $this->layout->set_header('title', '키워드')->set_view('acp/site_keywords_view',$data)->render();
    }
    
    
    /*
     * @brief return access log page
     * 
     * @param string $mode
     * 
     * @return null
     */
    function access_log($mode='list')
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
                if(isset($args['search']['only_outside'])){
                    unset($args['search']['only_outside']);
                    $only_outside = true;
                    $args['search']['referrer is not null and referrer not like "%notefolio.net%"'] = null;
                }
                if(isset($args['search']['to_access'])){
                    $args['search']['to_access like'] = $args['search']['to_access'];
                    unset($args['search']['to_access']);
                }
                //var_export($args['order']);
                
                $page_info = $this->db
                    ->select('count(*) as count, ceil(count(*)/'.$args['delimiter'].') as all_page')
                    ->get('log_access')->result_array();
                
                $this->data['list'] = $this->log_db->_get_list('access', $args['search'], array(), array($args['page'], $args['delimiter']), $args['order']);
                //var_export($this->db->last_query());

                $this->data['all_count'] = isset($page_info[0])?$page_info[0]['count']:0;
                $this->data['all_page'] = isset($page_info[0])?$page_info[0]['all_page']:1;
                $this->data['now_page'] = isset($args['page'])?$args['page']:1;
                $this->data['delimiter'] = isset($args['page'])?$args['delimiter']:30;
                $this->data['paging'] = $this->acp->get_paging($args['page'], $page_info[0]['all_page'], 'site/access_log/list'.(($only_outside)?'/search/only_outside':''));
                break;
            case "view":
                if (isset($args['id'])) {
                    $this->data['view'] = $this->log_db->_get_list('access', array('id'=>$args['id']), array(), array(1,1));
                } else {
                   redirect('/acp/site/access_log/list/');
                }
                break;
            case "write":
                $this->data['field'] = array();
                
                $this->data['field']['mode'] = 'write';
                $this->data['field']['prefix']='TT';
                $this->data['field']['length']='8';
                $this->data['field']['amount']='1';
                $this->data['field']['comment']='';
                
                break;
            case "modify":
                if (isset($args['id'])) {
                    $form_data = $this->log_db->_get_list('access', array('id'=>$args['id']), array(), array(1,1));
                    $this->data['field'] = $form_data[0];
                } else {
                   redirect('/acp/site/access_log/list/');
                }
                break;
            case "delete":
                if (isset($args['id'])) {
                    $form_data = $this->log_db->_get_list('access', array('id'=>$args['id']), array(), array(1,1));
                    $this->data['field'] = $form_data[0];
                } else {
                   redirect('/acp/site/access_log/list/');
                }
                break;
            //case "brief":
            //    return $this->_access_log_brief($this->input->post('mode'));
            //    break;
            case "proc":
                return $this->_access_log_proc($this->input->post('mode'));
                break;
            default:
                break;
        }

        $this->data['subtab'] = $this->acp->get_subtab(array("list"=>"목록",
                                                             "list/search/only_outside"=>"목록(외부접속)",
                                                             "view"=>"보기", 
                                                             "write"=>"쓰기", 
                                                             "modify"=>"수정"), 
                                                        $mode.(($only_outside)?'/search/only_outside':''), strtolower(get_class($this)).'/'.strtolower(__FUNCTION__).'/');
        
        $this->data['form_attr'] = array('class' => 'form', 'id' => 'access_log_'.$mode.'_form');
        $this->layout->set_header('title', '키워드')->set_view('acp/site_access_log_'.$mode,$data)->render();
    }

    
    /*
     * @brief process for access log
     * 
     * @param string $mode
     * 
     * @return null
     */
    function _access_log_proc($mode)
    {
        switch($mode) {
            case "write":
                $prefix = $this->input->post('prefix');
                $length = $this->input->post('length');
                $amount = $this->input->post('amount');
                $comment = $this->input->post('comment');
                
                if (!$prefix) {
                    alert('접두어가 필요합니다.');
                    redirect('/acp/site/access_log/write/');                    
                } else if (!$length>0) {
                    alert('길이가 지나치게 작습니다.');
                    redirect('/acp/site/access_log/write/');                    
                } else if (!$amount>1) {
                    alert('수량은 적어도 1개 이상입니다.');
                    redirect('/acp/site/access_log/write/');                    
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
                
                redirect('/acp/site/access_log/list/');
                
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
                redirect('/acp/site/access_log/view/id/'.$this->input->post('id'));
                
                break;
            case "delete":
                
                $this->invite_code_db->_delete($this->input->post('id'));
                redirect('/acp/site/access_log/list/');
                
                break;
        }
    }
}

/* End of file dashboard.php */
/* Location: ./application/controllers/acp/dashboard.php */