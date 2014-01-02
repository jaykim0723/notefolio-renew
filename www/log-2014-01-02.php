SELECT *
FROM (`ci_sessions`)
WHERE `session_id` =  '86a7610b54fb63ba54182bb0e320111c'
AND `user_agent` =  'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36'
SELECT `works`.*, `users`.`id`, `users`.`username`, `users`.`email`, `users`.`level`, `users`.`realname`, `users`.`last_ip`, `users`.`last_login`, `users`.`created`, `users`.`modified`
FROM (`works`)
LEFT JOIN `users` ON `users`.`id` = `works`.`user_id`
WHERE `works`.`work_id` =  '101720'
LIMIT 1
INSERT INTO `log_access` (`useragent`, `to_access`, `is_mobile`, `is_robot`, `referer`, `remote_addr`, `regdate`) VALUES ('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36', '/toekki/101720', 'N', 'N', 'http://notefolio_renew.localhost/toekki/101720?', '127.0.0.1', '2014-01-02 22:13:27')
===[took:0.005]

SELECT *
FROM (`ci_sessions`)
WHERE `session_id` =  '86a7610b54fb63ba54182bb0e320111c'
AND `user_agent` =  'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36'
SELECT `id` as user_id
FROM (`users`)
WHERE `username` =  'data'
SELECT `users`.*
FROM (`users`)
WHERE `users`.`id` =  ''
LIMIT 1
SELECT `works`.*, `users`.`id`, `users`.`username`, `users`.`email`, `users`.`level`, `users`.`realname`, `users`.`last_ip`, `users`.`last_login`, `users`.`created`, `users`.`modified`
FROM (`works`)
LEFT JOIN `users` ON `users`.`id` = `works`.`user_id`
ORDER BY `moddate` desc
LIMIT 24
INSERT INTO `log_access` (`useragent`, `to_access`, `is_mobile`, `is_robot`, `referer`, `remote_addr`, `regdate`) VALUES ('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36', '/data/profiles/toekki.jpg?_=18', 'N', 'N', 'http://notefolio_renew.localhost/toekki/101720?', '127.0.0.1', '2014-01-02 22:13:28')
===[took:0.008]

SELECT *
FROM (`ci_sessions`)
WHERE `session_id` =  '86a7610b54fb63ba54182bb0e320111c'
AND `user_agent` =  'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36'
SELECT `id` as user_id
FROM (`users`)
WHERE `username` =  'data'
SELECT `users`.*
FROM (`users`)
WHERE `users`.`id` =  ''
LIMIT 1
SELECT `works`.*, `users`.`id`, `users`.`username`, `users`.`email`, `users`.`level`, `users`.`realname`, `users`.`last_ip`, `users`.`last_login`, `users`.`created`, `users`.`modified`
FROM (`works`)
LEFT JOIN `users` ON `users`.`id` = `works`.`user_id`
ORDER BY `moddate` desc
LIMIT 24
INSERT INTO `log_access` (`useragent`, `to_access`, `is_mobile`, `is_robot`, `referer`, `remote_addr`, `regdate`) VALUES ('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36', '/data/profiles/toekki.jpg', 'N', 'N', 'http://notefolio_renew.localhost/toekki/101720?', '127.0.0.1', '2014-01-02 22:13:28')
===[took:0.008]

SELECT *
FROM (`ci_sessions`)
WHERE `session_id` =  '86a7610b54fb63ba54182bb0e320111c'
AND `user_agent` =  'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36'
INSERT INTO `log_access` (`useragent`, `to_access`, `is_mobile`, `is_robot`, `referer`, `remote_addr`, `regdate`) VALUES ('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36', '/feed/check_unread', 'N', 'N', 'http://notefolio_renew.localhost/toekki/101720?', '127.0.0.1', '2014-01-02 22:13:29')
===[took:0.005]

SELECT *
FROM (`ci_sessions`)
WHERE `session_id` =  '86a7610b54fb63ba54182bb0e320111c'
AND `user_agent` =  'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36'
DELETE FROM `ci_sessions`
WHERE `last_activity` < 1386076410
SELECT `works`.*, `users`.`id`, `users`.`username`, `users`.`email`, `users`.`level`, `users`.`realname`, `users`.`last_ip`, `users`.`last_login`, `users`.`created`, `users`.`modified`
FROM (`works`)
LEFT JOIN `users` ON `users`.`id` = `works`.`user_id`
WHERE `works`.`work_id` =  '101719'
LIMIT 1
INSERT INTO `log_access` (`useragent`, `to_access`, `is_mobile`, `is_robot`, `referer`, `remote_addr`, `regdate`) VALUES ('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36', '/toekki/101719', 'N', 'N', 'http://notefolio_renew.localhost/toekki/101720?', '127.0.0.1', '2014-01-02 22:13:30')
===[took:0.007]

SELECT *
FROM (`ci_sessions`)
WHERE `session_id` =  '86a7610b54fb63ba54182bb0e320111c'
AND `user_agent` =  'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36'
DELETE FROM `ci_sessions`
WHERE `last_activity` < 1386076410
SELECT `id` as user_id
FROM (`users`)
WHERE `username` =  'data'
SELECT `users`.*
FROM (`users`)
WHERE `users`.`id` =  ''
LIMIT 1
SELECT `works`.*, `users`.`id`, `users`.`username`, `users`.`email`, `users`.`level`, `users`.`realname`, `users`.`last_ip`, `users`.`last_login`, `users`.`created`, `users`.`modified`
FROM (`works`)
LEFT JOIN `users` ON `users`.`id` = `works`.`user_id`
ORDER BY `moddate` desc
LIMIT 24
INSERT INTO `log_access` (`useragent`, `to_access`, `is_mobile`, `is_robot`, `referer`, `remote_addr`, `regdate`) VALUES ('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36', '/data/profiles/.jpg', 'N', 'N', 'http://notefolio_renew.localhost/toekki/101720?', '127.0.0.1', '2014-01-02 22:13:30')
===[took:0.006]

