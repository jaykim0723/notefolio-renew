<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = "main";
$route['404_override'] = '';

//-- notefolio route

$route['(auth|auth_other|profile|upload|invite|info|main|feed|referrer|invite2|featured|active_user|alarm)'] = "$1"; // auth결과 메시지를 출력한다.
$route['(acp)'] = "$1/dashboard"; // acp 첫페이지.
// $route['(gallery)/(:num)'] = "$1/info/$2";
// $route['(gallery)/(:num)/(:any)'] = "$1/$3/$2";
$route['(gallery|profile|auth|auth_other|acp|comment|main|feed|feed_new|alarm)/(:any)/(:any)'] = "$1/$2/$3";
$route['(gallery|profile|auth|auth_other|acp|comment|upload|info|main|feed|factive_user|alarm)/(:any)'] = "$1/$2";
$route['(gallery|profile|auth|auth_other|acp|comment|upload|info|main|feed|factive_user|alarm)'] = "$1";

$route['(:any)/(gallery|collection|about|statistics|following|followers)/(:num)'] = "profile/$2/$1/$3"; // tabs list
$route['(:any)/(gallery|collection|about|statistics|following|followers)/(:any)'] = "$2/$3/$1"; // maxzidell/gallery/upload -> gallery/upload/maxzidell
$route['(:any)/(gallery|collection|about|statistics|following|followers)'] = "profile/$2/$1"; // tabs
$route['(:any)/(:num)/(update|delete)'] = "gallery/$3/$2"; 
$route['(:any)/(:num)'] = "gallery/info/$2";
$route['(:any)'] = "profile/myworks/$1";


/* End of file routes.php */
/* Location: ./application/config/routes.php */