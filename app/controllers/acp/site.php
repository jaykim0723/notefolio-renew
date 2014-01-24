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
}

/* End of file dashboard.php */
/* Location: ./application/controllers/acp/dashboard.php */