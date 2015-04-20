<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Upload extends CI_Upload {  
    public function __construct() {
        return parent::__construct();
    }

    function file_mime_type($file){    
    	return $this->_file_mime_type($file);
    }
    function prep_filename($filename){
log_message('debug','----------------prep_filename : '.$filename);
    	return $this->_prep_filename($filename);
    }
}  
