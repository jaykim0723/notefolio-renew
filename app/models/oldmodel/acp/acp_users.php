<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @brief Notefolio Acp Users Model
 *
 * @author Yoon, Seongsu(soplel@snooey.net)
 */
 
class Acp_users extends CI_Model
{
    private $table_name         = 'acp_user';          // user accounts

    function __construct()
    {
        parent::__construct();

        $ci =& get_instance();
    }

    /**
     * Get user record by Id
     *
     * @param int $user_id, int $level
     * @return  object
     */
    function get_user($user_id, $level=0)
    {
        $this->db->where('id', $user_id);
        $this->db->where('level >', $level);
        
        $query = $this->db->get($this->table_name);
        if ($query->num_rows() == 1) return $query->row();
        return NULL;
    }
}

/* End of file users.php */
/* Location: ./application/models/acp/acp_users.php */