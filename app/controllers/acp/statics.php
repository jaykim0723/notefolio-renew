<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * @brief statics controller for admin control panel
 * @author Yoon, Seongsu(sople1@snooey.net)
 * 
 */

class Statics extends CI_Controller
{
    private $title;
    private $data;
    
    function __construct()
    {

        parent::__construct();
        $this->nf->admin_check();

        $this->load->helper('form');
    }

    /*
     * @brief return main page
     * 
     * @param null
     * 
     * @return null
     */
    function index()
    {
        if ($message = $this->session->flashdata('message')) {
            $this->load->view('auth/general_message', array('message' => $message));
        } else if ($this->acp->is_elevated()>0) {     // logged in, elevated
            redirect('/acp/statics/research/'); // temporary
        } else {
            redirect('/acp/auth/login/?go_to=/'.$this->uri->uri_string());
        }
        
    }
    
    /*
     * @brief return research page
     * 
     * @param string $mode
     * 
     * @return null
     */
    function research($mode='user')
    {
        $this->data['acp_submenu_html'] =  $this->acp->get_submenu(strtolower($this->title), strtolower(__FUNCTION__));
        $this->layout->title(__FUNCTION__." - ".$this->title);
        $this->layout->coffee($this->layout_resource_path."coffee/research.coffee");
        
        $args = $this->uri->ruri_to_assoc(4);
        //var_export($args);
        
        $viewLayout = 'error/http_404';

        switch($mode) {
            case "user":
        		$viewLayout = 'acp/statics_user_graphs_view';
            	break;
            case "work":
        		$viewLayout = 'acp/statics_work_graphs_view';
            	break;
            default:
                break;
        }
        
        $this->data['subtab'] = $this->acp->get_subtab(array("user"=>"회원", "work"=>"작품"), 
                                                        $mode, strtolower(get_class($this)).'/'.strtolower(__FUNCTION__).'/');
        $this->data['form_attr'] = array('class' => 'form', 'id' => 'research_'.$mode.'_form');
        $this->layout->js('https://www.google.com/jsapi');
      	$this->layout->js($this->layout_resource_path.'js/chart.js');
      	$this->layout->js($this->layout_resource_path.'js/chart_'.$mode.'.js');
      	$this->layout->view($viewLayout, $this->data);
    }
    
    /*
     * @brief return research data json
     * 
     * @param string $mode
     * 
     * @return null
     */
    function research_data($mode='user')
    {
        $this->load->model('acp/acp_statics');

        $args = $this->uri->ruri_to_assoc(4);
        //var_export($args);
        foreach(array('date_from','date_to') as $v){
        	if($this->input->get_post($v)){
        		$args[$v] = $this->input->get_post($v);
        	}
        }

        //-- 거꾸로 선택해도 값은 올바르게 정렬하여 출력해야 한다.
        if(strtotime($args['date_to'])-strtotime($args['date_from'])<0){
        	$temp = $args['date_to'];
        	$args['date_to'] = $args['date_from'];
        	$args['date_from'] = $temp;
        	unset($temp);
        }

        $output = array('date'=>array("from"=>$args['date_from'], "to"=>$args['date_to']));

        switch($mode) {
            case "user":
				$output['userJoin'] = $this->acp_statics->get_user_join($args['date_from'],$args['date_to']);
				$output['userJoinWithFacebook'] = $this->acp_statics->get_user_join_with_facebook($args['date_from'],$args['date_to']);
				$output['userJustUploadAtJoin'] = $this->acp_statics->get_user_just_upload_at_join($args['date_from'],$args['date_to']);
				$output['uploadTermGraph'] = $this->acp_statics->get_user_upload_term($args['date_from'],$args['date_to']);
				$output['joinGender'] = $this->acp_statics->get_user_join_gender($args['date_from'],$args['date_to']);
				$output['percentageAge'] = $this->acp_statics->get_user_percentage_age($args['date_from'],$args['date_to']);
				$output['percentageGenderAge'] = $this->acp_statics->get_user_percentage_gender_age($args['date_from'],$args['date_to']);
        $output['userActive'] = $this->acp_statics->get_user_active($args['date_from'],$args['date_to']);
        $output['userLastLogin'] = $this->acp_statics->get_user_last_login($args['date_from'],$args['date_to']);
            	break;
            case "work":
				$output['workViewCount'] = $this->acp_statics->get_work_view_count($args['date_from'],$args['date_to']);
				$output['workNoteCount'] = $this->acp_statics->get_work_note_count($args['date_from'],$args['date_to']);
				$output['workCommentCount'] = $this->acp_statics->get_work_comment_count($args['date_from'],$args['date_to']);
				$output['workUploadUserWork'] = $this->acp_statics->get_work_upload_user_work($args['date_from'],$args['date_to']);
            	break;
            default:
                break;
        }

        exit(json_encode($output));
    }
    
