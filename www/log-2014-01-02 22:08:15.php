SELECT *
FROM (`ci_sessions`)
WHERE `session_id` =  '8ef98d44c15e6aed227edc1680234b47'
AND `user_agent` =  'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36'
INSERT INTO `log_access` (`useragent`, `to_access`, `is_mobile`, `is_robot`, `referer`, `remote_addr`, `regdate`) VALUES ('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36', '/feed/check_unread', 'N', 'N', 'http://notefolio_renew.localhost/toekki/101720?', '127.0.0.1', '2014-01-02 22:08:15')
===[took:0.009]

SELECT *
FROM (`ci_sessions`)
WHERE `session_id` =  '8ef98d44c15e6aed227edc1680234b47'
AND `user_agent` =  'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36'
INSERT INTO `work_comments` (`content`, `parent_id`, `work_id`, `user_id`, `regdate`, `moddate`) VALUES ('ㅣㅏ ㅣㅏㅣㅏㅣㅏ', '', '101720', '1', '2014-01-02 22:08:15', '2014-01-02 22:08:15')
SELECT `work_comments`.*, `users`.`id` as user_id, `users`.`username`, `users`.`email`, `users`.`level`, `users`.`realname`, `users`.`last_ip`, `users`.`last_login`, `users`.`created`, `users`.`modified`
FROM (`work_comments`)
LEFT JOIN `users` ON `work_comments`.`user_id`=`users`.`id`
WHERE `work_comments`.`id` =  86
LIMIT 1
INSERT INTO `log_access` (`useragent`, `to_access`, `is_mobile`, `is_robot`, `referer`, `remote_addr`, `regdate`) VALUES ('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36', '/comment/post/101720', 'N', 'N', 'http://notefolio_renew.localhost/toekki/101720?', '127.0.0.1', '2014-01-02 22:08:15')
===[took:0.005]

