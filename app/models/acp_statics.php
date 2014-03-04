<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @brief Notefolio Acp Statics Model
 *
 * @author Yoon, Seongsu(soplel@snooey.net)
 */
 
class Acp_statics extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $ci =& get_instance();
    }

//-- user

    /**
     * Get User Join data
     *
     * @param string $from, string $to
     * @return array
     */
    function get_user_join($from=null, $to=null)
    {
        if($to  ==null) $to  =date('Y-m-d');
        if($from==null) $from=date('Y-m-d', strtotime("-1 week", strtotime($to)));
        
        $printYear = ((int)(substr($from, 0, 4))<(int)(date('Y')))?true:false;

        $period = new DatePeriod(
             new DateTime($from),
             new DateInterval('P1D'),
             new DateTime($to)
        );

        $output = array(array('날짜' , '회원'));
        $data = array();

        $sql = "SELECT DATE_FORMAT(created, '%Y-%m-%d') as date, count(id) as count FROM users WHERE created Between ? and ? group by date"; 
        $query = $this->db->query($sql, array($from, $to));
        foreach ($query->result() as $row)
        {
            $data[$row->date] = round($row->count);
        }

        $i = 1;
        foreach ($period as $date) {
            $output[$i] = array($date->format((($printYear)?'Y년 ':'').' m월 d일'), isset($data[$date->format('Y-m-d')])?$data[$date->format('Y-m-d')]:0);
            $i++;
        }

        return $output;
        /*
        //---- dummy data
        $dateNum = floor((strtotime($to)-strtotime($from))/86400)+1;
        $output = array(array('날짜' , '회원'));
        if($dateNum>0){
            for($i=1;$i<=$dateNum;$i++){
                $date = date("m월 d일",strtotime(($i-$dateNum)." day", strtotime($to)));
                $num  = rand(80, 150);
                $output[$i] = array($date, $num);
            }
        }
        return $output;*/
    }

    /**
     * Get User Join with Facebook data
     *
     * @param string $from, string $to
     * @return array
     */
    function get_user_join_with_facebook($from=null, $to=null)
    {
        if($to  ==null) $to  =date('Y-m-d');
        if($from==null) $from=date('Y-m-d', strtotime("-1 week", strtotime($to)));
        
        $printYear = ((int)(substr($from, 0, 4))<(int)(date('Y')))?true:false;

        $period = new DatePeriod(
             new DateTime($from),
             new DateInterval('P1D'),
             new DateTime($to)
        );

        $output = array(array('날짜' , '총연동', '가입일 연동일 일치', '가입일 연동일 불일치'));
        $data = array();

        $sql = "SELECT
                DATE_FORMAT(join_date, '%Y-%m-%d') as date,
                count(user_id) as user_count,
                SUM(case when fb_date is not null then 1 else 0 end) user_fb_count,
                SUM(case when fb_date is not null AND DATE_FORMAT(join_date, '%Y-%m-%d') = DATE_FORMAT(fb_date, '%Y-%m-%d')  then 1 else 0 end) as same_count, 
                SUM(case when fb_date is not null AND DATE_FORMAT(join_date, '%Y-%m-%d') <> DATE_FORMAT(fb_date, '%Y-%m-%d') then 1 else 0 end) as diff_count
                from
                (
                select 
                    users.id as user_id,
                    users.created as join_date,
                    user_sns_fb.regdate as fb_date
                from
                    users
                    left join user_sns_fb on user_sns_fb.id = users.id
                where 
                    users.id is not null
                    and users.activated =? and users.created between ? and ?
                ) fb
                group by date"; 
        $query = $this->db->query($sql, array(1, $from, $to));
        foreach ($query->result() as $row)
        {
            $data[$row->date] = array(round($row->user_fb_count*100/$row->user_count, 2), round($row->same_count*100/$row->user_count, 2), round($row->diff_count*100/$row->user_count, 2));
        }

        $i = 1;
        foreach ($period as $date) {
            $output[$i] = array($date->format((($printYear)?'Y년 ':'').' m월 d일'), isset($data[$date->format('Y-m-d')][0])?$data[$date->format('Y-m-d')][0]:0, isset($data[$date->format('Y-m-d')][1])?$data[$date->format('Y-m-d')][1]:0, isset($data[$date->format('Y-m-d')][2])?$data[$date->format('Y-m-d')][2]:0);
            $i++;
        }

        return $output;
        /*
        //---- dummy data
        $dateNum = floor((strtotime($to)-strtotime($from))/86400)+1;
        $output = array(array('날짜' , '총연동', '가입일 연동일 일치', '가입일 연동일 불일치'));
        if($dateNum>0){
            for($i=1;$i<=$dateNum;$i++){
                $date = date("m월 d일",strtotime(($i-$dateNum)." day", strtotime($to)));
                $num1  = rand(70, 100);
                $num2  = rand(0, 100);
                $num3  = 100-$num2;
                $output[$i] = array($date, $num1, $num2, $num3);
            }
        }
        return $output;*/
    }

    /**
     * Get User Just Upload At Join data
     *
     * @param string $from, string $to
     * @return array
     */
    function get_user_just_upload_at_join($from=null, $to=null)
    {
        if($to  ==null) $to  =date('Y-m-d');
        if($from==null) $from=date('Y-m-d', strtotime("-1 week", strtotime($to)));
        
        $printYear = ((int)(substr($from, 0, 4))<(int)(date('Y')))?true:false;

        $period = new DatePeriod(
             new DateTime($from),
             new DateInterval('P1D'),
             new DateTime($to)
        );

        $output = array(array('날짜' , '비율'));
        $data = array();

        //query
        $sql = "SELECT joined, uploaded, user_count from
                (select 
                    DATE_FORMAT(users.created, '%Y-%m-%d') as joined,
                    count(works.user_id) as uploaded
                from
                    users,
                    works
                where
                    users.id = works.user_id
                    and date(works.regdate) = date(users.created)
                    and regdate between ? and ?
                group by joined
                order by works.user_id) list
                left join (
                select DATE_FORMAT(users.created, '%Y-%m-%d') as date, count(id) as user_count 
                from users
                where 
                    users.activated = ? and users.created between ? and ?
                group by date
                ) user
                on list.joined = user.date"; 
        $query = $this->db->query($sql, array($from, $to, 1, $from, $to));
        foreach ($query->result() as $row)
        {
            $data[$row->joined] = round($row->uploaded*100/$row->user_count, 2);
        }

        $i = 1;
        foreach ($period as $date) {
            $output[$i] = array($date->format((($printYear)?'Y년 ':'').' m월 d일'), isset($data[$date->format('Y-m-d')])?$data[$date->format('Y-m-d')]:0);
            $i++;
        }

        return $output; 
        
        /*
        //---- dummy data
        $dateNum = floor((strtotime($to)-strtotime($from))/86400)+1;
        $output = array(array('날짜' , '회원'));
        if($dateNum>0){
            for($i=1;$i<=$dateNum;$i++){
                $date = date("m월 d일",strtotime(($i-$dateNum)." day", strtotime($to)));
                $num  = rand(80, 150);
                $output[$i] = array($date, $num);
            }
        }
        return $output;*/
    }

    /**
     * Get User Upload Term data
     *
     * @param string $from, string $to
     * @return array
     */
    function get_user_upload_term($from=null, $to=null)
    {
        
        if($to  ==null) $to  =date('Y-m-d');
        if($from==null) $from=date('Y-m-d', strtotime("-1 week", strtotime($to)));

        $printYear = ((int)(substr($from, 0, 4))<(int)(date('Y')))?true:false;

        $period = new DatePeriod(
             new DateTime($from),
             new DateInterval('P1D'),
             new DateTime($to)
        );

        $output = array(array('날짜' , '일주일', '한달', '전기간'));
        $data = array();

        $sql = "SELECT 
                    date_format(date, '%Y-%m-%d') as date,
                    upload_week,
                    upload_month,
                    upload_total
                from
                    log_user_actives
                WHERE date between ? and ?
                group by date"; 
        $query = $this->db->query($sql, array($from, $to));
        foreach ($query->result() as $row)
        {
            $data[$row->date] = array(round($row->upload_week), round($row->upload_month), round($row->upload_total), );
            $i++;
        }

        $i = 1;
        foreach ($period as $date) {
            $output[$i] = array($date->format((($printYear)?'Y년 ':'').' m월 d일'), isset($data[$date->format('Y-m-d')][0])?$data[$date->format('Y-m-d')][0]:0, isset($data[$date->format('Y-m-d')][1])?$data[$date->format('Y-m-d')][1]:0, isset($data[$date->format('Y-m-d')][2])?$data[$date->format('Y-m-d')][2]:0);
            $i++;
        }

        return $output;

        //---- dummy data
        $dateNum = floor((strtotime($to)-strtotime($from))/86400)+1;
        $output = array(array('날짜' , '일주일', '한달', '전기간'));
        if($dateNum>0){
            for($i=1;$i<=$dateNum;$i++){
                $date = date("m월 d일",strtotime(($i-$dateNum)." day", strtotime($to)));
                $num1  = rand(80, 150);
                $num2  = rand(400, 1500);
                $num3  = rand(4000, 15000);
                $output[$i] = array($date, $num1, $num2, $num3);
            }
        }
        return $output;
    }

    /**
     * Get User joiners gender data
     *
     * @param string $from, string $to
     * @return array
     */
    function get_user_join_gender($from=null, $to=null)
    {
        if($to  ==null) $to  =date('Y-m-d');
        if($from==null) $from=date('Y-m-d', strtotime("-1 week", strtotime($to)));
        
        $printYear = ((int)(substr($from, 0, 4))<(int)(date('Y')))?true:false;

        $period = new DatePeriod(
             new DateTime($from),
             new DateInterval('P1D'),
             new DateTime($to)
        );

        $output = array(array('날짜', '남자', '여자'));
        $data = array();

        //query
        $sql = "SELECT  DATE_FORMAT(created, '%Y-%m-%d') as joined,
                        SUM(case when gender = 'm' then 1 else 0 end) as gender_m_count, 
                        SUM(case when gender = 'f' then 1 else 0 end) as gender_f_count, 
                        count(user_id) as user_count
                from users 
                left join user_profiles on users.id = user_profiles.user_id 
                WHERE activated =? and created between ? and ?
                group by joined"; 
        $query = $this->db->query($sql, array(1, $from, $to, $from, $to));
        foreach ($query->result() as $row)
        {
            $data[$row->joined] = array(round($row->gender_f_count*100/$row->user_count), round($row->gender_m_count*100/$row->user_count));
        }

        $i = 1;
        foreach ($period as $date) {
            $output[$i] = array($date->format((($printYear)?'Y년 ':'').' m월 d일'), isset($data[$date->format('Y-m-d')][0])?$data[$date->format('Y-m-d')][0]:0, isset($data[$date->format('Y-m-d')][1])?$data[$date->format('Y-m-d')][1]:0);
            $i++;
        }

        return $output;    
        /*
        //---- dummy data
        $dateNum = floor((strtotime($to)-strtotime($from))/86400)+1;
        $output = array(array('날짜' , '남자', '여자'));
        if($dateNum>0){
            for($i=1;$i<=$dateNum;$i++){
                $date = date("m월 d일",strtotime(($i-$dateNum)." day", strtotime($to)));
                $num1  = rand(0, 70);
                $num2  = 100 - $num1;
                $output[$i] = array($date, $num1, $num2);
            }
        }
        return $output;*/
    }

    /**
     * Get User's percentage by age data
     *
     * @param string $from, string $to
     * @return array
     */
    function get_user_percentage_age($from=null, $to=null)
    {
        if($to  ==null) $to  =date('Y-m-d');
        if($from==null) $from=date('Y-m-d', strtotime("-1 week", strtotime($to)));
 
        $list = array(
            array(0),
            array(1, 10),
            range(11, 15),
            range(16, 19),
            range(20, 22),
            range(23, 25),
            range(26, 28),
            range(29, 30),
            range(31, 40),
            range(41, 200)
            );

        $output = array(array('연령' , '비율'));
        $data = array();

        $i = 1;
        //query
        $sql = "SELECT  DATE_FORMAT(CURRENT_DATE, '%Y')-DATE_FORMAT(birth, '%Y')+1 as age,
                        count(gender) as age_count, 
                        count(user_id) as user_count, all_count 
                    from users 
                    left join user_profiles on users.id = user_profiles.user_id 
                    join (select count(id) as all_count from users where activated=? and created between ? and ?) all_user 
                    WHERE created between ? and ?
                    group by age"; 
        $query = $this->db->query($sql, array(1, $from, $to, $from, $to));
        foreach ($query->result() as $row)
        {
            if(is_null($row->age)) continue;
            foreach($list as $k=>$v){
                if(in_array($row->age, $v)){
                    $data[$k] += round($row->age_count*100/$row->all_count, 2);
                }
            }
        }

        foreach($list as $k=>$v){
            $i = $k+1;
            $output[$i][0] = (string)((count($v)>1)?$v[0]."~".$v[count($v)-1]:"{$v[0]}");
            $output[$i][1] = round($data[$k], 2);
        }

        return $output;     
        /*  
        //---- dummy data
        $dateNum = floor((strtotime($to)-strtotime($from))/86400)+1;
        $output = array(array('연령' , '비율'));
        if($dateNum>0){
            $AxisX = array("0", "1~10", "11~15", "16~19", "20~22", "23~25", "26~28", "29~30", "31~40", "41~");
            $i = 1;
            foreach($AxisX as $text){
                $normal = array("11~15", "16~19", "26~28", "29~30", "31~40", "41~");
                $large = array("20~22", "23~25", "26~28");
                if(in_array($text, $normal)){
                    $num = rand(70, 100);
                } else if(in_array($text, $large)){
                    $num = rand(0, 30);
                } else{
                    $num = 0;
                }
                $output[$i] = array($text, $num);
                $i++;
            }
        }
        return $output;*/
    }

    /**
     * Get User's gender percentage by age data
     *
     * @param string $from, string $to
     * @return array
     */
    function get_user_percentage_gender_age($from=null, $to=null)
    {
        if($to  ==null) $to  =date('Y-m-d');
        if($from==null) $from=date('Y-m-d', strtotime("-1 week", strtotime($to)));
 
        $list = array(
            array(0),
            array(1, 10),
            range(11, 15),
            range(16, 19),
            range(20, 22),
            range(23, 25),
            range(26, 28),
            range(29, 30),
            range(31, 40),
            range(41, 200)
            );

        //select DATE_FORMAT(CURRENT_DATE, '%Y')-DATE_FORMAT(birth, '%Y')+1 as age, SUM(case when gender = 'm' then 1 else 0 end) as gender_m_count, SUM(case when gender = 'f' then 1 else 0 end) as gender_f_count, count(user_id) as user_count, all_count from users left join user_profiles on users.id = user_profiles.user_id join (select count(id) as all_count from users where activated=1) all_user group by age
        $output = array(array('연령' , '남비율', '여비율'));
        $data = array();

        $i = 1;
        //query
        $sql = "SELECT  DATE_FORMAT(CURRENT_DATE, '%Y')-DATE_FORMAT(birth, '%Y')+1 as age,
                        SUM(case when gender = 'm' then 1 else 0 end) as gender_m_count, 
                        SUM(case when gender = 'f' then 1 else 0 end) as gender_f_count, 
                        count(user_id) as user_count, all_count 
                    from users 
                    left join user_profiles on users.id = user_profiles.user_id 
                    join (select count(id) as all_count from users where activated=? and created >= ? and created <= ?) all_user 
                    WHERE created between ? and ?
                    group by age"; 
        $query = $this->db->query($sql, array(1, $from, $to, $from, $to));
        foreach ($query->result() as $row)
        {
            if(is_null($row->age)) continue;
            foreach($list as $k=>$v){
                if(in_array($row->age, $v)){
                    $data[$k]['m'] += round($row->gender_m_count*100/$row->all_count, 2);
                    $data[$k]['f'] += round($row->gender_f_count*100/$row->all_count, 2);
                }
            }
        }

        foreach($list as $k=>$v){
            $i = $k+1;
            $output[$i][0] = (string)((count($v)>1)?$v[0]."~".$v[count($v)-1]:"{$v[0]}");
            $output[$i][1] = round($data[$k]['m'], 2);
            $output[$i][2] = round($data[$k]['f'], 2);
        }

        return $output;    
        /*
        //---- dummy data
        $dateNum = floor((strtotime($to)-strtotime($from))/86400)+1;
        $output = array(array('연령' , '남비율', '여비율'));
        if($dateNum>0){
            $AxisX = array("0", "1~10", "11~15", "16~19", "20~22", "23~25", "26~28", "29~30", "31~40", "41~");
            $i = 1;
            foreach($AxisX as $text){
                $normal = array("11~15", "16~19", "26~28", "29~30", "31~40", "41~");
                $large = array("20~22", "23~25", "26~28");
                if(in_array($text, $normal)){
                    $num1  = rand(30, 70);
                    $num2  = 100-$num1;
                } else if(in_array($text, $large)){
                    $num1  = rand(30, 70);
                    $num2  = 100-$num1;
                } else{
                    $num1  = 0;
                    $num2  = 0;
                }
                $output[$i] = array($text, $num1, $num2);
                $i++;
            }
        }
        return $output;*/
    }

    /**
     * Get User active data
     *
     * @param string $from, string $to
     * @return array
     */
    function get_user_active($from=null, $to=null)
    {
        if($to  ==null) $to  =date('Y-m-d');
        if($from==null) $from=date('Y-m-d', strtotime("-1 week", strtotime($to)));

        $printYear = ((int)(substr($from, 0, 4))<(int)(date('Y')))?true:false;

        $period = new DatePeriod(
             new DateTime($from),
             new DateInterval('P1D'),
             new DateTime($to)
        );

        $output = array(array('날짜' , '회원'));
        $data = array();

        $sql = "SELECT 
                    date_format(date, '%Y-%m-%d') as date,
                    logged_in_month
                from
                    log_user_actives
                WHERE date between ? and ?
                group by date"; 
        $query = $this->db->query($sql, array($from, $to));
        foreach ($query->result() as $row)
        {
            $data[$row->date] = round($row->logged_in_month);
            $i++;
        }

        $i = 1;
        foreach ($period as $date) {
            $output[$i] = array($date->format((($printYear)?'Y년 ':'').' m월 d일'), isset($data[$date->format('Y-m-d')])?$data[$date->format('Y-m-d')]:0);
            $i++;
        }

        return $output;
        /*
        //---- dummy data
        $dateNum = floor((strtotime($to)-strtotime($from))/86400)+1;
        $output = array(array('날짜' , '회원'));
        if($dateNum>0){
            for($i=1;$i<=$dateNum;$i++){
                $date = date("m월 d일",strtotime(($i-$dateNum)." day", strtotime($to)));
                $num  = rand(800, 1500);
                $output[$i] = array($date, $num);
            }
        }
        return $output;*/
    }

    /**
     * Get User's last login data
     *
     * @param string $from, string $to
     * @return array
     */
    function get_user_last_login($from=null, $to=null)
    {
        if($to  ==null) $to  =date('Y-m-d');
        if($from==null) $from=date('Y-m-d', strtotime("-1 week", strtotime($to)));

        $printYear = ((int)(substr($from, 0, 4))<(int)(date('Y')))?true:false;

        $period = new DatePeriod(
             new DateTime($from),
             new DateInterval('P1D'),
             new DateTime($to)
        );

        $output = array(array('날짜' , '회원'));
        $data = array();

        $sql = "SELECT 
                    date_format(last_login, '%Y-%m-%d') as date,
                        count(id) as count
                from
                    users
                WHERE last_login between ? and ?
                group by date"; 
        $query = $this->db->query($sql, array($from, $to));
        foreach ($query->result() as $row)
        {
            $data[$row->date] = round($row->count);
            $i++;
        }

        $i = 1;
        foreach ($period as $date) {
            $output[$i] = array($date->format((($printYear)?'Y년 ':'').' m월 d일'), isset($data[$date->format('Y-m-d')])?$data[$date->format('Y-m-d')]:0);
            $i++;
        }

        return $output;
    }

