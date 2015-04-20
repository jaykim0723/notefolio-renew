<!--<div id="top-menu" class="hidden-xs hidden-sm">
	<div class="container">
		<div class="row">
			<div class="col-md-6 windowsfuck">
				<a href="/" class="clear-list"><i class="spi spi-topbar_home"></i></a>
				<a href="http://magazine.notefolio.net" target="_blank" class="ml"><i class="spi spi-topbar_magazine"></i></a>
				<!-- <a href="http://shop.notefolio.net" class="ml"><i class="spi spi-topbar_shop"></i></a> -->
			<!--</div>
			<div class="col-md-6 righted windowsfuck">
				<?php if (USER_ID==0): ?>
					<a href="/auth/register"><i class="spi spi-topbar_register"></i></a> 
				<?php else: ?>				
					<a href="/auth/setting"><i class="spi spi-tobar_setting"></i></a>
					<a href="/auth/logout" class="ml"><i class="spi spi-topbar_logout"></i></a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>-->
<div id="header-gap" class="visible-md visible-lg">&nbsp;</div>
<header id="header" class="hidden-xs hidden-sm">
	<div class="container">
		<div class="row">
			<div class="col-md-6">
				<ul class="list-inline">
					<li>
						<a href="/" class="clear-list">
							<i class="spi spi-nflogo" style="margin-top:-2px;"></i>
						</a>
					</li>
					<li style="margin-left: 26px;">
						<a class="btn btn-link clear-list" href="/gallery/listing" style="font-size:16px;font-family:'Roboto',sans-serif;color:#444;font-weight:400;">Works</a>
					</li>
					<li style="margin-left: 14px;">
						<a class="btn btn-link clear-list" href="http://magazine.notefolio.net" target="_blank" style="font-size:16px;font-family:'Roboto',sans-serif;color:#444;font-weight:400;">Magazine</a>
					</li>
					<li style="margin-left: 14px;">
						<a class="btn btn-link clear-list" href="http://dotdotdot.co.kr" target="_blank" style="font-size:16px;font-family:'Roboto',sans-serif;color:#444;font-weight:400;">Shop</a>
						<span class="label label-nofol rounded" style="display: inline-block; font-weight: 300;margin: -14px; background-color: #bb9545">new</span>
						
					</li>
				</ul>
			</div>

			<div class="col-md-6 righted" style="padding-top: 3px;">
				<?php if (USER_ID==0): ?>
					<a class="btn btn-nofol btn-no-border" href="#" id="login-with-fb" style="font-size:16px;font-weight:300;margin-top:6px;">
						<i class="spi spi-fb" style="margin-top:-1px;">fb</i> Login with facebook
					</a>
					<a class="btn btn-nofol btn-no-border" href="/auth/login" style="font-size:16px;font-weight:300;margin-top:-5px;padding:6px 0px 6px 12px;">
						<i class="spi spi-user" style="margin-top:-2px;">user</i>&nbsp;&nbsp;Login
					</a>
					<a class="btn btn-nofol btn-no-border" href="/auth/register" style="font-size:16px;font-weight:300;margin-top:-5px;padding:6px 0px 6px 12px;">
						<i class="spi spi-signup" style="margin-top:-2px;">signup</i>&nbsp;&nbsp;Sign up
					</a>
				<?php else: ?>
					<span id="alarm-wrapper">
						<a id="btn-alarm" href="javascript:;" class="btn-nofol btn-no-border">
							<i class="spi spi-alarm"></i>
							<span class="label label-nofol rounded unread-alarm"></span>
						</a>
					</span>
					<a id="btn-feed" href="/feed/listing" class="btn-nofol btn-no-border">
						<i class="spi spi-feed"></i>
						<span class="label label-nofol rounded unread-feed"></span>
					</a>

					<a class="btn btn-nofol btn-no-border" href="/gallery/create" style="font-family:'Roboto',sans-serif;font-weight:300;font-size:16px;color:#6d6e71;"> <!--padding:10px 12px 6px;-->
						<i class="spi spi-plus2" style="margin-top:0px;"></i> Upload Work
					</a>
					<!--<a id="btn-profile" href="/<?php echo $this->session->userdata('username') ?>">
						<div id="btn-profile-icon">
							<img class="icon-round" src="/data/profiles/<?php echo $this->session->userdata('username') ?>_face.jpg?_=<?php echo substr($this->nf->get('user')->modified,-2) ?>" onerror="this.src='/img/default_profile_face.png'"/>
							<!--[if lte IE 9]><i class="si si-face-medium"></i><![endif]-->
						<!--</div>
						<!--<button type="button" class="btn btn-default btn-xs dropdown-toggle btn-profilemini" data-toggle="dropdown">
						  <div id="btn-profile-icon">
							<img class="icon-round" src="/data/profiles/<?php echo $this->session->userdata('username') ?>_face.jpg?_=<?php echo substr($this->nf->get('user')->modified,-2) ?>" onerror="this.src='/img/default_profile_face.png'"/>
							</div>
						  <span class="caret"></span>
						</button>
						<ul class="dropdown-menu">
						  <li><a href="/<?php echo $this->session->userdata('username')?>">My notefolio</a></li>
						  <li><a href="/auth/setting">setting</a></li>
						  <li><a href="/auth/logout">logout</a></li>
						</ul>-->
						<!--<span><?php echo $this->nf->get('user')->realname; ?></span>
					</a>-->
					<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" style="border-color: #fff !important; ">
						  <div id="btn-profile-icon">
							<img class="icon-round" src="/data/profiles/<?php echo $this->session->userdata('username') ?>_face.jpg?_=<?php echo substr($this->nf->get('user')->modified,-2) ?>" onerror="this.src='/img/default_profile_face.png'"/>
						 </div>
						  <a id="btn-profile" style="float: left!important" href="/<?php echo $this->session->userdata('username') ?>"><span><?php echo $this->nf->get('user')->realname; ?></span></a>
						  
						</button>
						<ul class="dropdown-menu" style="left: 79%;">
						  <li><a href="/<?php echo $this->session->userdata('username')?>">My notefolio</a></li>
						  <li><a href="/auth/setting">setting</a></li>
						  <li><a href="/auth/logout">logout</a></li>
						</ul>
						 
						  <div class="caret"></div>
				<?php endif ?>
			</div>

		</div>
	</div>
