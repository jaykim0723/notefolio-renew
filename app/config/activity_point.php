<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Activity Point Config File
 * @author Yoon, Seongsu(sople1@snooey.net)
 * 
 */
 
 $config['ap'] = array(
 	'user' => array(
 			'create'			=> 0,
 			'update'			=> 0,
 			'delete' 			=> 0,

 			'follow' 			=> 0,

 			'hot_creators'	 	=> 0,

 			'connect_facebook' 	=> 0,

 			'profile_face'		=> 0,
 			'profile_background'=> 0,

 			'folder'			=> 0,
 		),
 	'work' => array(
 			'create'			=> 0,
 			'update'			=> 0,
 			'delete'			=> 0,

 			'view'				=> 0.5,
 			'comment'			=> 0.5,
 			'note'				=> 1.5,
 			'collect'			=> 2,
 		),
 	);