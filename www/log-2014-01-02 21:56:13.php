SELECT *
FROM (`ci_sessions`)
WHERE `session_id` =  '163054f868903d05ef2e56428f1789b0'
AND `user_agent` =  'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36'
UPDATE `ci_sessions` SET `last_activity` = 1388667373, `session_id` = '330185942e42270fc081ff1966fd6c4a' WHERE session_id = '163054f868903d05ef2e56428f1789b0'
DELETE FROM `ci_sessions`
WHERE `last_activity` < 1386075373
SELECT `works`.*, `users`.`id`, `users`.`username`, `users`.`email`, `users`.`level`, `users`.`realname`, `users`.`last_ip`, `users`.`last_login`, `users`.`created`, `users`.`modified`
FROM (`works`)
LEFT JOIN `users` ON `users`.`id` = `works`.`user_id`
WHERE `works`.`work_id` =  '101720'
LIMIT 1
INSERT INTO `log_access` (`useragent`, `to_access`, `is_mobile`, `is_robot`, `referer`, `remote_addr`, `regdate`) VALUES ('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36', '/toekki/101720', 'N', 'N', 'http://notefolio_renew.localhost/toekki/101720?', '127.0.0.1', '2014-01-02 21:56:13')
===[took:0.005]

