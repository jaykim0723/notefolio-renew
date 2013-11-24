<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @brief Notefolio Admin Control Pannel Config File
 * @author Yoon, Seongsu(sople1@snooey.net)
 * 
 */
 
$config['acp_default_location'] = "/acp/";
$config['acp_menu'] = array(
                            'dashboard' => array (
                                'text' => '대쉬보드',
                                'url' => 'dashboard/',
                                'submenu' => array (
                                    'test' => array (
                                        'text' => 'test',
                                        'url' => 'test/',
                                     ),
                                ),
                             ),
                            'site' => array (
                                'text' => '사이트',
                                'url' => 'site/',
                                'submenu' => array (
                                    'test' => array (
                                        'text' => 'test',
                                        'url' => 'test/',
                                     ),
                                    'category' => array (
                                        'text' => '카테고리',
                                        'url' => 'category/',
                                     ),
                                    'mainbanner' => array (
                                        'text' => '메인배너',
                                        'url' => 'mainbanner/',
                                     ),
                                    'access_log' => array (
                                        'text' => '접속로그',
                                        'url' => 'access_log/',
                                     ),
                                ),
                             ),
                            'user' => array (
                                'text' => '사용자',
                                'url' => 'user/',
                                'submenu' => array (
                                	'member' => array (
                                        'text' => '회원',
                                        'url' => 'member/',
                                     ),
                                    'invite_code' => array (
                                        'text' => '초대장',
                                        'url' => 'invite_code/',
                                     ),
                                    'restrict' => array (
                                        'text' => '제한규칙',
                                        'url' => 'restrict/',
                                     ),
                                ),
                             ),
                            'gallery' => array (
                                'text' => '갤러리',
                                'url' => 'gallery/',
                                'submenu' => array (
                                    'rank' => array (
                                        'text' => '순위',
                                        'url' => 'rank/',
                                     ),
                                    'work' => array (
                                        'text' => '작업물',
                                        'url' => 'work/',
                                     ),
                                    'block' => array (
                                        'text' => '블록',
                                        'url' => 'block/',
                                     ),
                                    'category' => array (
                                        'text' => '카테고리',
                                        'url' => 'category/',
                                     ),
                                    'tag' => array (
                                        'text' => '태그',
                                        'url' => 'tag/',
                                     ),
                                ),
                             ),
                            'forum' => array (
                                'text' => '포럼',
                                'url' => 'forum/',
                                'submenu' => array (
                                    'space' => array (
                                        'text' => '영역',
                                        'url' => 'space/',
                                     ),
                                    'document' => array (
                                        'text' => '문서',
                                        'url' => 'document/',
                                     ),
                                    'comment' => array (
                                        'text' => '댓글',
                                        'url' => 'comment/',
                                     ),
                                   /* 'category' => array (
                                        'text' => '카테고리',
                                        'url' => 'category/',
                                     ),
                                    'tag' => array (
                                        'text' => '태그',
                                        'url' => 'tag/',
                                     ), */
                                ),
                             ),
                            'activity' => array (
                                'text' => '활동',
                                'url' => 'activity/',
                                'submenu' => array (
                                    'act_log' => array (
                                        'text' => '활동로그',
                                        'url' => 'act_log/',
                                     ),
                                    'alarms' => array (
                                        'text' => '알림',
                                        'url' => 'alarms/',
                                     ),
                                ),
                             ),
                            'admin' => array (
                                'text' => '관리자',
                                'url' => 'admin/',
                                'submenu' => array (
                                    'user' => array (
                                        'text' => '관리권한자',
                                        'url' => 'user/',
                                     ),
                                ),
                             ),
                             'featured' => array (
                                'text' => 'featured',
                                'url' => 'featured/',
                                'submenu' => array (
                                    'test' => array (
                                        'text' => 'test',
                                        'url' => 'test/',
                                     ),
                                    '메인작품등록' => array (
                                        'text' => 'featured',
                                        'url' => 'featured/',
                                     )
                                ),
                             ),
                             'statics' => array (
                                'text' => '통계',
                                'url' => 'statics/',
                                'submenu' => array (
                                    'research' => array (
                                        'text' => '분석',
                                        'url' => 'research/',
                                     ),
                                    'stat' => array (
                                        'text' => '현황',
                                        'url' => 'stat/',
                                     ),
                                    '작품 통계'=> array (
                                        'text' => '작품 통계',
                                        'url' => 'stc_works/',
                                     ),
                                    '회원 통계' => array (
                                        'text' => '회원 통계',
                                        'url' => 'stc_member/',
                                     )
                                ),
                             ),
                            'test' => array (
                                'text' => '테스트',
                                'url' => 'test/',
                                'submenu' => array (
                                    'code' => array (
                                        'text' => '코드 생성기',
                                        'url' => 'code/',
                                     ),
                                    'user_check' => array (
                                        'text' => '사용자이름 체크',
                                        'url' => 'user_check/',
                                     ),
                                    'email' => array (
                                        'text' => '메일 발송 테스트',
                                        'url' => 'email/',
                                     ),
                                    'fb_send_test' => array (
                                        'text' => 'facebook 발송 테스트',
                                        'url' => 'fb_send_test/',
                                     ),
                                    'microtime_test' => array (
                                        'text' => '마이크로타임 테스트',
                                        'url' => 'microtime_test/',
                                     ),
                                ),
                             ),
                        );
