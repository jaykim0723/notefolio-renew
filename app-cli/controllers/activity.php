<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @brief Activity Write Controller
 *
 * @author Yoon, Seongsu(soplel@snooey.net)
 */

define(USER_ID, 0);
 
class activity extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 */
	public function index()
	{
		echo "Hello {$to}!".PHP_EOL;
	}

	/**
	 * post to activity
	 *
     * @param string $data (querystring)
	 */
    public function post($crud, $area, $type, $data){
        $this->load->model('activity_model');
        $this->load->config('activity_point', TRUE);

        parse_str($data, $data);
        $params = $this->make_param($crud, array(
        	'area' => $area,
        	'type'  => $type,
        	'data' => $data,
        	));
        

        $ap_list = $this->config->item('ap',  'activity_point');
        $ap = $ap_list[$area][$type];

        var_export($ap);
        exit();
        $this->activity_model->post(array(
            'ref_id' => (isset($params['work']['work_id']))?$params['work']['work_id']:0,
            'user_id' => (isset($params['user_A']['id']))?$params['user_A']['id']:0,
            'area' => strtolower($area),
            'act' => strtolower($crud),
            'type' => strtolower($type),
            'point_get' => 0,
            'point_status' => 0,
            'data' => '',
            'remote_addr' => 'console'
        ));

        //echo $this->activity->last_response;
        return true;
    }


    /**
     * make activity parameter for user.
     * 
     * @param string $workType
     * @param array $resource
     * 
     * @return array
     */
    
    function make_param($workType, $resource=array())
    {   
        $this->load->model('user_model');

        //-- go by work type
        $workType = strtolower($workType);
        try{
            $resource['workType'] = $workType;
            $output =  $this->{'make_param_'.$workType}($resource);
        }
        catch(Exception $e){
            $this->last_error = @json_encode(array('status'=>'fail', 'message'=>'no_have_work_type'));
            return array();
        }

        $data = array();
        $params = (object)$resource;

        $user_A = $this->user_model->get_info(array('id'=>$params->data['user_A']));
        if(!isset($user_A->row)){
            $user_A->row = (object)array(
                'id' => $params->data['user_A'],
                'username' => '',
                'realname' => '',
                );
        }
        $data['user_A'] = array(
            'id'=>$user_A->row->id,
            'username'=>$user_A->row->username,
            'realname'=>$user_A->row->realname
            );


        $output = array_merge($data, $output);

        return $output;
    }

    /**
     * make activity parameter for user. (create)
     * 
     * @param array $resource
     * 
     * @return array
     */
    
    function make_param_create($params=array())
    {
        $this->load->model('user_model');
        $this->load->model('work_model');

        $params = (object)$params;

        $data = array();
        
        switch($params->area){
            case "user":
                if(in_array($params->type, array('follow'))){
                    $user_B = $this->user_model->get_info(array('id'=>$params->data['user_B']));
                    if(!isset($user_B->row)){
                        $user_B->row = (object)array(
                            'id' => $params->data['user_B'],
                            'username' => '',
                            'realname' => '',
                            );
                    }
                    $data['user_B'] = array(
                        'id'=>$user_B->id,
                        'username'=>$user_B->row->username,
                        'realname'=>$user_B->row->realname
                        );
                }
            break;
            case "work":
                if(in_array($params->type, array('work', 'collect', 'comment', 'note'))){
                    $work = $this->work_model->get_info(array('work_id'=>$params->data['work_id']));
                    if(!isset($work->row)){
                        $work->row = (object)array(
                            'work_id' => $params->data['work_id'],
                            'title' => '',
                            'nofol_rank' => 0
                            );
                        $params->data['user_B'] = 0;
                    }
                    $data['work'] = array(
                        'work_id' => $work->row->work_id,
                        'title' => $work->row->title,
                        'nofol_rank' => $work->row->nofol_rank
                        );
                    $params->data['user_B'] = $work->row->user->id;

                    $user_B = $this->user_model->get_info(array('id'=>$params->data['user_B']));
                    if(!isset($user_B->row)){
                        $user_B->row = (object)array(
                            'id' => $params->data['user_B'],
                            'username' => '',
                            'realname' => '',
                            );
                    }
                    $data['user_B'] = array(
                        'id'=>$user_B->row->id,
                        'username'=>$user_B->row->username,
                        'realname'=>$user_B->row->realname
                        );
                }
                if(in_array($params->type, array('collect', 'comment'))){
                    $data['comment'] = $params->data['comment'];
                }
            break;
            default:
                $this->last_error = @json_encode(array('status'=>'fail', 'message'=>'no_have_area'));
                return array();
            break;
        }

        return $data;
    }
    
}

/* End of file activity.php */
/* Location: ./application/controllers/activity.php */