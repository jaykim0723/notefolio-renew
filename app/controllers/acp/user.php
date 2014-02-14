<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
		$this->nf->admin_check();
		$this->load->model('user_model');
    }
	
    /**
     * index
	 *
	 */
	public function index()
	{
		redirect('/acp/user/member');
	}
	
    /**
     * get member list
	 *
	 */
	public function member($mode='list')
	{
		if($this->uri->segment(4)==FALSE) redirect('/acp/user/member/list');
		$args = $this->uri->uri_to_assoc(4);

		switch($mode){
			case "list":
				$data = $this->user_model->get_list($args);
			break;
			case "write":
				$data = new stdClass();
			case "modify":
			case "view":
			case "del":
				$args['get_profile'] = true;
				$args['get_sns_fb'] = true;
				$data = $this->user_model->get_info($args);
                var_export($args);
                exit();
			break;
			default:
				exit('error');
			break;
		}

		$this->layout->set_header('title', '회원')->set_view('acp/user_member_'.$mode.'_view',$data)->render();
	}
    
    /*
     * @brief return restrict page
     * 
     * @param string $method
     * 
     * @return null
     */
    function restrict($method=null)
    {
        $data['form_attr'] = array('class' => 'form-code', 'id' => 'restrict_form');
        
        //-- get from /app/config/user_restrict.php
        $this->config->load('user_restrict', TRUE, TRUE);
        
        //-- set as default data
        $default_restrict_username  =$this->config->item('restrict_username', 'user_restrict');
        $default_restrict_email     =$this->config->item('restrict_email',    'user_restrict');
        
        //-- save
        if ($method=='save') {
            //-- set as default data
            $default_restrict_username  =($this->input->post('restrict_username')!=false)?$this->input->post('restrict_username'):$defalut_restrict_username;
            $default_restrict_email     =($this->input->post('restrict_email')   !=false)?$this->input->post('restrict_email')   :$defalut_restrict_email;
            
            //-- call config manage library
            $this->load->library('manage_config');
            
            $data['save_result'] = var_export( $this->manage_config->write("user_restrict", 
                array(  'restrict_username'=>$default_restrict_username,
                        'restrict_email'   =>$default_restrict_email
                    )
            ), true );
        }
        
        $data['default_restrict_username'] = $default_restrict_username;
        $data['default_restrict_email']    = $default_restrict_email;
		
		$this->layout->set_header('title', '회원 가입제한규칙')->set_view('acp/user_member_'.$mode.'_view',$data)->render();
        $this->layout->view('acp/user_restrict', $data);
    }
}

/* End of file dashboard.php */
/* Location: ./application/controllers/acp/dashboard.php */