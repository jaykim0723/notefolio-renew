<?php if($this->session->userdata('username')==$row->username): ?>
	<script src="/js/member.js"></script>	
	<script src="/js/libs/jquery.Jcrop.min.js"></script>
	<script src="/js/libs/spectrum.js"></script>
	<script>
		if($('#style_crop').length==0)
			$('head').append('<link id="style_crop" rel="stylesheet" type="text/css" href="/css/crop/jquery.Jcrop.css"/>');
		if($('#style_spectrum').length==0)
			$('head').append('<link id="style_spectrum" rel="stylesheet" type="text/css" href="/css/spectrum.css"/>');
	</script>
<?php endif ?>

<div id="profile-header" style="background-image:url(/data/profiles/<?php echo $row->username ?>_bg.jpg?_=<?php echo substr($row->modified,-2) ?>);">
	<div id="profile-inner-wrapper" style="background-color:<?php echo $row->face_color ?>">
		<div id="profile-inner">
			<div id="profile-image">
				<img src="/data/profiles/<?php echo $row->username ?>_face.jpg?_=<?php echo substr($row->modified,-2) ?>" alt="" onerror="this.src='/img/default_profile_face.png'">
				<?php if($this->session->userdata('username')==$row->username): ?>
				<div id="btn-edit-profile" class="btn-group">
					<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
					  <span class="text">프로필사진 편집</span>
					  <span class="caret"></span>
					</button>
					<ul class="dropdown-menu">
					  <li><a id="btn-upload-face" href="#3">사진 업로드</a></li>
					  <li><a id="btn-select-face" href="#3">작품 중 선택</a></li>
					  <li><a id="btn-delete-face" href="#3">삭제</a></li>
					  <li class="divider"></li>
					  <li><a id="btn-change-color" href="#3">배경색 변경</a></li>
					</ul>
				</div>	
				<?php endif; ?>
			</div>
			<div id="profile-info">
				<h2><?php echo $row->username; ?></h2>
				<h4>&nbsp;<?php echo $this->nf->category_to_string($row->user_keywords); ?>&nbsp;</h4>
			</div>

			<div id="profile-sns-link">
				<?php foreach ($row->sns as $service => $id):
				$tmp = $this->nf->sns($service, $id);
				?>
				<a href="<?php echo $tmp->link  ?>" class="<?php echo $service ?>" class="btn-hover">
					<i class="spi spi-fb"></i>
				</a>
				<?php endforeach ?>
			</div>

		</div>
	</div>
	<?php if($this->session->userdata('username')==$row->username): ?>
	<div id="btn-edit-cover">
		<div class="pull-right btn-group">
			<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
			  <span class="text">배경 편집</span>
			  <span class="caret"></span>
			</button>
			<ul class="dropdown-menu">
			  <li><a id="btn-upload-bg" href="#3">사진 업로드</a></li>
			  <li><a id="btn-select-bg" href="#3">작품 중 선택</a></li>
			  <li><a id="btn-delete-bg" href="#3">삭제</a></li>
			</ul>
		</div>
	</div>	
	<?php endif; ?>
</div>

<section>
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<ul class="pull-right nav nav-pills list-inline">
					<li><a href="/<?php echo $row->username ?>/followings">23 Followings</a></li>
					<li><a href="/<?php echo $row->username ?>/followers">29 Followers</a></li>
				</ul>
				<div class="clearfix visible-xs"></div>
				<ul id="profile-nav" class="nav nav-pills">
					<li id="profile_nav_">
						<a href="/<?php echo $row->username ?>">
							<i class="spi spi-check">check</i><span class="text"> Works</span><span class="number">234</span>
						</a>
					</li>
					<li id="profile_nav_collection">
						<a href="/<?php echo $row->username ?>/collect">
							<i class="spi spi-check">check</i><span class="text"> Collect</span><span class="number">22</span>
						</a>
					</li>
					<li id="profile_nav_about">
						<a href="/<?php echo $row->username ?>/about">
							<i class="spi spi-check">check</i><span class="text"> About</span><span class="number">42</span>
						</a>
					</li>
					<li id="profile_nav_statistics">
						<a href="/<?php echo $row->username ?>/statistics">
							<i class="spi spi-check">check</i><span class="text"> Statistics</span><span class="number">234</span>
						</a>
					</li>
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

	<?php if($this->session->userdata('username')==$row->username): ?>
	// 본인의 프로필일 경우에는 관리자에 관한 메뉴들을 활성화시켜준다.
	$(function(){
		profileUtil.setGround();
	});
	<?php endif; ?>	

	NFview.area = 'profile';
</script>