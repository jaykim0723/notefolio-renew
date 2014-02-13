</div>
<footer id="footer" class="navbar-fixed-bottom visible-md visible-lg">
	<div class="container">
		<div class="row">
			<div id="footer-menu" class="col-md-10">
				&copy;
				Copyright 노트폴리오
				<a href="/info/about_us">About Us</a>
				<a href="/info/contact_us">Contact Us</a>
				<a href="/info/faq">FAQ</a>
				<a class="admin-none" href="/info/privacy">Privacy Policy</a>
				<a class="admin-none" href="/info/terms">Terms of use</a>
			</div>
			<div id="footer-gap" class="col-md-4">
			</div>
			<div class="col-md-2" style="padding-top:3px;">
				<a href="http://twitter.com/notefoliotwt"><img src="http://magazine.notefolio.net/theme_assets/img/twitter.png" style="width:20px;height:20px;">
				<span class="footerlink">twitter</span></a>
				<a href="http://notefolio.tistory.com/"><img src="http://magazine.notefolio.net/theme_assets/img/blog.png" style="width:20px;height:20px;">
				<span class="footerlink">blog</span></a>
				<a href="https://www.facebook.com/notefolio"><img src="http://magazine.notefolio.net/theme_assets/img/facebook.png" style="width:20px;height:20px;margin-left:15px;">
				<span class="footerlink">facebook</span></a>
			</div>
		</div>
	</div>
</footer>

<?php if($this->tank_auth->get_user_level()==9): ?>
	<!-- 관리자모드 발동 -->
	<script src="/js/admin.js"></script>
	<script>
		adminMenu.initBottom();
	</script>
<?php endif; ?>

<script>
	$(function(){
		site.alarm.checkUnread();
	});
</script>

<script>
    $('#login-with-fb').on('click',function(e){
        e.preventDefault();
        var fb_diag = window.open('<?=$this->config->item('base_url')?>fbauth/login/externel','fb_diag','width=600,height=300,scrollbars=yes,resizable=no');
        fb_diag.focus();
        //$.fn.dialog2.helpers.alert("현재 준비중입니다.");
    });
</script>

<div id="loading-indicator">
	<img src="/img/ajax-loader.gif" alt=""/>
	Loading...
</div>

<nav id="mobile-menu" class="hidden-md hidden-lg">

	<ul>
		<li>
			<form action="/gallery/listing/" role="form">
				<div id="mobile-search-box">
					<i class="spi spi-search_white"></i>
					<input type="search" name="q" placeholder="Search" id="mobile-search-field">
				</div>
			</form> 
		</li>
		<?php if (USER_ID>0): ?>
		<li id="mobile-menu-profile" class="centered">
			<a href="/<?php echo $this->nf->get('user')->username ?>">
				<img src="/data/profiles/<?php echo $this->nf->get('user')->username ?>_face.jpg?_=<?php echo substr($this->nf->get('user')->modified,-2) ?>" alt="" onerror="this.src='/img/default_profile_face.png'">
			</a>
			<p class="username"><a href="/<?php echo $this->nf->get('user')->username ?>"><?php echo $this->nf->get('user')->realname ?></a></p>
			<p class="follows">
				<a href="/<?php echo $this->nf->get('user')->username ?>/followers">
					<span class="count"><?php echo $this->nf->get('user')->following_cnt ?></span>
					followers 
				</a>
				<a href="/<?php echo $this->nf->get('user')->username ?>/followings">
					<span class="count"><?php echo $this->nf->get('user')->follower_cnt ?></span>
					followings 
				</a>
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
		<li class="commonmenu">
			<a href="/auth/setting">
				<i class="spi spi-setting_white"></i>
				Setting
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
			<a class="clear-list" href="//magazine.notefolio.net/" target="_blank">
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
		<li class="guestmenu">
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
		<li class="guestmenu">
			<a href="/auth/logout">
				<i class="spi spi-logout_white"></i>
				Sign Out
			</a>
		</li>
		<?php endif; ?>

	</ul>
</nav>