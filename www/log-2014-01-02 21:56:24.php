SELECT *
FROM (`ci_sessions`)
WHERE `session_id` =  '330185942e42270fc081ff1966fd6c4a'
AND `user_agent` =  'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36'
SELECT `work_comments`.*, `users`.`id` as user_id, `users`.`username`, `users`.`email`, `users`.`level`, `users`.`realname`, `users`.`last_ip`, `users`.`last_login`, `users`.`created`, `users`.`modified`
FROM (`work_comments`)
LEFT JOIN `users` ON `users`.`id` = `work_comments`.`user_id`
WHERE `work_id` =  '101720'
AND `parent_id` =  0
ORDER BY `regdate` desc
LIMIT 10
INSERT INTO `log_access` (`useragent`, `to_access`, `is_mobile`, `is_robot`, `referer`, `remote_addr`, `regdate`) VALUES ('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36', '/comment/get_list/101720?id_before=', 'N', 'N', 'http://notefolio_renew.localhost/toekki/101720?', '127.0.0.1', '2014-01-02 21:56:23')
===[took:0.004]

