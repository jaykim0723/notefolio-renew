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
    public function post($crud, $area, $act, $data){
        //$this->load->model('activity_model');
        $params = $this->make_param($work, array(
        	'area' => $area,
        	'act'  => $act,
        	'type' => $type,
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
        //-- make work type
        $workType = strtolower($workType);
        $type_array = array('create','read','update','delete',);
        if (array_key_exists($workType, $type_array)) {
            $resource['workType'] = $workType;
            return $this->{'make_param_'.$workType}($resource);
        }
        else {
            $this->last_error = @json_encode(array('status'=>'fail', 'message'=>'no_have_work_type'));
            return array();
        }
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
        parse_str($params['data'], $opt);
        var_export($opt);

        $data = array();
        $user_A = $this->user_model->get_info(array('id'=>$opt['user_A']))->row;
        $data['user_A'] = array(
            'id'=>$user_A->id,
            'username'=>$user_A->username,
            'realname'=>$user_A->realname
            );

        switch($params['area']){
            case "user":
                if(in_array($params['type'], array('follow'))){
                    $user_B = $this->user_model->get_info(array('id'=>$opt['user_B']))->row;
                    $data['user_B'] = array(
                        'id'=>$user_B->id,
                        'username'=>$user_B->username,
                        'realname'=>$user_B->realname
                        );
                }
            break;
            case "work":
                if(in_array($params['type'], array('work'))){
                    $work = $this->work_model->get_info(array('work_id'=>$work_id))->row;
                    $opt['user_B'] = $work->user->id;
                    $data['work'] = array(
                        'work_id' => $work->work_id,
                        'title' => $work->title
                        );

                    $user_B = $this->user_model->get_info(array('id'=>$opt['user_B']))->row;
                    $data['user_B'] = array(
                        'id'=>$user_B->id,
                        'username'=>$user_B->username,
                        'realname'=>$user_B->realname
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