</header>

<div id="content-wrap">
	<div id="mobile-header" class="mm-fixed-top visible-xs visible-sm">
		<div class="container">
			<div class="row">
				<div class="col-xs-2">
					<a id="mobile-menu-open" href="#">
						<i class="spi spi-menu"></i>
					</a>
				</div>
				<div class="col-xs-8 centered">
					<a href="/"><i class="spi spi-nflogo"></i></a>
				</div>
				<div class="col-xs-2 righted">
					<?php 
						$button['random'] = in_array($this->uri->rsegment(1), array('gallery', 'main'));
						$button['follow'] = in_array($this->uri->rsegment(1), array('profile'));
						if($this->uri->rsegment(1)=='gallery' && $this->uri->rsegment(2)=='info'){
							$button['follow'] = true;
							$button['random'] = false;
						}
					?>
					<?php if($button['random']): // 여기가 메인이나 겔러리이면 ?> 
						<a href="/gallery/random"><em class="pi pi-random">random</em></a>	<!-- 랜덤페이지로 -->
					<?php elseif($button['follow']): // 여기가 프로필페이지나 특정 작가의 상세정보페이지라면 ?>
						<?php if ($this->session->userdata('username')!=$profile['username']): // 작가 본인이 아닐 때에만 ?>
							<a id="mobile-header-follow" href="javascript:;" data-id="<?php echo $profile['user_id'] ?>" class="btn btn-follow <?=($profile['is_follow']=='y')?'activated':''?>" style="padding: 0;margin-top: -1px;"><i class="spi spi-following"></i><i class="spi spi-follow"></i></a> <!-- 이 작가의 팔로우 버튼 -->
						<?php endif ?>
					<?php endif;?>
				</div>
			</div>
		</div>
	</div> 