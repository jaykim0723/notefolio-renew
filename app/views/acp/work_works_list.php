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
          <th>&nbsp;</th>
          <th>작성자</th>
          <th>노폴랭크</th>
          <th>생성일</th>
          <th>보기</th>
          <th>수정</th>
          <th>삭제</th>
        </tr>
      </thead>
      <tbody>
    <?php
        if(isset($list)&&count($list)>0) {
          foreach($list as $k=>$v){
    ?>
        <tr class="">
          <td><a href="/acp/work/works/view/id/<?=$v->work_id?>"><?=$v->work_id?></a></td>
          <td><a href="/<?=$v->user->username?>/<?=$v->work_id?>">
            <img src="/data/covers/<?=$v->work_id?>_t2.jpg" width="200"><br/>
            <p><?=mb_substr($v->title, 0, 50, 'UTF-8')?>
                  <?=((mb_strlen($v->title, 'UTF-8')>50)?'...':'')?> / <?=$v->regdate?></p>
          </a></td>  
          <td><a href="/acp/user/member/view/id/<?=$v->user->id?>">
            <?=$v->user->realname?>
              </a></td>
          <td>
            피드백총계 : <?=$v->nofol_rank?><br />

            충실도  : <?=$v->discoverbility?><br />
            피드백기간계 : <?=$v->point?><br />
            스탭점수 : <?=$v->staffpoint?><br />
            랭크총계 : <?=$v->rank_point?><br />

            스탭포인트 수정:
            <input type="text" value="<?=$v->staffpoint?>" name="staffpoint"  />
            <a>
              <span>수정</span>
            </a>
          </td>
          <td><?=$v->regdate?></td>
          <td><a class="btn" href="/acp/work/works/view/id/<?=$v->work_id?>">보기</a></td>
          <td><a class="btn btn-primary" href="/acp/work/works/modify/id/<?=$v->work_id?>">수정</a></td>
          <td><a class="btn btn-danger" href="/acp/work/works/delete/id/<?=$v->work_id?>">삭제</a></td>
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