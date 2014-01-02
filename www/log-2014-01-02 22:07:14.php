SELECT *
FROM (`ci_sessions`)
WHERE `session_id` =  '8ef98d44c15e6aed227edc1680234b47'
AND `user_agent` =  'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36'
INSERT INTO `log_access` (`useragent`, `to_access`, `is_mobile`, `is_robot`, `referer`, `remote_addr`, `regdate`) VALUES ('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36', '/feed/check_unread', 'N', 'N', 'http://notefolio_renew.localhost/toekki/101720?', '127.0.0.1', '2014-01-02 22:07:14')
===[took:0.011]

