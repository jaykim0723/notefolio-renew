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
            <!--
            <p>ID:        <#?=$row->id?#></p>
            <p>아이디:      <#?=$row->username?#></p>
            <p>이메일:      <#?=$row->email?#></p>
            <p>실명:       <#?=$row->realname?#></p>
            <p>레벨:       <#?=$row->level?#></p>
            <p>가입일:      <#?=$row->created?#></p>
            <p>마지막 로그인: <#?=$row->last_ip?#> (<#?=$row->last_login?#>)</p>
            <p>마지막 수정:  <#?=$row->modified?#></p>
            <p>웹사이트:    <#?=$row->website?#></p>
            <p>facebook:  <#?=$row->facebook_id?#></p>
            <p>twitter:   <#?=$row->twitter_id?#></p>
            <p>성별:       <#?=$row->gender?#></p>
            <p>생일:       <#?=$row->birth?#></p>
            <p>자기소개:    <#?=$row->description?#></p>
            <p>메일링 수신:  <#?=$row->mailing?#></p>
            <p>팔로잉:      팔로우 <#?=$row->following_cnt?#>명 / 팔로워 <#?=$row->follower_cnt?#>명</p>
            -->
            <form id="add-profile" class="form-horizontal">
              <fieldset>
                
                <div class="control-group">                     
                  <label class="control-label" for="id">ID</label>
                  <div class="controls">
                    <input type="text" class="span1 disabled" id="id" value="" disabled="">
                    <p class="help-block">자동 생성됩니다.</p>
                  </div> <!-- /controls -->       
                </div> <!-- /control-group -->
                
                <div class="control-group">                     
                  <label class="control-label" for="username">아이디</label>
                  <div class="controls">
                    <input type="text" class="span6" id="username" value="">
                  </div> <!-- /controls -->       
                </div> <!-- /control-group -->
                
                <div class="control-group">                     
                  <label class="control-label" for="email">이메일</label>
                  <div class="controls">
                    <input type="text" class="span4" id="email" value="">
                  </div> <!-- /controls -->       
                </div> <!-- /control-group -->
                
                <br><br>
                
                <div class="control-group">                     
                  <label class="control-label" for="password1">비밀번호</label>
                  <div class="controls">
                    <input type="password" class="span4" id="password1" value="thisispassword">
                  </div> <!-- /controls -->       
                </div> <!-- /control-group -->
                
                <div class="control-group">                     
                  <label class="control-label" for="password2">비밀번호 재입력</label>
                  <div class="controls">
                    <input type="password" class="span4" id="password2" value="thisispassword">
                  </div> <!-- /controls -->       
                </div> <!-- /control-group -->

                <br>
                
                <div class="control-group">                     
                  <label class="control-label" for="realname">실명</label>
                  <div class="controls">
                    <input type="text" class="span6" id="realname" value="">
                  </div> <!-- /controls -->       
                </div> <!-- /control-group -->
                  
                 <br>
                  
                <div class="form-actions">
                  <button class="btn btn-success">만들기</button>
                  <a href="/acp/user/member/list/"><span class="btn">취소</span></a>
                </div> <!-- /form-actions -->
              </fieldset>
            </form>
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