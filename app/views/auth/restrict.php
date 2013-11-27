<?php
?>
<section>
	<div class="panel panel-danger">
		<div class="panel-heading">
		  <h3 class="panel-title">접근 금지</h3>
		</div>
		<div class="panel-body">
		  <p class="lead">
		  	이 페이지는 올바르지 않게 이용할 때 표시됩니다.<br />
		  	올바른 방법으로 페이지에 접근해 주십시오.
		  </p>
		  <p>&nbsp;</p>
		  <p class="small">
		  	올바르게 접속했는데도 이 페이지가 표시된다면, 저희에게 <a href="/info/contact_us">알려주세요</a>.
		  </p>
		</div>
	</div>							
	
	<span class="btn btn-primary" id="goto-main">Main</span>
	<span class="btn btn-primary" id="goto-gallery">Gallery</span>			
	
</section>

<script>
    $('#goto-main').on('click',function(e){
        e.preventDefault();
        window.location.href = '/';
    });
    $('#goto-gallery').on('click',function(e){
        e.preventDefault();
        window.location.href = '/gallery/';
    });
</script>
