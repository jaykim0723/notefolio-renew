<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * ACP Helper
 *
 */

/**
 * @brief get paging
 * 
 * @param array $params
 * @return string
 */
function get_paging($params=array()){
	$params = (object)$params;
	$default_params = (object)array(
        'now_page'      => 1, 
        'print_max' => 9, 
        'last_page' => 9, 
        'url'  => '', 
        'url_affix' => array(),
        'location'  => '/acp', 
	);

	foreach($default_params as $key => $value){
		if(!isset($params->{$key}))
			$params->{$key} = $value;
	}

    if(isset($params->url_affix['page'])) unset($params->url_affix['page']); //중복방지

    $CI =& get_instance();
    $url_affix = $CI->uri->assoc_to_uri($params->url_affix);
    $url_affix .= (empty($url_affix))?'':'/';

    $begin = ($params->now_page)-ceil((($params->print_max)-1)/2);
    $end = ($params->now_page)+floor((($params->print_max)-1)/2);
    if ($begin<1) {
        if(($params->last_page)>($end-$begin+1))
            $end = $end - $begin+1;
        else $end = ($params->last_page);
        $begin = 1;
    } else if ($end>($params->last_page)) {
        $begin = $begin + ($params->last_page) - $end;
        if($begin<1 )
            $begin = 1;
        else $end = ($params->last_page);
        $end = ($params->last_page);
    }
    
    $output = "<div id=\"pagenation-box\">\n";       
    $output .= "  <ul class=\"pagination \">\n";    
    $output .= "    <li><a href=\"{$params->location}{$params->url}/{$url_affix}page/".((($params->now_page)!=1)?($params->now_page)-1:1)."\">«</a></li>\n";
	$output .= "    <li><a href=\"{$params->location}{$params->url}/{$url_affix}page/1\">1</a></li>\n";
    $output .= "    <li><a href=\"{$params->location}{$params->url}/{$url_affix}page/1\" class='prev_page'>...</a></li>\n";
	
    for($i=$begin;$i<=$end;$i++) {
       if ($i == ($params->now_page)) {
           $current_class = " class=\"active\"";
       }
       else $current_class = "";
            
       $output.="<li id=\"acp_pagenav_$i\"$current_class><a href=\"{$params->location}{$params->url}/{$url_affix}page/{$i}\">{$i}</a></li>\n";
       
    }
	
    $output .= "    <li><a href=\"{$params->location}{$params->url}/{$url_affix}page/{$params->last_page}\"class='next_page'>...</a></li>\n";   
    $output .= "    <li><a href=\"{$params->location}{$params->url}/{$url_affix}page/{$params->last_page}\">{$params->last_page}</a></li>\n";    
	$output .= "    <li><a href=\"{$params->location}{$params->url}/{$url_affix}page/".((($params->now_page)!=($params->last_page))?($params->now_page)+1:($params->last_page))."\">»</a></li>\n"; 
	
    $output .= "  <ul>\n";    
    $output .= "</div>\n";
    
    return $output;
}