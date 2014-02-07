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
				<p class="username"><?php echo $this->nf->get('user')->username ?></p>
				<p class="follows">
					<span class="count"><?php echo $this->nf->get('user')->following_cnt ?></span>
					followers 
					<span class="count"><?php echo $this->nf->get('user')->follower_cnt ?></span>
					followings 
				</p>

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

<script src="/js/libs/bootstrap.min.js"></script>
<script src="/js/libs/bootstrap-dialog.js"></script>
<script src="/js/libs/bootstrap-select.js"></script>
<script src="/js/libs/jquery-ui-view-1.10.4.custom.min.js"></script>
<script src="/js/libs/fileuploader.js"></script>
<script src="/js/libs/jquery-ajax-uploader.js"></script>
<script src="/js/libs/waypoints.js"></script>
<script src="/js/libs/waypoints-infinite.js"></script>
<script src="/js/libs/waypoints-sticky.js"></script>
<script src="/js/libs/jquery.history.js"></script>
<script src="/js/libs/jquery.hammer.min.js"></script>
<script src="/js/libs/jquery.mmenu.min.all.js"></script>
<!-- <script src="/js/libs/dropzone.min.js"></script>
<script src="/js/libs/dropzone.dict-ko.js"></script>
 -->
<script>
	$('select:not(.no-jquery)').selectpicker();
</script>

</body>
</html>