//-- work

    /**
     * Get work view count data
     *
     * @param string $from, string $to
     * @return array
     */
    function get_work_view_count($from=null, $to=null)
    {
        if($to  ==null) $to  =date('Y-m-d');
        if($from==null) $from=date('Y-m-d', strtotime("-1 week", strtotime($to)));
        
        $printYear = ((int)(substr($from, 0, 4))<(int)(date('Y')))?true:false;

        $period = new DatePeriod(
             new DateTime($from),
             new DateInterval('P1D'),
             new DateTime($to)
        );

        $sql = "SELECT count(id) as all_count from log_work_view WHERE regdate>?"; 
        $query = $this->db->query($sql, array($from));
        $all_count = 0;
        foreach ($query->result() as $row)
        {
            $all_count = round($row->all_count);   
            $all_first_count = round($row->all_count); 
        }

        $output = array(array('날짜' , '모든 작품 총 조회수', '오늘 받은 총 조회수', '오늘 받은 평균 조회수'));
        $data = array();
        
        $sql = "SELECT 
                date_format(regdate, '%Y-%m-%d') as date,
                count(id) as log_count,
                count(distinct work_id) as work_count
            from
                log_work_view
            where
                regdate between ? and ?
            group by date"; 
        $query = $this->db->query($sql, array($from, $to));
        foreach ($query->result() as $row)
        {
            $data[$row->date] = array(round(round($all_count=$all_count+$row->log_count)/10, 2), round($row->log_count), round($row->log_count/$row->work_count, 2));
        }

        $i = 1;
        foreach ($period as $date) {
            $output[$i] = array($date->format((($printYear)?'Y년 ':'').' m월 d일'), isset($data[$date->format('Y-m-d')][0])?$data[$date->format('Y-m-d')][0]:(($i-1==0)?$all_first_count:$output[$i-1][1]), isset($data[$date->format('Y-m-d')][1])?$data[$date->format('Y-m-d')][1]:0, isset($data[$date->format('Y-m-d')][2])?$data[$date->format('Y-m-d')][2]:0);
            $i++;
        }

        return $output;
        /*
        //---- dummy data
        $dateNum = floor((strtotime($to)-strtotime($from))/86400)+1;
        $output = array(array('날짜' , '모든작품 총 조회수', '오늘작품 총 조회수', '오늘작품 평균 조회수'));
        if($dateNum>0){
            for($i=1;$i<=$dateNum;$i++){
                $date = date("m월 d일",strtotime(($i-$dateNum)." day", strtotime($to)));
                $num1  = rand(200+$num1, 700+$num1);
                $num2  = rand(0, 300);
                $num3  = $num2/rand(1, 300);
                $output[$i] = array($date, $num1, $num2, $num3);
            }
        }
        return $output;*/
    }

    /**
     * Get work note count data
     *
     * @param string $from, string $to
     * @return array
     */
    function get_work_note_count($from=null, $to=null)
    {
        if($to  ==null) $to  =date('Y-m-d');
        if($from==null) $from=date('Y-m-d', strtotime("-1 week", strtotime($to)));
        
        $printYear = ((int)(substr($from, 0, 4))<(int)(date('Y')))?true:false;

        $period = new DatePeriod(
             new DateTime($from),
             new DateInterval('P1D'),
             new DateTime($to)
        );

        $sql = "SELECT count(id) as all_count from log_work_note where regdate>?"; 
        $query = $this->db->query($sql, array($from));
        $all_count = 0;
        foreach ($query->result() as $row)
        {
            $all_count = round($row->all_count);   
            $all_first_count = round($row->all_count);   
        }

        $output = array(array('날짜' , '모든 작품 총 추천수', '오늘 받은 총 추천수', '오늘 받은 평균 추천수'));
        $data = array();
        
        $sql = "SELECT 
                date_format(regdate, '%Y-%m-%d') as date,
                count(id) as log_count,
                count(distinct work_id) as work_count
            from
                log_work_note
            where
                regdate between ? and ?
            group by date"; 
        $query = $this->db->query($sql, array('N', $from, $to));
        foreach ($query->result() as $row)
        {
            $data[$row->date] = array(round(round($all_count=$all_count+$row->log_count)/10, 2), round($row->log_count), round($row->log_count/$row->work_count, 2));
        }

        $i = 1;
        foreach ($period as $date) {
            $output[$i] = array($date->format((($printYear)?'Y년 ':'').' m월 d일'), isset($data[$date->format('Y-m-d')][0])?$data[$date->format('Y-m-d')][0]:(($i-1==0)?$all_first_count:$output[$i-1][1]), isset($data[$date->format('Y-m-d')][1])?$data[$date->format('Y-m-d')][1]:0, isset($data[$date->format('Y-m-d')][2])?$data[$date->format('Y-m-d')][2]:0);
            $i++;
        }

        return $output;
        /*
        //---- dummy data
        $dateNum = floor((strtotime($to)-strtotime($from))/86400)+1;
        $output = array(array('날짜' , '모든작품 총 추천수', '오늘작품 총 추천수', '오늘작품 평균 추천수'));
        if($dateNum>0){
            for($i=1;$i<=$dateNum;$i++){
                $date = date("m월 d일",strtotime(($i-$dateNum)." day", strtotime($to)));
                $num1  = rand(200+$num1, 700+$num1);
                $num2  = rand(0, 300);
                $num3  = $num2/rand(1, 300);
                $output[$i] = array($date, $num1, $num2, $num3);
            }
        }
        return $output;*/
    }

    /**
     * Get work comment count data
     *
     * @param string $from, string $to
     * @return array
     */
    function get_work_comment_count($from=null, $to=null)
    {
        if($to  ==null) $to  =date('Y-m-d');
        if($from==null) $from=date('Y-m-d', strtotime("-1 week", strtotime($to)));
        
        $printYear = ((int)(substr($from, 0, 4))<(int)(date('Y')))?true:false;

        $period = new DatePeriod(
             new DateTime($from),
             new DateInterval('P1D'),
             new DateTime($to)
        );

        $sql = "SELECT count(id) as all_count from work_comments where regdate>?"; 
        $query = $this->db->query($sql, array($from));
        $all_count = 0;
        foreach ($query->result() as $row)
        {
            $all_count = round($row->all_count);   
            $all_first_count = round($row->all_count);
        }

        $output = array(array('날짜' , '모든작품 총 댓글수', '오늘 받은 총 댓글수', '오늘 받은 평균 댓글수'));
        $data = array();
        
        $sql = "SELECT 
                    date_format(regdate, '%Y-%m-%d') as date,
                    count(id) as log_count,
                    count(distinct work_id) as work_count
                from
                    work_comments
                where
                    regdate between ? and ?
                group by date"; 
        $query = $this->db->query($sql, array($from, $to));
        foreach ($query->result() as $row)
        {
            $data[$row->date] = array(round(round($all_count=$all_count+$row->log_count)/10, 2), round($row->log_count), round($row->log_count/$row->work_count, 2));
        }

        $i = 1;
        foreach ($period as $date) {
            $output[$i] = array($date->format((($printYear)?'Y년 ':'').' m월 d일'), isset($data[$date->format('Y-m-d')][0])?$data[$date->format('Y-m-d')][0]:(($i-1==0)?$all_first_count:$output[$i-1][1]), isset($data[$date->format('Y-m-d')][1])?$data[$date->format('Y-m-d')][1]:0, isset($data[$date->format('Y-m-d')][2])?$data[$date->format('Y-m-d')][2]:0);
            $i++;
        }

        return $output;
        /*
        //---- dummy data
        $dateNum = floor((strtotime($to)-strtotime($from))/86400)+1;
        $output = array(array('날짜' , '모든작품 총 댓글수', '오늘작품 총 댓글수', '오늘작품 평균 댓글수'));
        if($dateNum>0){
            for($i=1;$i<=$dateNum;$i++){
                $date = date("m월 d일",strtotime(($i-$dateNum)." day", strtotime($to)));
                $num1  = rand(200+$num1, 700+$num1);
                $num2  = rand(0, 300);
                $num3  = $num2/rand(1, 300);
                $output[$i] = array($date, $num1, $num2, $num3);
            }
        }
        return $output;
        */
    }

    /**
     * Get work Upload user count/work count data
     *
     * @param string $from, string $to
     * @return array
     */
    function get_work_upload_user_work($from=null, $to=null)
    {
        if($to  ==null) $to  =date('Y-m-d');
        if($from==null) $from=date('Y-m-d', strtotime("-1 week", strtotime($to)));
        
        $printYear = ((int)(substr($from, 0, 4))<(int)(date('Y')))?true:false;

        $period = new DatePeriod(
             new DateTime($from),
             new DateInterval('P1D'),
             new DateTime($to)
        );

        $output = array(array('날짜' , '올린 사람', '작품'));
        $data = array();

        $sql = "SELECT
                    date, user_count, work_count
                from
                    (select 
                        date_format(regdate, '%Y-%m-%d') as date,
                            count(distinct user_id) as user_count,
                            count(id) as work_count
                    from
                        works
                    WHERE regdate >= ? and regdate <= ?
                    group by date) work"; 
        $query = $this->db->query($sql, array($from, $to));
        foreach ($query->result() as $row)
        {
            $data[$row->date] = array(round($row->user_count), round($row->work_count));
        }

        $i = 1;
        foreach ($period as $date) {
            $output[$i] = array($date->format((($printYear)?'Y년 ':'').' m월 d일'), isset($data[$date->format('Y-m-d')][0])?$data[$date->format('Y-m-d')][0]:0, isset($data[$date->format('Y-m-d')][1])?$data[$date->format('Y-m-d')][1]:0);
            $i++;
        }

        return $output;
        /*
        //---- dummy data
        $dateNum = floor((strtotime($to)-strtotime($from))/86400)+1;
        $output = array(array('날짜' , '올린 사람', '작품'));
        if($dateNum>0){
            for($i=1;$i<=$dateNum;$i++){
                $date = date("m월 d일",strtotime(($i-$dateNum)." day", strtotime($to)));
                $num1  = rand(80, 150);
                $num2  = rand(200, 1000);
                $output[$i] = array($date, $num1, $num2);
            }
        }
        return $output;*/
    }

