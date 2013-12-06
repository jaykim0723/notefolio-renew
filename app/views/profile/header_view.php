<div id="profile-header" style="background-image:url(http://notefolio.net/img/1312/29564_r);">
	<div id="profile-inner-wrapper">
		<div id="profile-inner">
			<div id="btn-edit-inner" class="btn-group">
				<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
				  이너 편집
				  <span class="caret"></span>
				</button>
				<ul class="dropdown-menu">
				  <li><a href="#">배경색 변경</a></li>
				  <li><a href="#">투명도 변경</a></li>
				</ul>
			</div>	
			<div id="profile-image">
				<img src="http://notefolio.net/profiles/147?h=1385655105" alt=""/>
				<div id="btn-edit-profile" class="btn-group">
					<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
					  프로필사진 편집
					  <span class="caret"></span>
					</button>
					<ul class="dropdown-menu">
					  <li><a href="#">사진 업로드</a></li>
					  <li><a href="#">작품 중 선택</a></li>
					  <li><a href="#">삭제</a></li>
					</ul>
				</div>	
			</div>
			<div class="centerted">
				<h2><?php echo $username ?></h2>
				<h4>aoeu, aoeu, aoeu</h4>
			</div>
		</div>
	</div>
	<?php if($this->session->userdata('username')==$username): ?>
	<div id="btn-edit-cover" class="btn-group">
		<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
		  커버 편집
		  <span class="caret"></span>
		</button>
		<ul class="dropdown-menu">
		  <li><a href="#">사진 업로드</a></li>
		  <li><a href="#">작품 중 선택</a></li>
		  <li><a href="#">삭제</a></li>
		</ul>
	</div>	
	<?php endif; ?>
</div>

<section>
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<ul class="pull-right nav nav-pills list-inline">
					<li><a>Following</a></li>
					<li><a>Follower</a></li>
				</ul>
				<div class="clearfix visible-xs"></div>
				<ul id="profile_nav" class="nav nav-pills">
					<li id="profile_nav_"><a href="/<?php echo $username ?>">작가의 작품</a></li>
					<li id="profile_nav_collection"><a href="/<?php echo $username ?>/collection">작가의 콜렉트</a></li>
					<li id="profile_nav_about"><a href="/<?php echo $username ?>/about">작가소개</a></li>
					<li id="profile_nav_statistics"><a href="/<?php echo $username ?>/statistics">통계</a></li>
				</ul>
				<script>
					$('#profile_nav_<?php echo $this->uri->segment(2) ?>').addClass('active');
				</script>
			</div>
		</div>
	</div>
</section>

<script>
	if(site.prevPage.url.indexOf(site.url+site.segment[0])==0){
		$('html,body').animate({
			scrollTop : site.prevPage.top
		}, 1);
	}
</script>