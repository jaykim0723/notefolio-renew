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
            <p>ID:        <?=$row->id?></p>
            <p>아이디:      <?=$row->username?></p>
            <p>이메일:      <?=$row->email?></p>
            <p>실명:       <?=$row->realname?></p>
            <p>레벨:       <?=$row->level?></p>
            <p>가입일:      <?=$row->created?></p>
            <p>마지막 로그인: <?=$row->last_ip?> (<?=$row->last_login?>)</p>
            <p>마지막 수정:  <?=$row->modified?></p>
            <hr />
            <p>웹사이트:    <?=$row->website?></p>
            <p>facebook:  <?=$row->facebook_id?></p>
            <p>twitter:   <?=$row->twitter_id?></p>
            <p>성별:       <?=$row->gender?></p>
            <p>생일:       <?=$row->birth?></p>
            <p>자기소개:    <?=$row->description?></p>
            <p>메일링 수신:  <?=$row->mailing?></p>
            <p>팔로잉:      팔로우 <?=$row->following_cnt?>명 / 팔로워 <?=$row->follower_cnt?>명</p>
            <hr />
            <p>페이스북 Number ID:      <?=$row->fb_num_id?></p>
            <p>페이스북 Access Token:   <?=$row->access_token?></p>
            <p>글 공개 시 페이스북 등록:   <?=$row->post_work?></p>
            <p>댓글 시 페이스북 등록:     <?=$row->post_comment?></p>
            <p>추천 시 페이스북 등록:     <?=$row->post_note?></p>
            <hr />

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