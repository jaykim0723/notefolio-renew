<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Layout
{
	protected	$ci;
	protected	$views = array(); // set_view 파라미터를 통해서 미리 셋팅이 되는 변수
	protected	$json_data = NULL;
	protected   $header = array(
		'title'       => 'Notefolio.net - 아티스트/디자이너의 크리에이티브 네트워크',
		'description' => '크리에이티브 네트워크 노트폴리오는 여기저기 흩어져 있는 아티스트와 디자이너들이 한 곳에 모여 자신의 작품을 공개하고 이야기하는 공간입니다.',
		'keywords'    => 'Notefolio, 노트폴리오, 아티스트, 디자이너, 크리에이티브, 네트워크, 갤러리, Gallery, Artist, Designer, Creative, Network',
		'type'        => '',
		'url'         => '',
		'image'       => '',
		'site_name'   => 'notefolio.net'
	);

	public function __construct()
	{
        $this->ci =& get_instance();
	}

	// normal template and pjax request
	function render($views=array()){
		if($this->json_data!==NULL){
			$this->ci->output->set_content_type('application/json')->set_output(json_encode($this->json_data));			
			return true;
		}

		if(sizeof($views)>0){
			$this->views[] = $views; // 미리 전달하지 않고 render할 때 개별 파라미터로 전달을 할 수도 있다.
		}

		$affix = '';
		$areaName = $this->ci->uri->segment(1);
		if(!$this->ci->input->is_ajax_request()){
			if(in_array($areaName, array('auth', 'acp')))
				$affix = $areaName.'_';
		}

		if(USER_ID>0){
			$this->ci->load->model('user_model');
			$user = $this->ci->user_model->get_info(array('id' => USER_ID, 'get_profile'=>true));
			if($user->status=='done'){
				$this->ci->nf->set('user', $user->row);
			}
			// 사용시
			// $this->nf->get('user')->username;
		}

		// print header
		if(!$this->ci->input->is_ajax_request()){
			(($areaName!='acp'))?$this->ci->load->view('layout/header_inc_view', $this->header):'';
			$this->ci->load->view('layout/header_'.$affix.'view', $this->header);
		}

		if(!is_array($this->views)) // 단일 view로 들어온 경우를 위하여
			$this->views = array($this->views);
		foreach($this->views as $view){
			$this->ci->load->view($view[0], $view[1]);
		}

		// print footer
		if(!$this->ci->input->is_ajax_request()){
			$this->ci->load->view('layout/footer_'.$affix.'view');
			(($areaName!='acp'))?$this->ci->load->view('layout/footer_inc_view', array('row'=>$this->ci->nf->get('user'))):'';
		}
	}


	/**
	 * 헤더에 관한 변수를 설정한다.
	 * @param string $data  [description]
	 * @param string $value [description]
	 */
	function set_header($data='', $value=''){
		if(!empty($value)){
			$data = array(
				$data => $value
			);
		}
		foreach($data as $key => $value)
			$this->header[$key] = $value;
		return $this;
	}

	function set_view($filename='', $data=array()){
		$this->views[] = array($filename, $data);
		return $this;
	}


	function set_json($data=array()){
		$this->json_data = $data;
		return $this;
	}

	
	

}

/* End of file Layout.php */
/* Location: .//Applications/XAMPP/xamppfiles/htdocs/notefolio_renew/app/libraries/Layout.php */