    /*
     * @brief return stat page
     * 
     * @param string $mode
     * 
     * @return null
     */
    function stat($mode='user')
    {
        $this->load->model('acp/acp_statics');

        $this->data['acp_submenu_html'] =  $this->acp->get_submenu(strtolower($this->title), strtolower(__FUNCTION__));
        $this->layout->title(__FUNCTION__." - ".$this->title);
        $this->layout->coffee($this->layout_resource_path."coffee/stat.coffee");
        
        $args = $this->uri->ruri_to_assoc(4);
        //var_export($args);
        
        $this->data['form_attr'] = array('class' => 'form', 'id' => 'research_stat_form');
        $this->layout->js('https://www.google.com/jsapi');
      	$this->layout->js($this->layout_resource_path.'js/chart.js');
      	$this->layout->js($this->layout_resource_path.'js/chart_stat.js');
      	$this->layout->view('acp/statics_stat', $this->data);
    }
    
    /*
     * @brief return stat data json
     * 
     * @param string $mode
     * 
     * @return null
     */
    function stat_data($mode=null)
    {
        $this->load->model('acp/acp_statics');

        $args = $this->uri->ruri_to_assoc(4);
        //var_export($args);

        if($mode=="keyword"){
          $output = array();

          $output['workKeywordUsage'] = $this->acp_statics->get_work_keyword_usage(10);
          $output['userKeywordUsage'] = $this->acp_statics->get_user_keyword_usage(10);
        
        } else if($mode=="stat"){
          $result = $this->acp_statics->get_work_first_upload();
          $output['firstUpload']    = $result['data'];
          $output['firstUploadAvg'] = $result['avg'];
          $result = $this->acp_statics->get_work_second_upload();
          $output['secondUpload']    = $result['data'];
          $output['secondUploadAvg'] = $result['avg'];
          $output['workPerUser'] = $this->acp_statics->get_work_per_user();
          $output['totalGenderAge'] = $this->acp_statics->get_user_gender_age_total();
          

        } else if($mode=="overview" OR $mode=="work"){
          foreach(array('date_from','date_to') as $v){
            if($this->input->get_post($v)){
              $args[$v] = $this->input->get_post($v);
            }
          }
          $args['date_from'] = new DateTime($args['date_from'].'-10');
          $args['date_from'] = $args['date_from']->modify('first day of this month')->format('Y-m-d');
          $args['date_to']   = new DateTime($args['date_to'  ].'-10');
          $args['date_to']   = $args['date_to']->modify('last day of this month')->format('Y-m-d');

          $output = array('date'=>array("from"=>$args['date_from'], "to"=>$args['date_to']));

          //-- 거꾸로 선택해도 값은 올바르게 정렬하여 출력해야 한다.
          if(strtotime($args['date_to'])-strtotime($args['date_from'])<0){
            $temp = $args['date_to'];
            $args['date_to'] = $args['date_from'];
            $args['date_from'] = $temp;
            unset($temp);
          }
          
          if($mode=="overview"){
            $total_data = $this->acp_statics->get_total_data($args['date_from'],$args['date_to']);
            $output['totalUser'] = $total_data['user'];
            $output['totalUserUploaded'] = $total_data['userUploaded'];
            $output['totalWorks'] = $total_data['works'];

          } else if($mode=="work"){
            $total_data = $this->acp_statics->get_total_work_data($args['date_from'],$args['date_to']);
            $output['totalViewCount'] = $total_data['viewCount'];
            $output['totalNoteCount'] = $total_data['noteCount'];
            $output['totalCommentCount'] = $total_data['commentCount'];

            $output['workViewMonthCount'] = $this->acp_statics->get_work_view_month_count($args['date_from'],$args['date_to']);
            $output['workNoteMonthCount'] = $this->acp_statics->get_work_note_month_count($args['date_from'],$args['date_to']);
            $output['workCommentMonthCount'] = $this->acp_statics->get_work_comment_month_count($args['date_from'],$args['date_to']);
            

          } 

        }

        exit(json_encode($output));
    }
    
    /*
     * @brief return rank data json
     * 
     * @param string $mode
     * 
     * @return null
     */
    function rank_data($mode=null)
    {
        $this->load->model('acp/acp_statics');

        $args = $this->uri->ruri_to_assoc(4);
        //var_export($args);
        if($mode==null)
          exit(json_encode(array('data'=>null)));

        $func_name = "get_".str_replace('-','_', $mode)."_rank";
        if(in_array($mode, array("upload_user", "view_user"))){
          $output = $this->acp_statics->$func_name($this->input->get_post('count'));
        }
        else {
          $output = $this->acp_statics->$func_name();
        }
        
        exit(json_encode($output));
    }
}
