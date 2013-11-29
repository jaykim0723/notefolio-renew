<div class="main">
  <div class="main-inner">
    <div class="container">
      <div class="row">
        <div class="widget ">
          <div class="widget-header">
            <i class="icon-user"></i>
            <h3>회원 정보</h3>
          </div>
          <!-- /widget-header -->
          <div class="widget-content">
            <p>
              <label class="control-label">ID</label>
              <?=$row->id?>
            </p>
            <p>
              <label class="control-label">아이디</label>
              <?=$row->username?>
            </p>
            <p>
              <label class="control-label">이메일</label>
              <?=$row->email?>
            </p>
            <p>
              <label class="control-label">실명</label>
              <?=$row->realname?>
            </p>
            <p>
              <label class="control-label">레벨</label>
              <?=$row->level?>
            </p>
            <p>
              <label class="control-label">가입일</label>
              <?=$row->created?>
            </p>
            <p>
              <label class="control-label">마지막 로그인</label>
              <?=$row->last_ip?> (<?=$row->last_login?>)
            </p>
            <p>
              <label class="control-label">마지막 수정<label>
              <?=$row->modified?>
            </p>
            <p>웹사이트:    <?=$row->website?></p>
            <p>facebook:  <?=$row->facebook_id?></p>
            <p>twitter:   <?=$row->twitter_id?></p>
            <p>성별:       <?=$row->gender?></p>
            <p>생일:       <?=$row->birth?></p>
            <p>자기소개:    <?=$row->description?></p>
            <p>메일링 수신:  <?=$row->mailing?></p>
            <p>팔로잉:      팔로우 <?=$row->following_cnt?>명 / 팔로워 <?=$row->follower_cnt?>명</p>

            <p>
              <a href="/acp/user/member/list"><span class="btn btn-info">리스트</span></a>
              <a href="/acp/user/member/edit/id/<?=$row->id?>"><span class="btn">수정</span></a>
              <a href="/acp/user/member/del/id/<?=$row->id?>"><span class="btn btn-danger">삭제</span></a>
            </p>
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