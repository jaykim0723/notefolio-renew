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
						<a class="btn btn-link clear-list" href="/gallery/listing" style="font-size:18px;font-family:'Roboto',sans-serif;color:#444;font-weight:400;">Gallery</a>
					</li>
				</ul>
			</div>

			<div class="col-md-7 righted">
				<?php if (USER_ID==0): ?>
					<a class="btn btn-nofol btn-no-border" href="#" id="login-with-fb" style="font-size:16px;font-weight:300;margin-top:6px;">
						<i class="spi spi-fb" style="margin-top:-1px;">fb</i> Login with facebook
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