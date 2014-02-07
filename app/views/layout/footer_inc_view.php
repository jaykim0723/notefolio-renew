<nav id="mobile-menu" class="hidden-md hidden-lg">

	<ul>
		<li>
			<form action="gallery/listing/1" role="form">
				<div id="mobile-search-box">
					<i class="spi spi-search_white"></i>
					<input type="search" name="q" placeholder="search" id="mobile-search-field">
				</div>
			</form> 
		</li>
		<?php if (USER_ID>0): ?>
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
					<i class="spi spi-alarm_white"></i>
					Alarm
					<span class="label rounded unread-alarm"></span>
				</a>
			</li>
			<li>
				<a class="clear-list" href="/feed/listing">
					<i class="spi spi-feed_white"></i>
					Feed
					<span class="label rounded unread-feed"></span>
				</a>
			</li>
		<?php endif; ?>
			<li>
				<a class="clear-list" href="/gallery/listing">
					<i class="spi spi-gallery_white"></i>
					Gallery
				</a>
			</li>
			<li>
				<a class="clear-list" href="//magazine.notefolio.net/">
					<i class="spi spi-magazine_white"></i>
					Magazine
				</a>
			</li>
			<li>
				<a class="clear-list" href="/info/about_us/">
					<i class="spi spi-info_white"></i>
					Notefolio info
				</a>
			</li>
		<?php if (USER_ID==0): ?>
			<li>
				<a href="/auth/login">
					<i class="spi spi-login_white"></i>
					Login
				</a>
			</li>
			<li>
				<a href="/auth/register">
					<i class="spi spi-plus2_white"></i>
					Register
				</a>
			</li>
		<?php endif; ?>
		<?php if (USER_ID>0): ?>
			<li>
				<a href="/auth/logout">
					<i class="spi spi-logout_white"></i>
					Sign Out
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