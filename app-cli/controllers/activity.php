<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @brief Activity Write Controller
 *
 * @author Yoon, Seongsu(soplel@snooey.net)
 */

define('USER_ID', 0);
 
class activity extends CI_Controller {
    var $last_error = '';

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
        echo 'Posting...';

        parse_str($data, $data);
        $params = $this->make_param($crud, array(
        	'area' => $area,
        	'type'  => $type,
        	'data' => $data,
        	));
        echo '.';
        

        $ap_list = $this->config->item('ap',  'activity_point');
        $ap = (isset($ap_list[$area][$type]))?$ap_list[$area][$type]:0;
        switch($crud){
            case "delete":
                $ap = -$ap;
            break;
            default:
                $ap = $ap;
            break;
        }
        echo '.';

        //$this->db->trans_start();

        $result = $this->activity_model->post(array(
            'ref_id' => (isset($params['work']['work_id']))?$params['work']['work_id']:0,
            'user_id' => (isset($params['user_A']['id']))?$params['user_A']['id']:0,
            'area' => strtolower($area),
            'act' => strtolower($crud),
            'type' => strtolower($type),
            'point_get' => $ap,
            'point_status' => (isset($params['work']['nofol_rank']))?$params['work']['nofol_rank']:0,
            'data' => serialize((object)$params),
            'remote_addr' => 'console'
        ));
        echo '.';

        if($result->status=='done'){
            $this->after_post(array(
                'crud' => $crud,
                'area' => $area,
                'type'  => $type,
                'ref_id' => (isset($params['work']['work_id']))?$params['work']['work_id']:0,
                'user_A_id' => (isset($params['user_A']['id']))?$params['user_A']['id']:0,
                'user_B_id' => (isset($params['user_B']['id']))?$params['user_B']['id']:0,
                'parent_id' => (isset($data['parent_id']))?$data['parent_id']:0,
                'point_get' =>$ap,
                'activity_id' => $result->activity_id,
                ));
        }
        echo '.';

        //$this->db->trans_complete();

        echo $this->db->trans_status();

        //echo $this->last_error;

        echo 'done.'.PHP_EOL;
        return false;
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
                        'id'=>$user_B->row->id,
                        'username'=>$user_B->row->username,
                        'realname'=>$user_B->row->realname
                        );
                }
            break;
            case "work":
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

                if(in_array($params->type, array('collect', 'comment'))){
                    $data['comment'] = (!empty($params->data['comment']))?$params->data['comment']:'';
                }
            break;
            default:
                $this->last_error = @json_encode(array('status'=>'fail', 'message'=>'no_have_area'));
                return array();
            break;
        }

        return $data;
    }

    /**
     * make activity parameter for user. (update)
     * 
     * @param array $resource
     * 
     * @return array
     */
    
    function make_param_update($params=array())
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
                        'id'=>$user_B->row->id,
                        'username'=>$user_B->row->username,
                        'realname'=>$user_B->row->realname
                        );
                }
            break;
            case "work":
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

    /**
     * make activity parameter for user. (delete)
     * 
     * @param array $resource
     * 
     * @return array
     */
    
    function make_param_delete($params=array())
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
                        'id'=>$user_B->row->id,
                        'username'=>$user_B->row->username,
                        'realname'=>$user_B->row->realname
                        );
                }
            break;
            case "work":
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
            break;
            default:
                $this->last_error = @json_encode(array('status'=>'fail', 'message'=>'no_have_area'));
                return array();
            break;
        }

        return $data;
    }


    /**
     * make after activity for user.
     * 
     * @param array $resource
     * 
     * @return array
     */
    
    function after_post($params=array())
    {  
        $this->load->model('activity_model');

        $params = (object)$params;

        if(empty($params->activity_id)){
            $this->last_error = json_encode(array('code'=>'error', 'message'=>"where is 'activity id'?"));
            return false;
        }
        echo '!';

        if($params->crud == 'create'){
            switch($params->area){
                case "user":
                    if(in_array($params->type, array('follow'))){
                        //send to-> alarm
                        $this->activity_model->post_alarm(array(
                            'crud' => $params->crud,
                            'area' => $params->area,
                            'type' => $params->type,
                            'ref_id' => $params->ref_id,
                            'user_A_id' => $params->user_A_id,
                            'user_B_id' => $params->user_B_id,
                            'parent_id' => $params->parent_id,
                            'activity_id' => $params->activity_id,
                            ));
                    }
                break;
                case "work":
                    if(in_array($params->type, array('collect', 'comment', 'note'))){
                        //send to-> alarm
                        $this->activity_model->post_alarm(array(
                            'crud' => $params->crud,
                            'area' => $params->area,
                            'type' => $params->type,
                            'ref_id' => $params->ref_id,
                            'user_A_id' => $params->user_A_id,
                            'user_B_id' => $params->user_B_id,
                            'parent_id' => $params->parent_id,
                            'activity_id' => $params->activity_id,
                            ));
                    }
                    
                    if(in_array($params->type, array('collect', 'comment', 'note'))){
                        //send to-> feed
                        $this->activity_model->post_feed(array(
                            'crud' => $params->crud,
                            'area' => $params->area,
                            'type' => $params->type,
                            'ref_id' => $params->ref_id,
                            'user_A_id' => $params->user_A_id,
                            'user_B_id' => $params->user_B_id,
                            'parent_id' => $params->parent_id,
                            'activity_id' => $params->activity_id,
                            ));
                    }
                break;
                default:
                break;
            }
        }
        else if($params->crud == 'update'){
            switch($params->area){
                case "work":
                    if(in_array($params->type, array('enabled',))){
                        //send to-> alarm
                        $this->activity_model->post_alarm(array(
                            'crud' => $params->crud,
                            'area' => $params->area,
                            'type' => $params->type,
                            'ref_id' => $params->ref_id,
                            'user_A_id' => $params->user_A_id,
                            'user_B_id' => $params->user_B_id,
                            'parent_id' => $params->parent_id,
                            'activity_id' => $params->activity_id,
                            ));
                    }
                    
                    if(in_array($params->type, array('enabled',))){
                        //send to-> feed
                        $this->activity_model->post_feed(array(
                            'crud' => $params->crud,
                            'area' => $params->area,
                            'type' => $params->type,
                            'ref_id' => $params->ref_id,
                            'user_A_id' => $params->user_A_id,
                            'user_B_id' => $params->user_B_id,
                            'parent_id' => $params->parent_id,
                            'activity_id' => $params->activity_id,
                            ));
                        echo $this->db->last_query();
                    }
                break;
                default:
                break;
            }
        }
        echo '!';

        if(($params->area=="work")&&(!empty($params->point_get))){
            //add point
            $this->db->query("UPDATE works 
                set nofol_rank = nofol_rank + {$params->point_get} 
                where work_id = {$params->ref_id};
                ");
        }
        echo '!';

        return true;
    }
    
}

/* End of file activity.php */
/* Location: ./application/controllers/activity.php */