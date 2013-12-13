<div id="top_menu" class="hidden-xs hidden-sm">
	<div class="container">
		<div class="row">
			<div class="col-md-6">
				<a href="/">Home</a>
				|
				<a href="http://magazine.notefolio.net">Magazine</a>
				|
				<a href="http://shop.notefolio.net">Shop</a>
			</div>
			<div class="col-md-6 righted">
				<?php if (USER_ID==0): ?>
					<a href="/auth/login">login</a>
				<?php else: ?>				
					<a href="/auth/setting">setting</a>
					|
					<a href="/auth/logout">logout</a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
<header id="header" class="hidden-xs hidden-sm sticky">
	<div class="container">
		<div class="row">
			<div class="col-md-6">
				<ul class="list-inline">
					<li>
						<a href="/">
							<i class="spi spi-nflogo"></i>
						</a>
					</li>
					<li>
						<a class="btn btn-link" href="/gallery/listing">Gallery</a>
					</li>
				</ul>
			</div>

			<div class="col-md-6 righted">
				<?php if (USER_ID==0): ?>
					<a class="btn btn-primary" href="/auth/login">Login with Facebook</a>
					<a class="btn btn-info" href="/auth/register">Sign Up</a>
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
					<a class="btn btn-default" href="/<?php echo $this->session->userdata('username') ?>">My Profile</a>
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
						<i class="spi spi-drowdown"></i>
					</a>
				</div>
				<div class="col-xs-8 centered">
					<a href="/"><i class="spi spi-home"></i></a>
				</div>
				<div class="col-xs-2 righted">
					<a href="/gallery"><i class="spi spi-drowdown"></i></a>
				</div>
			</div>
		</div>
	</div>