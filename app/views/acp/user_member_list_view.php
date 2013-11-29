<div class="main">
  <div class="main-inner">
    <div class="container">
      <div class="row">
        <div class="panel panel-info">
          <div class="panel-heading">
            <h3 class="panel-title">회원 목록 정보</h3>
          </div>
          <div class="panel-body">
            <p>전체: <?=$all_count?>개</p>
            <p>페이지: <?=$page?>/<?=$all_page?> 페이지</p>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th>ID</th>
                <th>아이디</th>
                <th>실명</th>
                <th>이메일</th>
                <th>가입일</th>
                <th><i class="icon-wrench"></i></th>
              </tr>
            </thead>
            <tbody>
              <? foreach ($rows as $key=>$row) { ?>
              <tr>
                <td><?=$row->id?></td>
                <td><?=$row->username?></td>
                <td><?=$row->realname?></td>
                <td><?=$row->email?></td>
                <td><?=$row->created?></td>
                <td>보기 수정 삭제</td>
              </tr>
              <? } ?>
            </tbody>
          </table>

          <?=get_paging($params=array('now_page'=>$page, 'url'=> '/acp/user/member'))?>
        </div>
        <!-- /table-responsive --> 
      </div>
      <!-- /row --> 
    </div>
    <!-- /container --> 
  </div>
  <!-- /main-inner --> 
</div>
<!-- /main -->