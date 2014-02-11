<?php 

?>
<div class="main">
  <div class="main-inner">
      <div class="container">


    <h2>작품 목록</h2>

    <p class="lead">총 <?=$all_count?> 개 / <?=$all_page?> 페이지 중 <?=$now_page?> 번째</p>

    <table  class="table table-hover">
      <thead>
        <tr>
          <th>순번</th>
          <th>T2</th>
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
          <td><a href="/acp/work/works/view/id/<?=$v->work_id?>"><?=$v->work_id?></a></td>
          <td><a href="/<?$v->user->username?>/<?=$v->work_id?>"><img src="/data/cover/<?=$v->work_id?>_t2.jpg"></a></td>  
          <td><a href="<?=$v->to_access?>">
              <?=mb_substr($v->to_access, 0, 50, 'UTF-8')?>
                  <?=((mb_strlen($v->to_access, 'UTF-8')>50)?'...':'')?></a></td>
          <td><a href="<?=$v->referer?>">
              <?=mb_substr($v->referer,  0, 50, 'UTF-8')?>
                  <?=((mb_strlen($v->referer,  'UTF-8')>50)?'...':'')?></a></td>
          <td><?=$v->regdate?></td>
          <td><a class="btn" href="/acp/work/works/view/id/<?=$v->id?>">보기</a></td>
          <td><a class="btn btn-primary" href="/acp/work/works/modify/id/<?=$v->id?>">수정</a></td>
          <td><a class="btn btn-danger" href="/acp/work/works/delete/id/<?=$v->id?>">삭제</a></td>
        </tr>
    <?php
          }
        } else {
    ?>
        <tr class="info">
          <td colspan="7">작품이 없습니다.</td>
        </tr>
    <?php   
        }
    ?>
      </tbody>
    </table>
    <div class="row-fluid">
      <?=get_paging($params=array('now_page'=>$page, 'last_page'=>$all_page, 'url'=> '/work/works/list'.$search_url))?>
    </div>


      </div> <!-- /container -->
  </div> <!-- /main-inner -->
</div>