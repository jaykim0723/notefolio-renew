<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Statics extends CI_Controller {

	public function index()
	{
		$this->post_user_active_log();
	}

	public function post_user_active_log($date=null)
	{
		if($date==null)
			$date = date("Y-m-d");
		echo("Date: $date".PHP_EOL);

		$this->load->model('db/log_user_active_db');

		$this->db->trans_start();
		$this->log_user_active_db->_insert(array('date'=>$date));
		$this->log_user_active_db->_update($date);
		$this->db->trans_complete();

		if($this->db->trans_status())
		{
			//-- success
			echo "successed.".PHP_EOL;
		} else {
			//-- fail
			echo "failed.".PHP_EOL;
		}	


/*
insert into log_user_actives
	set	date = '';
update log_user_actives 
set 
    upload_day = (SELECT 
            count(distinct user_id)
        FROM
            works
        where
            date_format(regdate, '%Y-%m-%d') = date),
    upload_week = (select 
            count(distinct user_id)
        from
            works
        where
            regdate between date_add(date, interval '-1' week) and date),
    upload_month = (select 
            count(distinct user_id)
        from
            works
        where
            regdate between date_add(date, interval '-1' month) and date),
    upload_total = (select 
            count(distinct user_id)
        from
            works
        where
            regdate <= date),
    logged_in_day = (SELECT 
            count(distinct id)
        FROM
            users
        where
            date_format(last_login, '%Y-%m-%d') = date),
    logged_in_week = (select 
            count(distinct id)
        from
            users
        where
            last_login between date_add(date, interval '-1' week) and date),
    logged_in_month = (select 
            count(distinct id)
        from
            users
        where
            last_login between date_add(date, interval '-1' month) and date),
    logged_in_month_three = (select 
            count(distinct id)
        from
            users
        where
            last_login between date_add(date, interval '-3' month) and date)
where
    date = '';

*/

	}

}

/* End of file statics.php */
/* Location: ./application/controllers/statics.php */