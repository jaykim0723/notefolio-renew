<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @brief Notefolio Admin Control Pannel Config File
 * @author Yoon, Seongsu(sople1@snooey.net)
 * 
 */
 
$config['upload_path']       = APPPATH.'../www/data/';
$config['img_upload_path']   = $config['upload_path'].'img/';
$config['cover_upload_path'] = $config['upload_path'].'cover/';
$config['img_allowed_types'] = 'gif|jpg|jpeg|png|bmp';