<div id="top-menu" class="hidden-xs hidden-sm">
	<div class="container">
		<div class="row">
			<div class="col-md-6">
				<a href="/" class="clear-list"><i class="spi spi-topbar_home"></i></a>
				<a href="http://magazine.notefolio.net" class="ml"><i class="spi spi-topbar_magazine"></i></a>
				<a href="http://shop.notefolio.net" class="ml"><i class="spi spi-topbar_shop"></i></a>
			</div>
			<div class="col-md-6 righted">
				<?php if (USER_ID==0): ?>
					<a href="/auth/register"><i class="spi spi-topbar_register"></i></a> 
				<?php else: ?>				
					<a href="/auth/setting"><i class="spi spi-tobar_setting"></i></a>
					<a href="/auth/logout" class="ml"><i class="spi spi-topbar_logout"></i></a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
<header id="header" class="hidden-xs hidden-sm sticky">
	<div class="container">
		<div class="row">
			<div class="col-md-5">
				<ul class="list-inline">
					<li>
						<a href="/" class="clear-list">
							<i class="spi spi-nflogo" style="margin-top:-2px;"></i>
						</a>
					</li>
					<li style="margin-left: 32px;">
						<a class="btn btn-link clear-list" href="/gallery/listing" style="font-size:18px;font-family:'Roboto',sans-serif;color:#6d6e71;font-weight:400;">Gallery</a>
					</li>
				</ul>
			</div>

			<div class="col-md-7 righted">
				<?php if (USER_ID==0): ?>
					<a class="btn btn-nofol btn-no-border" href="#" id="login-with-fb" style="font-size:16px;font-weight:300;margin-top:6px;">
						<i class="spi spi-fb" style="margin-top:-2px;">fb</i> Login with facebook
					</a>
					<a class="btn btn-nofol btn-no-border" href="/auth/login" style="font-size:16px;font-weight:300;margin-top:5px;padding:6px 0px 6px 12px;">
						<i class="spi spi-user" style="margin-top:-2px;">user</i>&nbsp;&nbsp;Login
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

					<a class="btn btn-nofol btn-no-border" href="/gallery/create" style="font-family:'Roboto',sans-serif;font-weight:300;font-size:16px;color:#6d6e71;padding:10px 12px 6px;">
						<i class="spi spi-plus2" style="margin-top:-1px;"></i> Upload work
					</a>
					<a id="btn-profile" href="/<?php echo $this->session->userdata('username') ?>">
						<div id="btn-profile-icon">
							<img src="/data/profiles/<?php echo $this->session->userdata('username') ?>_face.jpg" alt=""/>
							<i class="si si-face-medium"></i>
						</div>
						<span><?php echo $this->session->userdata('username'); ?></span>
					</a>
				<?php endif ?>
			</div>

		</div>
	</div>
</header>

<nav id="mobile-menu" class="hidden-md hidden-lg">

	<ul>
		<li>
			<form action="gallery/listing/1" role="form">
				<div class="input-group input-group-md">
					<input type="search" name="q" placeholder="search" class="form-control">
					<div class="input-group-btn">
						<button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search"></i></button>
					</div>					
				</div>
			</form> 
		</li>
		<?php if (USER_ID==0): ?>
			<li>
				<a href="/auth/login">Login</a>
			</li>
			<li>
				<a href="/auth/register">Register</a>
			</li>
		<?php else: ?>
			<li id="mobile-menu-profile" class="centered">
				<a href="/<?php echo $this->nf->get('user')->username ?>">
					<img src="/data/profiles/<?php echo $this->nf->get('user')->username ?>_face.jpg?_=<?php echo substr($this->nf->get('user')->modified,-2) ?>" alt="" onerror="this.src='/img/default_profile_face.png'">
				</a>
				<?php echo $this->nf->get('user')->username ?>
				<br/>
				followers <?php echo $this->nf->get('user')->following_cnt ?>
				/
				followings <?php echo $this->nf->get('user')->follower_cnt ?>
			</li>
			<li>
				<a class="clear-list" href="/alarm/listing">
					Alarm
					<span class="label label-danger rounded unread-alarm"></span>
				</a>
			</li>
			<li>
				<a class="clear-list" href="/feed/listing">
					Feed
					<span class="label label-danger rounded unread-feed"></span>
				</a>
			</li>

			<li>
				<a class="clear-list" href="/<?php echo $this->session->userdata('username') ?>/myworks">My works</a>
			</li>
			<li>
				<a class="clear-list" href="/<?php echo $this->session->userdata('username') ?>/statistics">Statistics</a>
			</li>
			<li>
				<a href="/auth/setting">Settings</a>
			</li>
			<li id="mobile-menu-signout">
				<a href="btn btn-default btn-lg">
					Sign out
				</a>
			</li>
		<?php endif; ?>

	</ul>

</nav>
<div id="content-wrap">
	<div id="mobile-header" class="mm-fixed-top visible-xs visible-sm">
		<div class="container">
			<div class="row">
				<div class="col-xs-2">
					<a href="javascript:$('#mobile-menu').trigger('open');">
						<i class="spi spi-feed"></i>
					</a>
				</div>
				<div class="col-xs-8 centered">
					<a href="/"><i class="spi spi-nflogo"></i></a>
				</div>
				<div class="col-xs-2 righted">
					<a href="/gallery"><i class="spi spi-feed"></i></a>
				</div>
			</div>
		</div>
	</div> 