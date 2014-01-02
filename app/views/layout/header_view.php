<div id="top-menu" class="hidden-xs hidden-sm">
	<div class="container">
		<div class="row">
			<div class="col-md-6">
				<a href="/"><i class="spi spi-topbar_home"></i></a>
				<a href="http://magazine.notefolio.net" class="ml"><i class="spi spi-topbar_magazine"></i></a>
				<a href="http://shop.notefolio.net" class="ml"><i class="spi spi-topbar_shop"></i></a>
			</div>
			<div class="col-md-6 righted">
				<?php if (USER_ID==0): ?>
					<a href="/auth/login"><i class="spi spi-topbar_login"></i></a> 
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
						<a href="/">
							<i class="spi spi-nflogo"></i>
						</a>
					</li>
					<li>
						<a class="btn btn-link" href="/gallery/listing"><i class="spi spi-gallery"></i></a>
					</li>
				</ul>
			</div>

			<div class="col-md-7 righted">
				<?php if (USER_ID==0): ?>
					<a class="btn btn-nofol btn-hover bg1" href="/auth/login">
						<i class="spi spi-signupfb"></i> Login with Facebook
					</a>
					<a class="btn btn-nofol btn-hover bg2" href="/auth/register">
						<i class="spi spi-signup"></i> Sign Up
					</a>
				<?php else: ?>
					<a id="btn-feed" href="/feed/listing" class="btn-hover">
						<i class="spi spi-feed"></i>
						<span class="label label-nofol rounded unread-feed"></span>
					</a>
					<span id="alarm-wrapper">
						<a id="btn-alarm" href="javascript:;" class="btn-hover">
							<i class="spi spi-alarm"></i>
							<span class="label label-nofol rounded unread-alarm"></span>
						</a>
					</span>

					<a class="btn btn-nofol btn-hover" href="/gallery/create">
						<i class="spi spi-uploadworks"></i> Upload work
					</a>
					<a id="btn-profile" href="/<?php echo $this->session->userdata('username') ?>">
						<div id="btn-profile-icon">
							<img src="/data/profiles/<?php echo $this->session->userdata('username') ?>.jpg" alt=""/>
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
				<a href="/<?php echo $this->session->userdata('username') ?>">
					<img src="http://notefolio.net/profiles/147?h=1385655105" alt="">
				</a>
			</li>
			<li>
				<a href="/feed/listing">
					Feed
					<span class="label label-danger rounded unread-feed"></span>
				</a>
			</li>
			<li>
				<a href="/alarm/listing">
					Alarm
					<span class="label label-danger rounded unread-alarm"></span>
				</a>
			</li>
			<li>
				<a href="/<?php echo $this->session->userdata('username') ?>/myworks">My works</a>
			</li>
			<li>
				<a href="/<?php echo $this->session->userdata('username') ?>/statistics">Statistics</a>
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