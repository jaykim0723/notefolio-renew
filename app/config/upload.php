<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @brief Notefolio Upload Config File
 * @author Yoon, Seongsu(sople1@snooey.net)
 * 
 */
 
$config['upload_path']       = APPPATH.'../www/data/';
$config['upload_uri']        = '/data/';
$config['img_upload_path']   = $config['upload_path'].'img/';
$config['img_upload_uri']    = $config['upload_uri'].'img/';
$config['cover_upload_path'] = $config['upload_path'].'cover/';
$config['cover_upload_uri']  = $config['upload_uri'].'cover/';
$config['img_allowed_types'] = 'gif|jpg|jpeg|png|bmp';

//manage size
$config['thumbnail_large']=array('max_width'  => 1600, //pixel
                                'max_height'  =>   0  //pixel, not using
                                );
$config['thumbnail_medium']=array('max_width' => 800,  //pixel
                                'max_height'  =>   0  //pixel, not using
                                );
$config['thumbnail_wide']= array('max_width'  => 800,  //pixel
                                'max_height'  => 400  //pixel
                                );
$config['thumbnail_single']=array('max_width' => 400,  //pixel
                                'max_height'  => 400  //pixel
                                );
$config['thumbnail_small']= array('max_width' => 300,  //pixel
                                'max_height'  => 300  //pixel
                                );