<?php 

?>
<div class="main">
  <div class="main-inner">
    <div class="container">
      <div class="row">
        <div class="widget ">
          <div class="widget-header">
            <i class="icon-user"></i>
            <h3>접속 로그 목록</h3>
          </div>
          <!-- /widget-header -->
          <div class="widget-content">
            <div class="container">
              <p>전체: <?=$all_count?>개</p>
              <p>페이지: <?=$page?>/<?=$all_page?> 페이지</p>
            </div>
            
            <table  class="table table-hover">
              <thead>
                <tr>
                  <th>순번</th>
                  <th>IP</th>
                  <th>접속위치</th>
                  <th>리퍼러</th>
                  <th>생성일</th>
                  <th>보기</th>
                  <th>수정</th>
                  <th>삭제</th>
                </tr>
              </thead>
              <tbody>
            <?php
                if(isset($list)&&$list!=array()) {
                  foreach($list as $k=>$v){
            ?>
                <tr class="">
                  <td><a href="/acp/site/access_log/view/id/<?=$v['id']?>"><?=$v['id']?></a></td>
                  <td><a href="http://whois.net/ip-address-lookup/<?=$v['remote_addr']?>"><?=$v['remote_addr']?></a></td>  
                  <td><a href="<?=$v['to_access']?>">
                      <?=mb_substr($v['to_access'], 0, 50, 'UTF-8')?>
                          <?=((mb_strlen($v['to_access'], 'UTF-8')>50)?'...':'')?></a></td>
                  <td><a href="<?=$v['referer']?>">
                      <?=mb_substr($v['referer'],  0, 50, 'UTF-8')?>
                          <?=((mb_strlen($v['referer'],  'UTF-8')>50)?'...':'')?></a></td>
                  <td><?=$v['regdate']?></td>
                  <td><a class="btn" href="/acp/site/access_log/view/id/<?=$v['id']?>">보기</a></td>
                  <td><a class="btn btn-primary" href="/acp/site/access_log/modify/id/<?=$v['id']?>">수정</a></td>
                  <td><a class="btn btn-danger" href="/acp/site/access_log/delete/id/<?=$v['id']?>">삭제</a></td>
                </tr>
            <?php
                  }
                } else {
            ?>
                <tr class="info">
                  <td colspan="7">접속 로그가 없습니다.</td>
                </tr>
            <?php   
                }
            ?>
              </tbody>
            </table>
            <div class="row-fluid">
              <?=get_paging($params=array('now_page'=>$page, 'last_page'=>$all_page, 'url'=> '/site/access_log/list'.$search_url))?>
            </div>
          </div>
          <!-- /widget-content -->
        </div>
      </div>
      
      <!-- /row --> 
    </div>
    <!-- /container --> 
  </div>
  <!-- /main-inner --> 
</div>
<!-- /main -->