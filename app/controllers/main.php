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
		$data = array(
			(object)array(
				't' => 'image',
				'c' => '//notefolio.net/img/1310/25318_r',
				'i' => '2349'
			),
			(object)array(
				't' => 'image',
				'c' => '//notefolio.net/img/1310/25317_r',
				'i' => '2350'
			),
			(object)array(
				't' => 'text',
				'c' => '국민의 모든 자유와 권리는 국가안전보장, 질서유지 또는 공공복리를 위하여 필요한 경우에 한하여 법률로써 제한할 수 있으며, 제한하는 경우에도 자유와 권리의 본질적인 내용을 침해할 수 없다 모든 국민은 신체의 자유를 가진다. 다만, 현행범인인 경우와 장기 3년 이상의 형에 해당하는 죄를 범하고 도피 또는 증거인멸의 염려가 있을 때에는 사후에 영장을 청구할 수 있다. 모든 국민은 주거의 자유를 침해받지 아니한다. 공공필요에 의한 재산권의 수용, 사용 또는 제한 및 그에 대한 보상은 법률로써 하되, 정당한 보상을 지급하여야 한다.',
				'i' => ''
			),
			(object)array(
				't' => 'video',
				'c' => '//www.youtube.com/embed/cSESe4mkLTs',
				'i' => ''
			),
			(object)array(
				't' => 'text',
				'c' => '주거에 대한 압수나 수색을 할 때에는 검사의 신청에 의하여 법관이 발부한 영장을 제시하여야 한다. 공무원의 신분과 정치적 중립성은 법률이 정하는 바에 의하여 보장된다.',
				'i' => ''
			),
			(object)array(
				't' => 'line',
				'c' => '',
				'i' => ''
			),
			(object)array(
				't' => 'text',
				'c' => '공개하지 아니한 회의내용의 공표에 관하여는 법률이 정하는 바에 의한다. 피고인의 자백이 고문·폭행·협박·구속의 부당한 장기화 또는 기망 기타의 방법에 의하여 자의로 진술된 것이 아니라고 인정될 때 또는 정식재판에 있어서 피고인의 자백이 그에게 불리한 유일한 증거일 때에는 이를 유죄의 증거로 삼거나 이를 이유로 처벌할 수 없다. 신체장애자 및 질병·노령 기타의 사유로 생활능력이 없는 국민은 법률이 정하는 바에 의하여 국가의 보호를 받는다. 국가는 개인이 가지는 불가침의 기본적 인권을 확인하고 이를 보장할 의무를 진다.',
				'i' => ''
			),
			(object)array(
				't' => 'text',
				'c' => '헌법에 의하여 체결·공포된 조약과 일반적으로 승인된 국제법규는 국내법과 같은 효력을 가진다. 모든 국민은 법률이 정하는 바에 의하여 국방의 의무를 진다. 모든 국민은 고문을 받지 아니하며, 형사상 자기에게 불리한 진술을 강요당하지 아니한다. 신체장애자 및 질병·노령 기타의 사유로 생활능력이 없는 국민은 법률이 정하는 바에 의하여 국가의 보호를 받는다. 군인·군무원·경찰공무원 기타 법률이 정하는 자가 전투·훈련 등 직무집행과 관련하여 받은 손해에 대하여는 법률이 정하는 보상 외에 국가 또는 공공단체에 공무원의 직무상 불법행위로 인한 배상은 청구할 수 없다. 국회는 의장 1인과 부의장 2인을 선출한다. 혼인과 가족생활은 개인의 존엄과 양성의 평등을 기초로 성립되고 유지되어야 하며, 국가는 이를 보장한다. 누구든지 체포 또는 구속을 당한 때에는 적부의 심사를 법원에 청구할 권리를 가진다. 훈장 등의 영전은 이를 받은 자에게만 효력이 있고, 어떠한 특권도 이에 따르지 아니한다. 모든 국민은 그 보호하는 자녀에게 적어도 초등교육과 법률이 정하는 교육을 받게 할 의무를 진다. 혼인과 가족생활은 개인의 존엄과 양성의 평등을 기초로 성립되고 유지되어야 하며, 국가는 이를 보장한다. 국회의원은 국회에서 직무상 행한 발언과 표결에 관하여 국회 외에서 책임을 지지 아니한다. 공개하지 아니한 회의내용의 공표에 관하여는 법률이 정하는 바에 의한다.',
				'i' => ''
			)
		);
		// exit(serialize($data));

		$work_list = $this->work_model->get_list(array(
			'page'      => $page,
			'delimiter' => $page==1 ? 17 : 16 // 처음일 때에는 하나를 따로 뺀다
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