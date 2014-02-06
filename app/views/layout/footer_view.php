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
			<div class="col-md-2" style="padding-top:2px;">
				<img src="http://magazine.notefolio.net/theme_assets/img/blog.png" style="width:20px;height:20px;margin-top:1px;">
				<span class="footerlink">blog</span>
				<img src="http://magazine.notefolio.net/theme_assets/img/facebook.png" style="width:20px;height:20px;margin-top:1px;">
				<span class="footerlink">facebook</span>
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