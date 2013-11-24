<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_DB_active_record extends CI_DB_active_record {

    /**
     * Get SELECT query string
     *
     * Compiles a SELECT query string and returns the sql.
     *
     * @param   string  the table name to select from (optional)
     * @param   bool    TRUE: resets QB values; FALSE: leave QB vaules alone
     * @return  string
     */
    public function get_compiled_select($table = '', $reset = TRUE)
    {
        if ($table !== '')
        {
            $this->_track_aliases($table);
            $this->from($table);
        }

        $select = $this->_compile_select();

        if ($reset === TRUE)
        {
            $this->_reset_select();
        }

        return $select;
    }

    // --------------------------------------------------------------------

    /**
     * Get INSERT query string
     *
     * Compiles an insert query and returns the sql
     *
     * @param   string  the table to insert into
     * @param   bool    TRUE: reset QB values; FALSE: leave QB values alone
     * @return  string
     */
    public function get_compiled_insert($table = '', $reset = TRUE)
    {
        if ($this->_validate_insert($table) === FALSE)
        {
            return FALSE;
        }

        $sql = $this->_insert(
            $this->protect_identifiers(
                $this->qb_from[0], TRUE, NULL, FALSE
            ),
            array_keys($this->qb_set),
            array_values($this->qb_set)
        );

        if ($reset === TRUE)
        {
            $this->_reset_write();
        }

        return $sql;
    }

    // --------------------------------------------------------------------

    /**
     * Get UPDATE query string
     *
     * Compiles an update query and returns the sql
     *
     * @param   string  the table to update
     * @param   bool    TRUE: reset QB values; FALSE: leave QB values alone
     * @return  string
     */
    public function get_compiled_update($table = '', $reset = TRUE)
    {
        // Combine any cached components with the current statements
        $this->_merge_cache();

        if ($this->_validate_update($table) === FALSE)
        {
            return FALSE;
        }

        $sql = $this->_update($this->protect_identifiers($this->qb_from[0], TRUE, NULL, FALSE), $this->qb_set);

        if ($reset === TRUE)
        {
            $this->_reset_write();
        }

        return $sql;
    }
}

/* End of file DB_active_rec.php */
/* Location: ./system/database/DB_active_rec.php */