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

<?php
$filename = '/data/profiles/'.$row->username.'_bg.jpg';
if(!file_exists($this->input->server('DOCUMENT_ROOT').$filename)){
	$filename = '/img/bg4.png';
}

?>
<div id="profile-header" style="background-image:url(<?=$filename?>?_=<?php echo substr($row->modified,-2) ?>);">

	<div id="profile-total">
		<span>Works  <span style="color:#fff"><?php echo number_format($total->work_cnt) ?></span></span>
		<span>Viewed  <span style="color:#fff"><?php echo number_format($total->hit_cnt) ?></span></span>
		<span>Liked  <span style="color:#fff"><?php echo number_format($total->note_cnt) ?></span></span>
		<span>Collected  <span style="color:#fff"><?php echo number_format($total->collect_cnt) ?></span></span>
	</div>

	<div id="profile-inner-wrapper" style="background-color:<?php echo $row->face_color ?>;<?php if (USER_ID!=$row->user_id){echo 'padding-top:25px'; } ?>">
			
		<div id="profile-inner" style="<?php if (USER_ID!=$row->user_id){echo 'padding-top:30px !important;'; } ?>">
			<?php if($this->session->userdata('username')==$row->username): ?>
			<div id="btn-edit-profile" class="pull-right btn-group">
				<button type="button" class="btn btn-default btn-xs dropdown-toggle btn-profilemini" data-toggle="dropdown">
				  <span class="text">프로필 설정</span>
				  <span class="caret"></span>
				</button>
				<ul class="dropdown-menu">
				  <li><a id="btn-upload-face" class="hidden-xs hidden-sm" href="#3">프로필 사진 업로드</a></li>
				  <li><a id="btn-select-face" class="hidden-xs hidden-sm" href="#3">작품 중 선택</a></li>
				  <li><a id="btn-delete-face" class="hidden-xs hidden-sm" href="#3">삭제</a></li>
				  <li class="divider hidden-xs hidden-sm"></li>
				  <li><a id="btn-change-color" href="#3">배경색 설정</a></li>
				  <li class="divider"></li>
				  <li><a id="btn-change-realname" href="#3">사용자명 설정</a></li>
				  <li><a id="btn-change-keywords" href="#3">카테고리 설정</a></li>
				  <li><a id="btn-change-sns" href="#3">SNS주소 설정</a></li>
				</ul>
			</div>
			<?php endif; ?>
			<div id="profile-image">
				<img src="/data/profiles/<?php echo $row->username ?>_face.jpg?_=<?php echo substr($row->modified,-2) ?>" alt="" onerror="this.src='/img/default_profile_face.png'">
			</div>
			<div id="profile-info">
				<h2>
					<?php 
					if(USER_ID==$row->user_id){
					?>
					<a href="javascript:profileUtil.changeRealname();">
					<?php
						if(empty($row->realname)){
							echo '사용자명을 입력해주세요';
						} else {
							echo $row->realname;
						}
					?>
					</a>
					<?php
					}
					else {
						echo $row->realname;
					}
					?>
				</h2>
				<h4 id="profile-keywords" data-value="<?php echo $row->user_keywords ?>">
					&nbsp;
					<?php 
					if(USER_ID==$row->user_id && empty($row->user_keywords)){
						echo '<a href="javascript:profileUtil.changeKeywords();">카테고리를 입력해주세요.</a>';
					} else {
						echo $this->nf->category_to_string($row->user_keywords, true); 
					}
					?>
					&nbsp;
				</h4>
			</div>

			<div id="profile-sns-link">
				<?php 
				$s = $this->nf->sns_to_string($row->sns);
				if($s!='')
					echo $s;
				else if(USER_ID==$row->user_id)
					echo '<a href="javascript:profileUtil.changeSNS();">SNS주소를 입력해주세요.</a>';
				?>
			</div>

			<?php if (USER_ID!=$row->user_id): ?>
			<div class="centered">
				<a href="javascript:;" data-id="<?php echo $row->user_id ?>" class="btn btn-follow btn-nofol2 btn-hover <?php echo $row->is_follow=='y'?'activated' : '' ?>" style="border:none;">
					<i class="spi spi-following_white"></i>
					<i class="spi spi-follow_point"></i>
					<i class="spi spi-follow_white"></i>
					<span>Follow<?php echo $row->is_follow=='y'?'ing' : '' ?></span>
				</a>
			</div>
			<?php endif ?>

		</div>
	</div>
	<?php if($this->session->userdata('username')==$row->username): ?>
	<div id="btn-edit-cover" class="hidden-xs hidden-sm">
		<div class="pull-right btn-group">
			<button type="button" class="btn btn-default btn-xs dropdown-toggle btn-profilemini" data-toggle="dropdown">
			  <span class="text">배경 설정</span>
			  <span class="caret"></span>
			</button>
			<ul class="dropdown-menu">
			  <li><a id="btn-upload-bg" href="#3">업로드</a></li>
			  <li><a id="btn-select-bg" href="#3">작품 중 선택</a></li>
			  <li><a id="btn-delete-bg" href="#3">삭제</a></li>
			</ul>
		</div>
	</div>	
	<?php endif; ?>