//-- stat works

    /**
     * Get work view count by month data
     *
     * @param string $from, string $to
     * @return array
     */
    function get_work_view_month_count($from=null, $to=null)
    {
        if($to  ==null) $to  =date('Y-m-d');
        if($from==null) $from=date('Y-m-01', strtotime("-3 month", strtotime($to)));
        
        //total works note count by month
        $sql = "SELECT date, view_count, work_count 
                    FROM (SELECT DATE_FORMAT(regdate, '%Y년 %m월') as date, count(id) as view_count 
                        FROM log_work_view WHERE regdate >= ? and regdate <= ? group by date) views 
                    left join (SELECT DATE_FORMAT(regdate, '%Y년 %m월') as work_date, count(id) as work_count 
                        FROM works WHERE regdate >= ? and regdate <= ? group by work_date) works on views.date=works.work_date;"; 
        $query = $this->db->query($sql, array($from, $to, $from, $to));
        
        $output = array(array('날짜' , '총 조회수', '평균 조회수', '월간 업로드'));
        $i=1;
        foreach ($query->result() as $row)
        {
            $output[$i] = array($row->date, round($row->view_count/100,3), round($row->view_count/$row->work_count,2), round($row->work_count,0));
            $i++;
        }

        return $output;
        /*
        //---- dummy data
        $monthNum = floor((strtotime($to)-strtotime($from))/86400/30)+1;
        $output = array(array('날짜' , '총 조회수', '평균 조회수'));
        if($monthNum>0){
            for($i=1;$i<=$monthNum;$i++){
                $date = date("Y년 m월",strtotime(($i-$monthNum)." month", strtotime($to)));
                $num1  = rand(2000, 7000);
                $num2  = $num1/rand(1, $num1);
                $output[$i] = array($date, $num1, $num2);
            }
        }
        return $output;*/
    }

    /**
     * Get work note count by month data
     *
     * @param string $from, string $to
     * @return array
     */
    function get_work_note_month_count($from=null, $to=null)
    {
        if($to  ==null) $to  =date('Y-m-d');
        if($from==null) $from=date('Y-m-01', strtotime("-3 month", strtotime($to)));
        
        //total works note count by month
        $sql = "SELECT date, note_count, work_count 
                    FROM (SELECT DATE_FORMAT(regdate, '%Y년 %m월') as date, count(id) as note_count 
                        FROM log_work_note WHERE regdate >= ? and regdate <= ? group by date) notes 
                    left join (SELECT DATE_FORMAT(regdate, '%Y년 %m월') as work_date, count(id) as work_count 
                        FROM works WHERE regdate >= ? and regdate <= ? group by work_date) works on notes.date=works.work_date;"; 
        $query = $this->db->query($sql, array($from, $to, $from, $to));
        
        $output = array(array('날짜' , '총 추천수', '평균 추천수', '월간 업로드'));
        $i=1;
        foreach ($query->result() as $row)
        {
            $output[$i] = array($row->date, round($row->note_count,0), round($row->note_count/$row->work_count,2), round($row->work_count,0));
            $i++;
        }

        return $output;
        /*
        //---- dummy data
        $monthNum = floor((strtotime($to)-strtotime($from))/86400/30)+1;
        $output = array(array('날짜' , '총 추천수', '평균 추천수'));
        if($monthNum>0){
            for($i=1;$i<=$monthNum;$i++){
                $date = date("Y년 m월",strtotime(($i-$monthNum)." month", strtotime($to)));
                $num1  = rand(2000, 7000);
                $num2  = $num1/rand(1, $num1);
                $output[$i] = array($date, $num1, $num2);
            }
        }
        return $output;*/
    }

    /**
     * Get work comment count by month data
     *
     * @param string $from, string $to
     * @return array
     */
    function get_work_comment_month_count($from=null, $to=null)
    {
        if($to  ==null) $to  =date('Y-m-d');
        if($from==null) $from=date('Y-m-01', strtotime("-3 month", strtotime($to)));
        
        // "SELECT date, comment_count, work_count FROM (SELECT DATE_FORMAT(regdate, '%Y-%m') as date, count(id) as comment_count FROM work_comments group by date) comments left join (SELECT DATE_FORMAT(regdate, '%Y-%m') as work_date, count(id) as work_count FROM works group by work_date) works on comments.date=works.work_date;"
        //total works comment count by month
        $sql = "SELECT date, comment_count, work_count 
                    FROM (SELECT DATE_FORMAT(regdate, '%Y년 %m월') as date, count(id) as comment_count 
                        FROM work_comments WHERE regdate >= ? and regdate <= ? group by date) comments 
                    left join (SELECT DATE_FORMAT(regdate, '%Y년 %m월') as work_date, count(id) as work_count 
                        FROM works WHERE regdate >= ? and regdate <= ? group by work_date) works on comments.date=works.work_date;"; 
        $query = $this->db->query($sql, array($from, $to, $from, $to));
        
        $output = array(array('날짜' , '총 댓글수', '평균 댓글수', '월간 업로드'));
        $i=1;
        foreach ($query->result() as $row)
        {
            $output[$i] = array($row->date, round($row->comment_count,0), round($row->comment_count/$row->work_count,2), round($row->work_count,0));
            $i++;
        }

        return $output;
        /*
        //---- dummy data
        $monthNum = floor((strtotime($to)-strtotime($from))/86400/30)+1;
        $output = array(array('날짜' , '총 댓글수', '평균 댓글수'));
        if($monthNum>0){
            for($i=1;$i<=$monthNum;$i++){
                $date = date("Y년 m월",strtotime(($i-$monthNum)." month", strtotime($to)));
                $num1  = rand(2000, 7000);
                $num2  = $num1/rand(1, $num1);
                $output[$i] = array($date, $num1, $num2);
            }
        }
        return $output;*/
    }

    /**
     * Get work keyword usage data
     *
     * @param int $num
     * @return array
     */
    function get_work_keyword_usage($count=10)
    {
        $output = array(array('키워드' , '선택수'));

        $i = 1;
        //work category
        $sql = "SELECT category, count(id) as count, all_count FROM work_categories
                join (select count(id) as all_count from work_categories) allCount
                group by category order by count desc limit 0, ?"; 
        $query = $this->db->query($sql, array($count));
        foreach ($query->result() as $row)
        {
            $output[$i] = array($this->notefolio->print_keywords(array($row->category), FALSE), $row->count,);
            $i++;
        }

        return $output;
    }

    /**
     * Get user keyword usage data
     *
     * @param int $count
     * @return array
     */
    function get_user_keyword_usage($count=10)
    {
        $output = array(array('키워드' , '선택수'));

        $i = 1;
        //user category
        $sql = "SELECT category, count(id) as count, all_count FROM user_categories
                join (select count(id) as all_count from user_categories) allCount
                group by category order by count desc limit 0, ?"; 
        $query = $this->db->query($sql, array($count));
        foreach ($query->result() as $row)
        {
            $output[$i] = array($this->notefolio->print_keywords(array($row->category), FALSE), $row->count,);
            $i++;
        }

        return $output;
    }

    /**
     * Get period of first Work upload per user data
     *
     * @param null
     * @return array
     */
    function get_work_first_upload()
    {
        $list = array(
            array(0),
            array(1),
            array(2),
            array(3),
            array(4),
            array(5),
            array(6),
            array(7),
            array(8),
            array(9),
            range(10, 29),
            range(30, 49),
            range(50, 79),
            range(80, 99),
            range(100, 119),
            range(120, 149),
            range(150, 249),
            range(250, 10000)
            );

        //select DATE_FORMAT(CURRENT_DATE, '%Y')-DATE_FORMAT(birth, '%Y')+1 as age, SUM(case when gender = 'm' then 1 else 0 end) as gender_m_count, SUM(case when gender = 'f' then 1 else 0 end) as gender_f_count, count(user_id) as user_count, all_count from users left join user_profiles on users.id = user_profiles.user_id join (select count(id) as all_count from users where activated=1) all_user group by age
        $output = array('data'=>array(array('기간' , '회원 수')), 'avg'=>0);
        $data = array();

        //query
        $sql = "SELECT 
                    uploaded, count(uploaded) as count
                from
                    (select 
                        dateDIFF(work.regdate, created) as uploaded
                    from
                        users
                    left join (select 
                        user_id, id as work_id, regdate
                    from
                        works
                    group by user_id
                    order by user_id asc , regdate asc) work ON users.id = work.user_id) u
                where
                    uploaded is not null
                group by uploaded"; 
        $query = $this->db->query($sql, array());
        foreach ($query->result() as $row)
        {
            foreach($list as $k=>$v){
                if(in_array($row->uploaded, $v)){
                    $data[$k] += round($row->count, 0);
                    $output['avg'] += round($row->uploaded, 0) * round($row->count, 0);
                }
            }
        }

        $i = 0;
        foreach($list as $k=>$v){
            $i = $k+1;
            $output['data'][$i][0] = (string)(($v[0]==0)?"당일":"{$v[0]}");
            $output['data'][$i][1] = round($data[$k], 0);
        }

        $output['avg'] = round($output['avg'] / array_sum($data), 2);

        return $output;
        /*
        //---- dummy data
        $output = array(array('기간' , '회원 수'));

        $AxisX = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "10",
                       "30", "50", "80", "100", "120", "150", "250", "기타");
        $i = 1;
        foreach($AxisX as $text){
            $normal = array("4", "5", "6", "7", "8", "9", "10",
                            "80", "100", "120", "150", "250", "기타");
            $large = array("1", "2", "3", "30", "50");
            if(in_array($text, $normal)){
                $num = rand(0, 50);
            } else if(in_array($text, $large)){
                $num = rand(100, 400);
            } else{
                $num = 0;
            }
            $output[$i] = array($text, $num);
            $i++;
        }
        
        return $output;*/
    }

    /**
     * Get period in second work and first Work upload per user data
     *
     * @param null
     * @return array
     */
    function get_work_second_upload()
    {
        $list = array(
            array(0),
            array(1),
            array(2),
            array(3),
            array(4),
            array(5),
            array(6),
            array(7),
            array(8),
            array(9),
            range(10, 29),
            range(30, 49),
            range(50, 79),
            range(80, 99),
            range(100, 119),
            range(120, 149),
            range(150, 249),
            range(250, 10000)
            );

        //select DATE_FORMAT(CURRENT_DATE, '%Y')-DATE_FORMAT(birth, '%Y')+1 as age, SUM(case when gender = 'm' then 1 else 0 end) as gender_m_count, SUM(case when gender = 'f' then 1 else 0 end) as gender_f_count, count(user_id) as user_count, all_count from users left join user_profiles on users.id = user_profiles.user_id join (select count(id) as all_count from users where activated=1) all_user group by age
        $output = array('data'=>array(array('기간' , '회원 수')), 'avg'=>0);
        $data = array();

        //query
        $sql = "SELECT gap, count(gap) as count
                from
                (select 
                    dateDIFF(work_second.regdate, work_first.regdate) as gap
                from
                    (
                        select 
                            user_id, regdate
                        from
                            works
                        group by user_id
                        order by user_id asc , regdate asc
                    ) work_first
                    left join (
                        select works.user_id, works.regdate as regdate
                        from
                            works,
                            (select 
                                user_id, id as work_id_first, regdate
                            from
                                works
                            group by user_id
                            order by user_id asc , regdate asc) work_first
                        where
                            works.user_id = work_first.user_id
                            and
                            works.id != work_first.work_id_first
                        group by works.user_id
                    ) work_second ON work_first.user_id = work_second.user_id
                order by gap
                ) u
                where gap is not null
                group by gap"; 
        $query = $this->db->query($sql, array());
        foreach ($query->result() as $row)
        {
            foreach($list as $k=>$v){
                if(in_array($row->gap, $v)){
                    $data[$k] += round($row->count, 0);
                    $output['avg'] += round($row->gap, 0) * round($row->count, 0);
                }
            }
        }

        $i = 0;
        foreach($list as $k=>$v){
            $i = $k+1;
            $output['data'][$i][0] = (string)(($v[0]==0)?"당일":"{$v[0]}");
            $output['data'][$i][1] = round($data[$k], 0);
        }

        $output['avg'] = round($output['avg'] / array_sum($data), 2);

        return $output;
        /*
        //---- dummy data
        $output = array(array('기간' , '회원 수'));

        $AxisX = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "10",
                       "30", "50", "80", "100", "120", "150", "250", "기타");
        $i = 1;
        foreach($AxisX as $text){
            $normal = array("4", "5", "6", "7", "8", "9", "10",
                            "80", "100", "120", "150", "250", "기타");
            $large = array("1", "2", "3", "30", "50");
            if(in_array($text, $normal)){
                $num = rand(0, 50);
            } else if(in_array($text, $large)){
                $num = rand(100, 400);
            } else{
                $num = 0;
            }
            $output[$i] = array($text, $num);
            $i++;
        }
        
        return $output; */
    }

    /**
     * Get Work upload per user data
     *
     * @param null
     * @return array
     */
    function get_work_per_user()
    {
        $list = array(
            array(1),
            range(2, 5),
            range(6, 10),
            range(11, 15),
            range(16, 20),
            range(21, 25),
            range(26, 30),
            range(31, 35),
            range(36, 40),
            range(41, 45),
            range(46, 50),
            range(51, 100),
            range(101, 10000)
            );

        //select user_count as works, count(user_count) as count from (SELECT count(id) as user_count from works group by user_id) o group by works order by works asc 
        $output = array(array('작품 수' , '회원 수'));
        $data = array();

        $i = 1;
        //query
        $sql = "SELECT  user_count as works, 
                        count(user_count) as count 
                    from (SELECT count(id) as user_count from works group by user_id) o 
                    group by works order by works asc "; 
        $query = $this->db->query($sql, array());
        foreach ($query->result() as $row)
        {
            if(empty($row->works))
                continue;
            else {
                foreach($list as $k=>$v){
                    if(in_array($row->works, $v)){
                        $data[$k] += round($row->count, 0);
                    }
                }
            }
        }

        foreach($list as $k=>$v){
            $i = $k+1;
            $output[$i][0] = (string)((count($v)>1)?$v[0]."~".$v[count($v)-1]:"{$v[0]}");
            $output[$i][1] = round($data[$k], 0);
        }

        return $output;
        /*
        //---- dummy data
        $output = array(array('작품 수' , '회원 수'));

        $AxisX = array("0", "1", "2~5", "6~10", "11~15", "16~20", "21~25", "26~30", "31~35", "36~40", "41~45", "46~50", "51~100", "101~");
        $i = 1;
        foreach($AxisX as $text){
            $normal = array("0", "16~20", "21~25", "26~30", "31~35", "36~40", "41~45", "46~50", "51~100", "101~");
            $large = array("1", "2~5", "6~10", "11~15");
            if(in_array($text, $normal)){
                $num = rand(0, 50);
            } else if(in_array($text, $large)){
                $num = rand(100, 400);
            } else{
                $num = 0;
            }
            $output[$i] = array($text, $num);
            $i++;
        }

        return $output;*/
    }

    /**
     * Get User's gender percentage by age Total data
     *
     * @param null
     * @return array
     */
    function get_user_gender_age_total()
    {
        $list = array(
            range(0, 9),
            range(10, 14),
            range(15, 18),
            range(19, 21),
            range(22, 24),
            range(25, 27),
            range(28, 29),
            range(30, 39),
            range(40, 200)
            );

        //select DATE_FORMAT(CURRENT_DATE, '%Y')-DATE_FORMAT(birth, '%Y')+1 as age, SUM(case when gender = 'm' then 1 else 0 end) as gender_m_count, SUM(case when gender = 'f' then 1 else 0 end) as gender_f_count, count(user_id) as user_count, all_count from users left join user_profiles on users.id = user_profiles.user_id join (select count(id) as all_count from users where activated=1) all_user group by age
        $output = array(array('연령' , '남비율', '여비율'));
        $data = array();

        $i = 1;
        //query
        $sql = "SELECT  DATE_FORMAT(CURRENT_DATE, '%Y')-DATE_FORMAT(birth, '%Y')+1 as age,
                        SUM(case when gender = 'm' then 1 else 0 end) as gender_m_count, 
                        SUM(case when gender = 'f' then 1 else 0 end) as gender_f_count, 
                        count(user_id) as user_count, all_count 
                    from users 
                    left join user_profiles on users.id = user_profiles.user_id 
                    join (select count(id) as all_count from users where activated=?) all_user 
                    group by age"; 
        $query = $this->db->query($sql, array(1));
        foreach ($query->result() as $row)
        {
            if(empty($row->age))
                continue;
            else {
                foreach($list as $k=>$v){
                    if(in_array($row->age, $v)){
                        $data[$k]['m'] += round($row->gender_m_count*100/$row->all_count, 2);
                        $data[$k]['f'] += round($row->gender_f_count*100/$row->all_count, 2);
                    }
                }
            }
        }

        foreach($list as $k=>$v){
            $i = $k+1;
            $output[$i][0] = (string)$v[0];
            $output[$i][1] = round($data[$k]['m'], 2);
            $output[$i][2] = round($data[$k]['f'], 2);
        }

        return $output;
        /*
        //---- dummy data
        $output = array(array('연령' , '남비율', '여비율'));

        $AxisX = array("0", "10", "15", "19", "22", "25", "28", "30", "40", "41~");
        $i = 1;
        foreach($AxisX as $text){
            $normal = array("15", "19", "28", "30", "40", "41~");
            $large = array("22", "25", "28");
            if(in_array($text, $normal)){
                $num1  = rand(30, 70);
                $num2  = 100-$num1;
            } else if(in_array($text, $large)){
                $num1  = rand(30, 70);
                $num2  = 100-$num1;
            } else{
                $num1  = 0;
                $num2  = 0;
            }
            $output[$i] = array($text, $num1, $num2);
            $i++;
        }

        return $output;*/
    }

    /**
     * Get User's Upload rank data
     *
     * @param int $count
     * @return array
     */
    function get_upload_user_rank($count=100)
    {

        //count for user upload
        $sql = "SELECT count(id) as count FROM users left join
                    (SELECT user_id, ifnull(count(id),0) as work_count FROM works
                     group by user_id) works
                    on users.id = works.user_id
                    WHERE work_count>? "; 
        $query = $this->db->query($sql, array($count));
        foreach ($query->result() as $row)
        {
            $userCount = $row->count;
        }
        
        $output = array('data'=>array(), 'count'=>$userCount);

        $i = 0;
        //user upload
        $sql = "SELECT id, username, realname, created, work_count FROM users left join
                    (SELECT user_id, ifnull(count(id),0) as work_count FROM works
                     group by user_id) works
                    on users.id = works.user_id
                    left join (SELECT user_id, realname FROM user_profiles) profile
                    on users.id = profile.user_id
                    WHERE work_count>=? order by work_count desc"; 
        $query = $this->db->query($sql, array($count));
        foreach ($query->result() as $row)
        {
            $output['data'][$i] = array(
                                'id'=>$row->id,
                                'username'=>$row->username,
                                'realname'=>$row->realname,
                                'regdate'=>$row->created,
                                'count'=>$row->work_count);
            $i++;
        }
       
        return $output;
        /*
        //---- dummy data
        $lineCount = 40;
        $output = array('data'=>array(), 'count'=>1234);
        if($lineCount>0){
            for($i=1;$i<=$lineCount;$i++){
                $id = $i;
                $username  = "user".$i;
                $realname  = "사용자".$i;
                $regdate  = strtotime(rand(-100, 0)+' days', time());
                $count  = rand(0, 100);
                $output['data'][$i] = array(
                                    'id'=>$id,
                                    'username'=>$username,
                                    'realname'=>$realname,
                                    'regdate'=>$regdate,
                                    'count'=>$count);
            }
        }
        return $output;*/
    }

    /**
     * Get User's View rank data
     *
     * @param int $count
     * @return array
     */
    function get_view_user_rank($count=100)
    {

        //count for user upload
        $sql = "SELECT count(id) as count FROM works left join
                    (SELECT work_id, hit_cnt as view_count FROM work_counts) work_views
                    on works.id = work_views.work_id

                    WHERE view_count>? "; 
        $query = $this->db->query($sql, array($count));
        foreach ($query->result() as $row)
        {
            $userCount = $row->count;
        }
        
        $output = array('data'=>array(), 'count'=>$userCount);

        $i = 0;
        //user upload
        $sql = "SELECT id, title, users.user_id, username, realname, regdate, view_count FROM works left join
                    (SELECT work_id, hit_cnt as view_count FROM work_counts) work_views
                    on works.id = work_views.work_id left join
                    (SELECT id as user_id, username FROM users
                     group by user_id) users
                    on works.user_id = users.user_id 
                    left join (SELECT user_id, realname FROM user_profiles) profile
                    on works.user_id = profile.user_id
                    WHERE view_count>=? order by view_count desc"; 
        $query = $this->db->query($sql, array($count));
        foreach ($query->result() as $row)
        {
            $output['data'][$i] = array(
                                'id'=>$row->id,
                                'user_id'=>$row->user_id,
                                'title'=>$row->title,
                                'realname'=>$row->realname,
                                'regdate'=>$row->regdate,
                                'count'=>$row->view_count);
            $i++;
        }
       
        return $output;

        /*
        //---- dummy data
        $lineCount = 40;
        $output = array('data'=>array(), 'count'=>1234);
        if($lineCount>0){
            for($i=1;$i<=$lineCount;$i++){
                $id = $i;
                $username  = "user".$i;
                $realname  = "사용자".$i;
                $regdate  = strtotime(rand(-100, 0)+' days', time());
                $count  = rand(0, 100);
                $output['data'][$i] = array(
                                    'id'=>$id,
                                    'username'=>$username,
                                    'realname'=>$realname,
                                    'regdate'=>$regdate,
                                    'count'=>$count);
            }
        }
        return $output;*/
    }

    /**
     * Get work keyword rank data
     *
     * @param array $opt
     * @return array
     */
    function get_work_keyword_rank($opt=array())
    {
        $lineCount = 40;
        $output = array('data'=>array());

        $i = 1;
        //work category
        $sql = "SELECT category, count(id) as count, all_count FROM work_categories
                join (select count(id) as all_count from work_categories) allCount
                group by category order by count desc limit 0, ?"; 
        $query = $this->db->query($sql, array($lineCount));
        foreach ($query->result() as $row)
        {
            $output['data'][$i] = array('name'=>$this->notefolio->print_keywords(array($row->category), FALSE), 'count'=>$row->count, 'percent'=>round($row->count*100/$row->all_count,2)."%");
            $i++;
        }

        return $output;
    }

    /**
     * Get user keyword rank data
     *
     * @param array $opt
     * @return array
     */
    function get_user_keyword_rank($opt=array())
    {
        $lineCount = 40;
        $output = array('data'=>array());

        $i = 1;
        //user category
        $sql = "SELECT category, count(id) as count, all_count FROM user_categories
                join (select count(id) as all_count from user_categories) allCount
                group by category order by count desc limit 0, ?"; 
        $query = $this->db->query($sql, array($lineCount));
        foreach ($query->result() as $row)
        {
            $output['data'][$i] = array('name'=>$this->notefolio->print_keywords(array($row->category), FALSE), 'count'=>$row->count, 'percent'=>round($row->count*100/$row->all_count,2)."%");
            $i++;
        }

        return $output;
    }

    /**
     * Get total data
     *
     * @param string $from, string $to
     * @return array
     */
    function get_total_data($from=null, $to=null)
    {
        $output = array();
        
        /*
        $sql = "SELECT * FROM some_table WHERE id = ? AND status = ? AND author = ?"; 
        $this->db->query($sql, array(3, 'live', 'Rick'));
        */

        //total user
        $sql = "SELECT count(id) as count FROM users WHERE activated = ? and created >= ? and created <= ?"; 
        $query = $this->db->query($sql, array(1, $from, $to));
        foreach ($query->result() as $row)
        {
            $output['user'] = $row->count;
        }

        //total user upload
        $sql = "SELECT count(id) as count FROM users left join
                    (SELECT user_id, ifnull(count(id),0) as work_count FROM works
                     where regdate >= ? and regdate <= ? group by user_id) works
                    on users.id = works.user_id
                    WHERE work_count>? and activated = ? and created >= ? and created <= ?"; 
        $query = $this->db->query($sql, array($from, $to, 0, 1, $from, $to));
        foreach ($query->result() as $row)
        {
            $output['userUploaded'] = $row->count;
        }

        //total works
        $sql = "SELECT count(id) as count FROM works WHERE regdate >= ? and regdate <= ?"; 
        $query = $this->db->query($sql, array($from, $to));
        foreach ($query->result() as $row)
        {
            $output['works'] = $row->count;
        }
        
        return $output;
    }

    /**
     * Get total work data
     *
     * @param string $from, string $to
     * @return array
     */
    function get_total_work_data($from=null, $to=null)
    {
        $output = array();
        
        /*
        $sql = "SELECT * FROM some_table WHERE id = ? AND status = ? AND author = ?"; 
        $this->db->query($sql, array(3, 'live', 'Rick'));
        */

        //total works view count
        $sql = "SELECT count(id) as count FROM log_work_view WHERE regdate >= ? and regdate <= ?"; 
        $query = $this->db->query($sql, array($from, $to));
        foreach ($query->result() as $row)
        {
            $output['viewCount'] = $row->count;
        }

        //total works note count
        $sql = "SELECT count(id) as count FROM log_work_note WHERE regdate >= ? and regdate <= ?"; 
        $query = $this->db->query($sql, array($from, $to));
        foreach ($query->result() as $row)
        {
            $output['noteCount'] = $row->count;
        }

        //total works comment count
        $sql = "SELECT count(id) as count FROM work_comments WHERE regdate >= ? and regdate <= ?"; 
        $query = $this->db->query($sql, array($from, $to));
        foreach ($query->result() as $row)
        {
            $output['commentCount'] = $row->count;
        }

        return $output;
    }



}

/* End of file acp_statics.php */
/* Location: ./application/models/acp/acp_statics.php */