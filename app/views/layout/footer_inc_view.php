<section>
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<ul class="nav nav-pills pull-right">
					<li id="profile_nav_followings">
						<a href="/<?php echo $row->username ?>/followings">followings(<?php echo number_format($total->following_cnt) ?>)</a>
					</li>
					<li id="profile_nav_followers">
						<a href="/<?php echo $row->username ?>/followers">followers(<?php echo number_format($total->follower_cnt) ?>)</a>
					</li>
				</ul>
				<div class="clearfix visible-xs"></div>
				<ul id="profile-nav" class="nav nav-pills">
					<li id="profile_nav_">
						<a href="/<?php echo $row->username ?>">
							<i class="spi spi-check">check</i><span class="text"> Works</span><span class="number">234</span>
						</a>
					</li>
					<li id="profile_nav_collect">
						<a href="/<?php echo $row->username ?>/collect">
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