</div>

<section>
	<div class="container">
		<div class="row promenu">
			<div class="col-md-12" style="padding:0px;">
				<ul class="nav nav-pills pull-right fol">
					<li id="profile_nav_followings">
						<a href="/<?php echo $row->username ?>/followings" style="color: #7a7880;">Followings&nbsp;&nbsp;<span style="font-weight:700"><?php echo number_format($total->following_cnt) ?></span></a>
					</li>
					<li id="profile_nav_followers">
						<a href="/<?php echo $row->username ?>/followers" style="color: #7a7880;"> Followers&nbsp;&nbsp;<span style="font-weight:700"><?php echo number_format($total->follower_cnt) ?></span></a>
					</li>
				</ul>
				<div class="clearfix visible-xs"></div>
				<ul id="profile-nav" class="nav nav-pills  item<?php echo $this->session->userdata('username')==$row->username ? '4' : '3' ?>">
					<li id="profile_nav_">
						<a href="/<?php echo $row->username ?>">
							<i class="spi spi-work"></i><i class="spi spi-work_point"></i><span class="text"> Works</span><span class="number"></span>
						</a>
					</li>
					<li id="profile_nav_collect">
						<a href="/<?php echo $row->username ?>/collect">
							<i class="spi spi-love"></i><i class="spi spi-love_point"></i><span class="text"> Collect</span><span class="number"></span>
						</a>
					</li>
					<li id="profile_nav_about">
						<a href="/<?php echo $row->username ?>/about">
							<i class="spi spi-user"></i><i class="spi spi-user_point" ></i><span class="text"> About</span><span class="number"></span>
						</a>
					</li>
					<?php if($this->session->userdata('username')==$row->username): ?>
					<li id="profile_nav_statistics">
						<a href="/<?php echo $row->username ?>/statistics">
							<i class="spi spi-graph"></i><i class="spi spi-graph_point" ></i><span class="text"> Statistics</span><span class="number"></span>
						</a>
					</li>
					<?php endif; ?>

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
	NFview.keywordList = <?php
		$this->load->config('keyword', TRUE);
		$keyword_list = $this->config->item('keyword', 'keyword');	
		echo json_encode($keyword_list);
	?>;
</script>
<?php
	// /app/controller/auth.php line 653
	//$this->session->set_userdata('welcome_newmember',true); // 가입환영용
	if($this->session->userdata('welcome_newmember')){
?>
<script type="text/javascript">
	$(function(){
		var firstTimeHelper = new BootstrapDialog({
	        message: function(dialogRef){
	            var $message = $('<div><h4>노트폴리오에 오신 것을 환영합니다.</h4>원활한 사용을 위하여 개인정보 설정을 하여 주십시오.</div>');
	            var $button = $('<button style="margin-top:30px;" class="btn btn-primary btn-lg btn-block">Next</button>');
	            $button.on('click', {dialogRef: dialogRef}, function(event){
	                event.data.dialogRef.close();
	                profileUtil.changeKeywords();
	            });
	            $message.append($button);
	    
	            return $message;
	        },
	        closable: false
	    });
	    firstTimeHelper.realize();
	    firstTimeHelper.getModalHeader().hide();
	    firstTimeHelper.getModalFooter().hide();
	    firstTimeHelper.open();
	});
</script>
<?php
	$this->session->unset_userdata('welcome_newmember');
	}
?>
<?php

/*
<?php if($this->tank_auth->is_logged_in() && ($this->config->item('debug_tutorial')=='y' OR strpos($this->session->userdata('tutorial'), 'profile')!==FALSE)):
$this->session->set_userdata('tutorial', str_replace($this->session->userdata('tutorial'), '(profile)', ''));
?>
<script src="/js/libs/bootstro.min.js"></script>
<script>
	if($('#style_bootstro').length==0)
		$('head').append('<link id="style_bootstro" href="/css/bootstro.min.css" rel="stylesheet"/>');
	$(function(){
		if($(window).width()>991){
			// init bootstro
			$('#profile-image').addClass('bootstro')
				.attr('data-bootstro-step', 0)
				.attr('data-bootstro-title', '프로필 변경')
				.attr('data-bootstro-content', '배경색과 투명도를 변경할 수 있으며, 사진도 변경할 수 있습니다. 마우스를 올려보시면 버튼이 나타납니다.');
			$('#btn-edit-cover').show().addClass('bootstro')
				.attr('data-bootstro-step', 1)
				.attr('data-bootstro-placement', 'left')
				.attr('data-bootstro-title', '배경색 변경')
				.attr('data-bootstro-content', '우측 상단을 마우스로 클릭하시면 배경을 커스터마이징 할 수 있습니다.');
			$('#profile-nav').show().addClass('bootstro')
				.attr('data-bootstro-step', 2)
				.attr('data-bootstro-title', '네비게이션 메뉴')
				.attr('data-bootstro-content', '당신에 관한 모든 사안들을 이곳에서 확인하세요. 특히 Statistics는 가관입니다.');
			bootstro.start();
		}else{
			// init mobile tutorial
			
		}
	});
	

</script>
<?php endif; ?>	
*/ ?>
