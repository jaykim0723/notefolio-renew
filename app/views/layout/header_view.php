<header id="header" class="navbar-fixed-top visible-md visible-lg">
	<div class="container">
		<div class="row">
			<div class="col-md-12">

				<div class="pull-right btn-group">
					<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
						<?php if (USER_ID==0): ?>
							Proferences
						<?php else: ?>
							<?php echo $this->session->userdata('username') ?>
						<?php endif ?>
					<span class="caret"></span>
					</button>
					<ul class="dropdown-menu">
						<li><a href="/gallery/create">Upload work</a></li>
						<li><a href="/<?php echo $this->session->userdata('username') ?>">My Profile</a></li>
						<li><a href="#">Setting</a></li>
						<li>
							<?php if (USER_ID==0): ?>
								<a href="/auth/login">Login</a>
							<?php else: ?>
								<a href="/auth/logout">Logout</a>
							<?php endif ?>
						</li>
					</ul>
				</div>

				<ul class="list-inline">
					<li>
						<a href="/">Main</a>
					</li>
					<li>
						<a href="/gallery/listing">Gallery</a>
					</li>
					<li>
						<a href="/feed/listing">Feed</a>
					</li>
					<li>
						<a href="/alarm/listing">Alarm</a>
					</li>
				</ul>
			</div>
		</div>
	</div>
</header>
<nav id="mobile-menu" class="hidden-md hidden-lg">
	<ul>
		<li>
			<a href="#">Note</a>
		</li>
		<li>
			<a href="#">Statistics</a>
		</li>
		<li>
			<a href="#">My works</a>
		</li>
		<li>
			<a href="#">Settings</a>
		</li>
	</ul>
</nav>
<div id="content-wrap">
	<header id="mobile-header" class="mm-fixed-top visible-xs visible-sm">
		<div class="container">
			<a href="javascript:$('#mobile-menu').trigger('open');">open</a>
			|
			<a href="/">main</a>
			|
			<a href="/gallery">Gallery</a>
		</div>
	</header>