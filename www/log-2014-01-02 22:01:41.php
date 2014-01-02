SELECT *
FROM (`ci_sessions`)
WHERE `session_id` =  '330185942e42270fc081ff1966fd6c4a'
AND `user_agent` =  'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36'
UPDATE `ci_sessions` SET `last_activity` = 1388667701, `session_id` = '9c05a7408814b65575524c8cf47840ee' WHERE session_id = '330185942e42270fc081ff1966fd6c4a'
INSERT INTO `log_access` (`useragent`, `to_access`, `is_mobile`, `is_robot`, `referer`, `remote_addr`, `regdate`) VALUES ('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36', '/feed/check_unread', 'N', 'N', 'http://notefolio_renew.localhost/toekki/101720?', '127.0.0.1', '2014-01-02 22:01:41')
===[took:0.005]

