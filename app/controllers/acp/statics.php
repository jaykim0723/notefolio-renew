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
        redirect('/acp/statics/research/'); // temporary        
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
        
        $args = $this->uri->ruri_to_assoc(4);
        //var_export($args);
        
        $this->data['form_attr'] = array('class' => 'form', 'id' => 'research_'.$mode.'_form');
        $this->layout->set_header('title', '분석')->set_view('acp/statics_'.$mode.'_graphs_view',$this->data)->render();
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
        $this->load->model('acp_statics');

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
        $this->load->model('acp_statics');
        
        $args = $this->uri->ruri_to_assoc(4);
        //var_export($args);
        
        $this->data['form_attr'] = array('class' => 'form', 'id' => 'research_stat_form');
        $this->layout->set_header('title', '현황')->set_view('acp/statics_stat_graphs_view',$this->data)->render();

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
        $this->load->model('acp_statics');

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
        $this->load->model('acp_statics');

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
