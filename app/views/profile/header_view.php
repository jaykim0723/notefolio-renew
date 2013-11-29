<div id="profile_header">
	<div id="profile_inner">
		aoenthu
	</div>
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
					<li id="profile_nav_"><a href="/<?php echo $this->session->userdata('username') ?>">작가의 작품</a></li>
					<li id="profile_nav_collection"><a href="/<?php echo $this->session->userdata('username') ?>/collection">작가의 콜렉트</a></li>
					<li id="profile_nav_about"><a href="/<?php echo $this->session->userdata('username') ?>/about">작가소개</a></li>
					<li id="profile_nav_statistics"><a href="/<?php echo $this->session->userdata('username') ?>/statistics">통계</a></li>
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