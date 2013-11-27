<?php
?>
<section>
	<div class="panel panel-danger">
		<div class="panel-heading">
		  <h3 class="panel-title">접근 금지</h3>
		</div>
		<div class="panel-body">
		  <p class="lead">
		  	이 페이지를 보고 계신다면 잘못 접속하셨을 수 있습니다.<br />
		  	만약 의도하지 않은 접근이라면 아래 버튼을 눌러 이동하실 수 있습니다.
		  </p>
		  <p>&nbsp;</p>
		  <p class="small">
		  	혹시 정상적으로 접근했는데도 이 페이지가 표시된다면, 저희에게 <a href="/info/contact_us">알려주세요</a>.
		  </p>
		</div>							
		<span class="btn btn-primary" id="goto-main">Main</span>
		<span class="btn btn-primary" id="goto-gallery">Gallery</span>
	</div>			
	
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
