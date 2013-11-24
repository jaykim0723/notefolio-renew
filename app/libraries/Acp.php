<?php
/**
 * @brief Notefolio Acp Library
 *
 * @author Yoon, Seongsu(soplel@snooey.net)
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Acp {
    public $idx = null;
    public $id = null;
    public $name = null;
    public $default_location = '/acp/';
    
    function __construct() {
        $this->ci =& get_instance();

        $this->ci->load->config('acp', TRUE);
        $this->default_location = $this->ci->config->item('acp_default_location', 'acp');
        $this->ci->load->model('oldmodel/acp/acp_users');
    }
    
    /*
     * @brief get menu from config
     * 
     * @param string $current
     * @return string
     */
    function get_menu ($current='') {
        $menu = $this->ci->config->item('acp_menu', 'acp');
        
        $output = ""; //init output
        
        foreach($menu as $key => $row ){
            if ($key == $current) {
                $current_class = " class=\"active\"";
            }
            else $current_class = "";
            $output.="<li id=\"acp_$key\"$current_class><a href=\"{$this->default_location}{$row['url']}\">{$row['text']}</a></li>\n";
        }
        
        
        return $output;
    }
    
    /*
     * @brief get submenu from config
     * 
     * @param string $current, string $current
     * @return string
     */
    function get_submenu ($parent='', $current='') {
        $menu = $this->ci->config->item('acp_menu', 'acp');
        
        $output = ""; //init output
        
        if ($parent!='' && $menu[$parent] != null) {
            $menu = $menu[$parent];
        
            $output ="<li class=\"nav-header\">{$menu['text']}</li>\n";
            
            if ($menu['submenu'] != null) {
                foreach($menu['submenu'] as $key => $row ){
                    if ($key == $current) {
                        $current_class = " class=\"active\"";
                    }
                    else $current_class = "";
                    
                    $output.="<li id=\"acp_sub_$key\"$current_class><a href=\"{$this->default_location}{$menu['url']}{$row['url']}\">{$row['text']}</a></li>\n";
                }
            }
        }
        
        return $output;
    }
    
    /*
     * @brief get subtab
     * 
     * @param array $list, string $now, string $default_url
     * @return string
     */
    function get_subtab ($list=array(), $now='', $default_url='') {
        /*
         * <ul class="nav nav-tabs">
         *   <li class="active">
         *     <a href="#">Home</a>
         *   </li>
         *   <li><a href="#">...</a></li>
         *   <li><a href="#">...</a></li>
         * </ul>
         */
        
        $output = "<ul class=\"nav nav-tabs\">\n"; //init output
            
        if ($list!=array()) {
            foreach($list as $key => $val ){
                if ($key == $now) {
                    $current_class = " class=\"active\"";
                }
                else $current_class = "";
                
                $output.="<li id=\"acp_subtab_$key\"$current_class><a href=\"{$this->default_location}{$default_url}{$key}\">{$val}</a></li>\n";
            }
        }
        
        $output .= "</ul>\n"; //end output
        
        return $output;
    }
    
    /*
     * @brief get paging
     * 
     * @param array $list, string $now, string $default_url
     * @return string
     */
    function get_paging ($now=1, $all=9, $default_url='') {
        /*
         *<div class="pagination">
         *  <ul>
         *    <li><a href="#">Prev</a></li>
         *    <li><a href="#">1</a></li>
         *    <li><a href="#">2</a></li>
         *    <li><a href="#">3</a></li>
         *    <li><a href="#">4</a></li>
         *    <li><a href="#">Next</a></li>
         *  </ul>
         *</div>
         */
        $begin = $now-4;
        $end = $now+4;
        if ($begin<1) {
            if($all>($end-$begin+1))
                $end = $end - $begin+1;
            else $end = $all;
            $begin = 1;
        } else if ($end>$all) {
            $begin = $begin + $all - $end;
            if($begin<1 )
                $begin = 1;
            else $end = $all;
            $end = $all;
        }
        
        $output = "<div class=\"pagination-note pagination-note-centered \">\n";       
        $output .= "  <ul>\n";    
        //$output .= "    <li><a href=\"{$this->default_location}{$default_url}/page/".(($begin==1)?1:$begin)."\" class='pagination-left-arr'></a></li>\n"; //원래코드
        $output .= "    <li><a href=\"{$this->default_location}{$default_url}/page/".(($now!=1)?$now-1:1)."\" class='pagination-left-arr'></a></li>\n";
		$output .= "    <li><a href=\"{$this->default_location}{$default_url}/page/1\">1</a></li>\n";
        $output .= "    <li><a href=\"{$this->default_location}{$default_url}/page/1\" class='prev_page'>...</a></li>\n";
		
        for($i=$begin;$i<=$end;$i++) {
           if ($i == $now) {
               $current_class = " class=\"active\"";
           }
           else $current_class = "";
                
           $output.="<li id=\"acp_pagenav_$i\"$current_class><a href=\"{$this->default_location}{$default_url}/page/{$i}\">{$i}</a></li>\n";
           
        }
		
        $output .= "    <li><a href=\"{$this->default_location}{$default_url}/page/$all\"class='next_page'>...</a></li>\n";   
        $output .= "    <li><a href=\"{$this->default_location}{$default_url}/page/$all\">$all</a></li>\n";    
        //$output .= "    <li><a href=\"{$this->default_location}{$default_url}/page/".(($end==$all)?$all:$end)."\" class='pagination-right-arr'></a></li>\n"; //원래 코드
		$output .= "    <li><a href=\"{$this->default_location}{$default_url}/page/".(($now!=$all)?$now+1:$all)."\" class='pagination-right-arr'></a></li>\n"; 
		
        $output .= "  <ul>\n";    
        $output .= "</div>\n";
        
        return $output;
    }
    /**
     * elevate user to administrator level
     *
     * @param int $user_id
     * @return  bool
     */
    function elevate($user_id)
    {
        if ($user_id > 0) {

            if (!is_null($user = $this->ci->acp_users->get_user($user_id))) {   // can elevate

                $this->ci->session->set_userdata(array(
                        'acp_user_id'   => $user->id,
                        'acp_user_level'  => $user->level,
                ));
                return TRUE;
            } else {                                                            // fail - wrong login
                $this->error = array('login' => 'level_is_low');
            }
        }
        return FALSE;
    }

    /**
     * unelevate administrator level
     *
     * @return  void
     */
    function unelevate()
    {
        $this->ci->session->set_userdata(array('acp_user_id' => '', 'acp_user_level' => ''));
    }

    /**
     * if user can be admin?
     *
     * @param int $user_id
     * @return  int
     */
    function check_elevate($user_id)
    {
        if ($user_id > 0) {

            if (!is_null($user = $this->ci->acp_users->get_user($user_id))) {   // can elevate
                return $user->level;
            } else {                                                            // fail - wrong login
                return 0;
            }
        }
        return 0;
    }

    /**
     * Check if user is elevated..
     * 
     * @return  bool
     */
    function is_elevated()
    {
        return $this->ci->session->userdata('acp_user_level');
    }

    /**
     * Get user_id [for admin]
     *
     * @return  string
     */
    function get_user_id()
    {
        return $this->ci->session->userdata('acp_user_id');
    }
}