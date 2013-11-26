<section>
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="well" style="height: 300px;">
					프로필 헤더
				</div>

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
