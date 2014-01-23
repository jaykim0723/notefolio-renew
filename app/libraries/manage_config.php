<?php
/**
 * @brief Manage Config Library
 *
 * @author Yoon, Seongsu(soplel@snooey.net)
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Manage_config
{   
    var $config_path = '';

    function __construct($config=null) {
        $this->ci =& get_instance();
        $this->ci->load->helper('file');
        $this->config_path = BASEPATH.APPPATH."config/";
    }
    
    /* make config tpl
     * 
     * @param string $filename, string $data, string $tpl
     * 
     * @return bool
     */
    function make_tpl($filename, $data, $tpl=null)
    {
        if($tpl==null){
            $tpl  = "<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');\n";
            $tpl .= "$data\n";
            $tpl .= "\n";
            $tpl .= "/* End of file $filename.php */\n";
            $tpl .= "/* Location: ./application/config/$filename.php */\n";
        }
        
        return $tpl;
    }
    
    /* read config file raw
     * 
     * @param string $filename
     * 
     * @return bool
     */
    function read($filename)
    {
        return read_file($this->config_path."$filename.php");
    }
    
    /* write config file
     * 
     * @param string $filename, array $insertdata
     * 
     * @return bool
     */
    function write($filename, $insertdata)
    {
        $parsed_data = '';
        foreach ($insertdata as $k => $v) {
            $v = var_export($v, true);
            $parsed_data .= "\$config['$k'] = $v;\n";
        }
        
        $data = $this->make_tpl($filename, $parsed_data);
        
        return write_file($this->config_path."$filename.php", $data, 'w+');
    }
    
    /* delete config file
     * 
     * @param string $filename
     * 
     * @return bool
     */
    function delete($filename)
    {
        return unlink($this->config_path."$filename.php");
    }
}

?>