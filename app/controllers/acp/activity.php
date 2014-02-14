<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * @brief Activity controller for admin control panel
 * @author Yoon, Seongsu(sople1@snooey.net)
 * 
 */

class Activity extends NF_ACP_Controller
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
            redirect('/acp/activity/act_log/'); // temporary
        } else {
            redirect('/acp/auth/login/?go_to=/'.$this->uri->uri_string());
        }
        
    }
    
    /*
     * @brief return activity log page
     * 
     * @param string $mode
     * 
     * @return null
     */
    function act_log($mode='list')
    {
        $this->data['acp_submenu_html'] =  $this->acp->get_submenu(strtolower($this->title), strtolower(__FUNCTION__));
        $this->layout->title(__FUNCTION__." - ".$this->title);
        $this->layout->coffee($this->layout_resource_path."coffee/act_log.coffee");
        
        $args = $this->uri->ruri_to_assoc(4);
        //var_export($args);
                
        switch($mode) {
            case "list":
                if(!isset($args['page'])) $args['page'] = 1;
                if(!isset($args['delimiter'])) $args['delimiter'] = 30;
                if(!isset($args['order'])) $args['order'] ="id=desc";
                parse_str(str_replace(":","=",str_replace("|", "&", $args['order'])), $args['order'] );
                //var_export($args['order']);
                
                $page_info = $this->log_db->_get_list('activity', array(), array('count(*) as count', 'ceil(count(*)/'.$args['delimiter'].') as all_page'));
                
                $this->data['list'] = $this->log_db->_get_list('activity', 
                    array('user_join'=>
                        array('table'=>'(select id as user_id, username, email from users) u', 'on'=>'log_activity.user_id = u.user_id', 'type'=>'left') ),
                    array(), array($args['page'], $args['delimiter']), $args['order']);
                
                $this->data['all_count'] = isset($page_info[0])?$page_info[0]['count']:0;
                $this->data['all_page'] = isset($page_info[0])?$page_info[0]['all_page']:1;
                $this->data['now_page'] = isset($args['page'])?$args['page']:1;
                $this->data['delimiter'] = isset($args['page'])?$args['delimiter']:30;
                $this->data['paging'] = $this->acp->get_paging($args['page'], $page_info[0]['all_page'], 'activity/act_log/list');
                break;
            case "view":
                if (isset($args['id'])) {
                    $this->data['view'] = $this->log_db->_get_list('activity', array('id'=>$args['id'],'user_join'=>
                            array('table'=>'(select id as user_id, username, email from users) u', 'on'=>'log_activity.user_id = u.user_id', 'type'=>'left')), array(), array(1,1));
                } else {
                   redirect('/acp/activity/act_log/list/');
                }
                break;
            case "write":
                $this->data['field'] = array();
                
                $this->data['field']['mode'] = 'write';
                $this->data['field']['prefix']='TT';
                $this->data['field']['length']='8';
                $this->data['field']['amount']='1';
                $this->data['field']['comment']='';
                
                break;
            case "modify":
                if (isset($args['id'])) {
                    $form_data = $this->log_db->_get_list('activity', array('id'=>$args['id']), array(), array(1,1));
                    $this->data['field'] = $form_data[0];
                } else {
                   redirect('/acp/activity/act_log/list/');
                }
                break;
            case "delete":
                if (isset($args['id'])) {
                    $form_data = $this->log_db->_get_list('activity', array('id'=>$args['id']), array(), array(1,1));
                    $this->data['field'] = $form_data[0];
                } else {
                   redirect('/acp/activity/act_log/list/');
                }
                break;
            case "proc":
                return $this->_act_log_proc($this->input->post('mode'));
                break;
            default:
                break;
        }
        
        $this->data['subtab'] = $this->acp->get_subtab(array("list"=>"목록", "view"=>"보기", "write"=>"쓰기", "modify"=>"수정"), 
            $mode, strtolower(get_class($this)).'/'.strtolower(__FUNCTION__).'/');
        
        $this->data['form_attr'] = array('class' => 'form', 'id' => 'act_log_'.$mode.'_form');
        $this->layout->view('acp/activity_act_log_'.$mode, $this->data);
    }
    
    /*
     * @brief process for activity log
     * 
     * @param string $mode
     * 
     * @return null
     */
    function _act_log_proc($mode)
    {
        switch($mode) {
            case "write":
                $prefix = $this->input->post('prefix');
                $length = $this->input->post('length');
                $amount = $this->input->post('amount');
                $comment = $this->input->post('comment');
                
                if (!$prefix) {
                    alert('접두어가 필요합니다.');
                    redirect('/acp/activity/act_log/write/');                    
                } else if (!$length>0) {
                    alert('길이가 지나치게 작습니다.');
                    redirect('/acp/activity/act_log/write/');                    
                } else if (!$amount>1) {
                    alert('수량은 적어도 1개 이상입니다.');
                    redirect('/acp/activity/act_log/write/');                    
                }
                
        
                $this->load->library('codegen');
                
                for($i=0;$i<$amount;$i++) {
                    $code = $this->codegen->get_code($length, 0, 'time');
                    if($this->invite_code_db->_get_list(array('code'=>$prefix.$code), array(), array(1,1))==array()){
                        $this->invite_code_db->_insert(array('code'=>$prefix.$code, 'comment'=>$comment));
                    } else {
                        //log_message('debug', var_export(($this->invite_code_db->_get_list(array('code'=>$prefix.$code), '', array(1,1))!=array()), true));
                        $i--;
                    }
                }
                
                redirect('/acp/activity/act_log/list/');
                
                break;
            case "modify":
                
                $param = array();
                if($this->input->post('code')) $param['code']=$this->input->post('code');
                if($this->input->post('regdate')) $param['regdate']=$this->input->post('regdate');
                if($this->input->post('moddate')) $param['moddate']=$this->input->post('moddate');
                if($this->input->post('user_id')) $param['user_id']=$this->input->post('user_id');
                if($this->input->post('generate_user_id')) $param['generate_user_id']=$this->input->post('generate_user_id');
                if($this->input->post('comment')) $param['comment']=$this->input->post('comment');
                
                //var_export($this->input->post());

                $this->invite_code_db->_update($this->input->post('id'),$param);
                redirect('/acp/activity/act_log/view/id/'.$this->input->post('id'));
                
                break;
            case "delete":
                
                $this->invite_code_db->_delete($this->input->post('id'));
                redirect('/acp/activity/act_log/list/');
                
                break;
        }
    }
    
    /*
     * @brief return activity log page
     * 
     * @param string $mode
     * 
     * @return null
     */
    function alarms($mode='list')
    {
        $this->data['acp_submenu_html'] =  $this->acp->get_submenu(strtolower($this->title), strtolower(__FUNCTION__));
        $this->layout->title(__FUNCTION__." - ".$this->title);
        $this->layout->coffee($this->layout_resource_path."coffee/alarms.coffee");
        
        $args = $this->uri->ruri_to_assoc(4);
        //var_export($args);
                
        switch($mode) {
            case "list":
                if(!isset($args['page'])) $args['page'] = 1;
                if(!isset($args['delimiter'])) $args['delimiter'] = 30;
                if(!isset($args['order'])) $args['order'] ="id=desc";
                parse_str(str_replace(":","=",str_replace("|", "&", $args['order'])), $args['order'] );
                //var_export($args['order']);
                
                $page_info = $this->log_db->_get_list('activity', array(), array('count(*) as count', 'ceil(count(*)/'.$args['delimiter'].') as all_page'));
                
                $this->data['list'] = $this->user_db->_get_user_alarm_list(
                    array('user_join'=>
                        array('table'=>'(select id as user_id, username, email from users) u', 'on'=>'user_alarms.user_id = u.user_id', 'type'=>'left'),
                        'log_join'=>
                        array('table'=>'(select id as log_id, user_id as log_user_id, area, type, data from log_activity) log', 'on'=>'user_alarms.ref_id = log.log_id', 'type'=>'left'),
                        'log_user_join'=>
                        array('table'=>'(select id as log_user_id, username as log_username, email as log_email from users) log_u', 'on'=>'log.log_user_id = log_u.log_user_id', 'type'=>'left') ),
                        array(), array($args['page'], $args['delimiter']), $args['order']);
                
                $this->data['all_count'] = isset($page_info[0])?$page_info[0]['count']:0;
                $this->data['all_page'] = isset($page_info[0])?$page_info[0]['all_page']:1;
                $this->data['now_page'] = isset($args['page'])?$args['page']:1;
                $this->data['delimiter'] = isset($args['page'])?$args['delimiter']:30;
                $this->data['paging'] = $this->acp->get_paging($args['page'], $page_info[0]['all_page'], 'activity/alarms/list');
                break;
            case "view":
                if (isset($args['id'])) {
                    $this->data['view'] = $this->user_db->_get_user_alarm_list(array('id'=>$args['id'],
                        'user_join'=>
                        array('table'=>'(select id as user_id, username, email from users) u', 'on'=>'user_alarms.user_id = u.user_id', 'type'=>'left'),
                        'log_join'=>
                        array('table'=>'(select id as log_id, user_id as log_user_id, area, type, data from log_activity) log', 'on'=>'user_alarms.ref_id = log.log_id', 'type'=>'left'),
                        'log_user_join'=>
                        array('table'=>'(select id as log_user_id, username as log_username, email as log_email from users) log_u', 'on'=>'log.log_user_id = log_u.log_user_id', 'type'=>'left') ), array(), array(1,1));
                } else {
                   redirect('/acp/activity/alarms/list/');
                }
                break;
            case "write":
                $this->data['field'] = array();
                
                $this->data['field']['mode'] = 'write';
                $this->data['field']['prefix']='TT';
                $this->data['field']['length']='8';
                $this->data['field']['amount']='1';
                $this->data['field']['comment']='';
                
                break;
            case "modify":
                if (isset($args['id'])) {
                    $form_data = $this->log_db->_get_list('activity', array('id'=>$args['id']), array(), array(1,1));
                    $this->data['field'] = $form_data[0];
                } else {
                   redirect('/acp/activity/alarms/list/');
                }
                break;
            case "delete":
                if (isset($args['id'])) {
                    $form_data = $this->log_db->_get_list('activity', array('id'=>$args['id']), array(), array(1,1));
                    $this->data['field'] = $form_data[0];
                } else {
                   redirect('/acp/activity/alarms/list/');
                }
                break;
            case "proc":
                return $this->_alarms_proc($this->input->post('mode'));
                break;
            default:
                break;
        }
        
        $this->data['subtab'] = $this->acp->get_subtab(array("list"=>"목록", "view"=>"보기", "write"=>"쓰기", "modify"=>"수정"), 
                                                        $mode, strtolower(get_class($this)).'/'.strtolower(__FUNCTION__).'/');
        
        $this->data['form_attr'] = array('class' => 'form', 'id' => 'alarms_'.$mode.'_form');
        $this->layout->view('acp/activity_alarms_'.$mode, $this->data);
    }
    
    /*
     * @brief process for activity_log
     * 
     * @param string $mode
     * 
     * @return null
     */
    function _alarms_proc($mode)
    {
        switch($mode) {
            case "write":
                $prefix = $this->input->post('prefix');
                $length = $this->input->post('length');
                $amount = $this->input->post('amount');
                $comment = $this->input->post('comment');
                
                if (!$prefix) {
                    alert('접두어가 필요합니다.');
                    redirect('/acp/activity/alarms/write/');                    
                } else if (!$length>0) {
                    alert('길이가 지나치게 작습니다.');
                    redirect('/acp/activity/alarms/write/');                    
                } else if (!$amount>1) {
                    alert('수량은 적어도 1개 이상입니다.');
                    redirect('/acp/activity/alarms/write/');                    
                }
                
        
                $this->load->library('codegen');
                
                for($i=0;$i<$amount;$i++) {
                    $code = $this->codegen->get_code($length, 0, 'time');
                    if($this->invite_code_db->_get_list(array('code'=>$prefix.$code), array(), array(1,1))==array()){
                        $this->invite_code_db->_insert(array('code'=>$prefix.$code, 'comment'=>$comment));
                    } else {
                        //log_message('debug', var_export(($this->invite_code_db->_get_list(array('code'=>$prefix.$code), '', array(1,1))!=array()), true));
                        $i--;
                    }
                }
                
                redirect('/acp/activity/alarms/list/');
                
                break;
            case "modify":
                
                $param = array();
                if($this->input->post('code')) $param['code']=$this->input->post('code');
                if($this->input->post('regdate')) $param['regdate']=$this->input->post('regdate');
                if($this->input->post('moddate')) $param['moddate']=$this->input->post('moddate');
                if($this->input->post('user_id')) $param['user_id']=$this->input->post('user_id');
                if($this->input->post('generate_user_id')) $param['generate_user_id']=$this->input->post('generate_user_id');
                if($this->input->post('comment')) $param['comment']=$this->input->post('comment');
                
                //var_export($this->input->post());

                $this->invite_code_db->_update($this->input->post('id'),$param);
                redirect('/acp/activity/alarms/view/id/'.$this->input->post('id'));
                
                break;
            case "delete":
                
                $this->invite_code_db->_delete($this->input->post('id'));
                redirect('/acp/activity/alarms/list/');
                
                break;
        }
    }
}
