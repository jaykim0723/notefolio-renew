<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @brief migrate(original) Controller
 *
 * @author Yoon, Seongsu(soplel@snooey.net)
 */
 
class migrate extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 */
	public function index($to='null')
	{
		echo "Hello {$to}!".PHP_EOL;
	}
    
	/**
	 * migrate info for user list
	 *
	 * @param int $user_id
	 */
	public function get_user(){
        //-- facebook post 
        $cmd = 'php ../../notefolio-web/app_cli/cli.php migrate user_list';
        var_export(exec($cmd));  
	}

	/**
	 * migrate info for work list
	 *
	 */
	public function get_work(){
	}

}

/* End of file migrate.php */
/* Location: ./application/controllers/migrate.php */