<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


// 경고메세지를 경고창으로
function alert($msg='', $url='') {

 if ($msg=='') $msg = '올바른 방법으로 이용해 주십시오.';

 echo "<!doctype html><html><head><meta charset='utf-8'><meta http-equiv=\"content-type\" content=\"text/html; charset=\"utf-8\"></head><body>";
 echo "<script type='text/javascript'>alert('".$msg."');";
    if ($url)
        echo "location.replace('".site_url($url)."');";
 else
  echo "history.go(-1);";
 echo "</script></body></html>";
 exit;
}

