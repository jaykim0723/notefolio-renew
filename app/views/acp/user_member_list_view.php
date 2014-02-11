<div class="main">
  <div class="main-inner">
    <div class="container">
      <div class="row">
        <div class="widget ">
          <div class="widget-header">
            <i class="icon-user"></i>
            <h3>회원 목록</h3>
          </div>
          <!-- /widget-header -->
          <div class="widget-content">
            <div class="container">
              <p>전체: <?=$all_count?>개</p>
              <p>페이지: <?=$page?>/<?=$all_page?> 페이지</p>
            </div>
            <div class="container">
              <div class="table-responsive">
                <table class="table table-striped table-hover">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>아이디</th>
                      <th>실명</th>
                      <th>이메일</th>
                      <th>가입일</th>
                      <th>
                        <? for ($i=0;$i<11;$i++) { ?>
                        <i class="icon-wrench"></i>
                        <? } ?>
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <? foreach ($rows as $key=>$row) { ?>
                    <tr>
                      <td><a href="/acp/user/member/view/id/<?=$row->id?>"><?=$row->id?></a></td>
                      <td><a href="/acp/user/member/view/id/<?=$row->id?>"><?=$row->username?></a></td>
                      <td><a href="/acp/user/member/view/id/<?=$row->id?>"><?=$row->realname?></a></td>
                      <td><?=$row->email?></td>
                      <td><?=$row->created?></td>
                      <td>
                        <a href="/acp/user/member/view/id/<?=$row->id?>"><span class="btn btn-info">보기</span></a>
                        <a href="/acp/user/member/edit/id/<?=$row->id?>"><span class="btn">수정</span></a>
                        <a href="/acp/user/member/del/id/<?=$row->id?>"><span class="btn btn-danger">삭제</span></a>
                      </td>
                    </tr>
                    <? } ?>
                  </tbody>
                </table>
              </div>
              <!-- /table-responsive -->
              <?=get_paging($params=array('now_page'=>$page, 'last_page'=>$all_page, 'url'=> '/user/member/list'))?>
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