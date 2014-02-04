<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @brief Activity Write Controller
 *
 * @author Yoon, Seongsu(soplel@snooey.net)
 */
 
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
        parse_str($data, $data);
        $params = $this->make_param($crud, array(
        	'area' => $area,
        	'type'  => $type,
        	'data' => $data,
        	));
        var_export($params);
        //$this->activity_model->post($params);

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
                if(in_array($params->type, array('work'))){
                    $work = $this->work_model->get_info(array('work_id'=>$params->data['work_id']));
                    if(!isset($work->row)){
                        $work->row = (object)array(
                            'work_id' => $params->data['work_id'],
                            'title' => ''
                            );
                        $params->data['user_B'] = 0;
                    }
                    $data['work'] = array(
                        'work_id' => $work->row->work_id,
                        'title' => $work->row->title
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