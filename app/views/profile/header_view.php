<?php if (USER_ID!=0): ?>
	<script src="/js/member.js"></script>	
	<script src="/js/libs/jquery.Jcrop.min.js"></script>
	<script>
		if($('#style_crop').length==0)
			$('head').append('<link id="style_crop" rel="stylesheet" type="text/css" href="/css/crop/jquery.Jcrop.css"/>');
	</script>
<?php endif ?>

<div id="profile-header" style="background-image:url(/data/profiles/<?php echo $row->username ?>-bg.jpg?_=<?php echo substr($row->modified,-2) ?>);">
	<div id="profile-inner-wrapper">
		<div id="profile-inner">
			<div id="btn-edit-inner">
					<?php if($this->session->userdata('username')==$row->username): ?>
					<div class="pull-right btn-group">
					<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
					  <span class="text">이너 편집</span>
					  <span class="caret"></span>
					</button>
					<ul class="dropdown-menu">
					  <li><a href="#">배경색 변경</a></li>
					  <li><a href="#">투명도 변경</a></li>
					</ul>
					<?php endif; ?>	
				</div>
			</div>	
			<div id="profile-image">
				<img src="/data/profiles/<?php echo $row->username ?>.jpg?_=<?php echo substr($row->modified,-2) ?>" alt=""/>
				<?php if($this->session->userdata('username')==$row->username): ?>
				<div id="btn-edit-profile" class="btn-group">
					<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
					  <span class="text">프로필사진 편집</span>
					  <span class="caret"></span>
					</button>
					<ul class="dropdown-menu">
					  <li><a id="btn-upload-face" href="#">사진 업로드</a></li>
					  <li><a id="btn-select-face">작품 중 선택</a></li>
					  <li><a href="#">삭제</a></li>
					</ul>
				</div>	
				<?php endif; ?>
			</div>
			<div id="profile-info">
				<h2><?php echo $row->username; ?></h2>
				<h4>&nbsp;<?php echo @implode('·', $row->user_keywords); ?>&nbsp;</h4>
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
			  <span class="text">커버 편집</span>
			  <span class="caret"></span>
			</button>
			<ul class="dropdown-menu">
			  <li><a id="btn-upload-bg" href="#">사진 업로드</a></li>
			  <li><a id="btn-select-bg">작품 중 선택</a></li>
			  <li><a href="#">삭제</a></li>
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
						<a href="/<?php echo $row->username ?>/collection">
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
		$('#btn-upload-face').ajaxUploader({
			multiple : false
		});
		$('#btn-upload-bg').ajaxUploader({
			multiple : false
		});
		$('#btn-select-face, #btn-select-bg').on('click', function(){
			var _actionTarget = this.id == 'btn-select-bg' ? 'bg' : 'face';
			site.popWorkList({
				username :site.segment[0],
				done : function(dialog){
					console.log('profile headialoger done', dialog);
					var work_id = dialog.getModalBody().find('.selected').prop('id').replace('work-recent-', '');
					console.log('work_id : ', work_id);
					dialog.close();
					
					// work_id에 대한 이미지 원본을 가지고 와서 크롭창을 띄워준다.
					var src = '';
					if(_actionTarget=='face'){
						// change profile face
						memberUtil.popCrop({
							message : '프로필 사진으로 쓸 영역을 지정해주세요.',
							width : 400,
							height: 400,
							done : function(){
								var crop = NFview.popCrop[0].tellSelect();
								// 이미지 src, crop 정보를 토대로 사진을 잘라내는 명령을 내린다.
								$.post('/profile/change_profile_face', {
									src : src,
									x : crop.x,
									y : crop.y,
									h : crop.h,
									h : crop.h
								}, 'json').done(function(responseJSON){
									console.log('crop profile face done > responseJSON', responseJSON);
									// 프로필 이미지를 응답받은 주소로 갱신을 해준다.
									// #do stuff
								});
							}
						});
					}else{
						// change profile bg
						$.post('/profile/change_profile_bg', {
							src : src
						}, 'json').done(function(responseJSON)){
							console.log('crop profile bg done > responseJSON', responseJSON);
							// 프로필 배경을 응답받은 주소로 갱신을 해준다.
							// #do stuff
						});
					}
				}
			});
		})
	});
	<?php endif; ?>	
	NFview.area = 'profile';
</script>