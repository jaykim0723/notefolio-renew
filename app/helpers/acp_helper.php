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
        'url'  => '', 
        'location'  => '/acp/', 
	);
	foreach($default_params as $key => $value){
		if(!isset($params->{$key}))
			$params->{$key} = $value;
	}
    $begin = ($params->now_page)-((($params->print_max)-1)/2);
    $end = ($params->now_page)-((($params->print_max)-1)/2);
    if ($begin<1) {
        if(($params->print_max)>($end-$begin+1))
            $end = $end - $begin+1;
        else $end = ($params->print_max);
        $begin = 1;
    } else if ($end>($params->print_max)) {
        $begin = $begin + ($params->print_max) - $end;
        if($begin<1 )
            $begin = 1;
        else $end = ($params->print_max);
        $end = ($params->print_max);
    }
    
    $output = "<div class=\"pagination-note pagination-note-centered \">\n";       
    $output .= "  <ul>\n";    
    $output .= "    <li><a href=\"{$params->location}{$params->url}/page/".((($params->now_page)!=1)?($params->now_page)-1:1)."\" class='pagination-left-arr'></a></li>\n";
	$output .= "    <li><a href=\"{$params->location}{$params->url}/page/1\">1</a></li>\n";
    $output .= "    <li><a href=\"{$params->location}{$params->url}/page/1\" class='prev_page'>...</a></li>\n";
	
    for($i=$begin;$i<=$end;$i++) {
       if ($i == ($params->now_page)) {
           $current_class = " class=\"active\"";
       }
       else $current_class = "";
            
       $output.="<li id=\"acp_pagenav_$i\"$current_class><a href=\"{$params->location}{$params->url}/page/{$i}\">{$i}</a></li>\n";
       
    }
	
    $output .= "    <li><a href=\"{$params->location}{$params->url}/page/{$params->print_max}\"class='next_page'>...</a></li>\n";   
    $output .= "    <li><a href=\"{$params->location}{$params->url}/page/{$params->print_max}\">{$params->print_max}</a></li>\n";    
	$output .= "    <li><a href=\"{$params->location}{$params->url}/page/".((($params->now_page)!=($params->print_max))?($params->now_page)+1:($params->print_max))."\" class='pagination-right-arr'></a></li>\n"; 
	
    $output .= "  <ul>\n";    
    $output .= "</div>\n";
    
    return $output;
}