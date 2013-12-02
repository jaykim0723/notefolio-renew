<header id="header" class="navbar-fixed-top visible-md visible-lg">
	<div class="container">
		<div class="row">
			<div class="col-md-5">
				<ul class="list-inline">
					<li>
						<a href="/">Main</a>
					</li>
					<li>
						<a href="/gallery/listing">Gallery</a>
					</li>
				</ul>
			</div>

			<div class="col-md-4">
				<?php if (USER_ID==0): ?>
					<a href="/auth/login">Login</a>
					<a href="/auth/register">Register</a>
				<?php else: ?>
					<ul class="list-inline">
						<li>
							<a href="/feed/listing">Feed</a>
						</li>
						<li id="alarm-wrapper">
							<a id="btn-alarm" href="javascript:;">
								Alarm
								<span class="label label-default unread-alarm"></span>
							</a>
						</li>
						<li class="btn-group">
							<button class="btn btn-default" id="btn-profile">
								<?php echo $this->session->userdata('username') ?>
							</button>
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
								<span class="caret"></span>
								<span class="sr-only">Toggle Dropdown</span>
							</button>
							<ul class="dropdown-menu" role="menu">
								<li>
									<a href="/gallery/create">
										<i class="glyphicon glyphicon-circle-arrow-up"></i>
										Upload work
									</a>
								</li>
								<!-- <li><a href="/<?php echo $this->session->userdata('username') ?>">My Profile</a></li> -->
								<li>
									<a href="/auth/setting">
										<i class="glyphicon glyphicon-cog"></i>
										Setting
									</a>
								</li>
								<li>
									<a href="/auth/logout">
										<i class="glyphicon glyphicon-arrow-right"></i>
										Logout
									</a>
								</li>
							</ul>
							<script>
								$('#btn-profile').on('click', function(){
									site.redirect(site.url+site.username);
								})
							</script>
						</li>
					</ul>
				<?php endif ?>
			</div>

			<div class="col-md-3">
				<form action="gallery/listing/1" role="form">
					<div class="input-group input-group-md">
						<input type="search" name="q" placeholder="search" class="form-control">
						<div class="input-group-btn">
							<button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search"></i></button>
						</div>					
					</div>
				</form>
			</div>			

		</div>
	</div>
</header>

<nav id="mobile-menu" class="hidden-md hidden-lg">
	<ul>
		<?php if (USER_ID==0): ?>
			<li>
				<a href="/auth/login">Login</a>
			</li>
			<li>
				<a href="/auth/register">Register</a>
			</li>
		<?php else: ?>
			<li>
				<a href="/feed/listing">Feed</a>
			</li>
			<li>
				<a href="/alarm/listing">
					Alarm
					<span class="label label-default unread-alarm"></span>
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
		<?php endif; ?>

	</ul>
</nav>
<div id="content-wrap">
	<div id="mobile-header" class="mm-fixed-top visible-xs visible-sm">
		<div class="container">
			<a href="javascript:$('#mobile-menu').trigger('open');">open</a>
			|
			<a href="/">main</a>
			|
			<a href="/gallery">Gallery</a>
		</div>
	</div>