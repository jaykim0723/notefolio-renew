
<form action='/auth/setting' id='register' name='register_form' method='post'>

	<?php echo validation_errors();  ?>	
	<?php
		$this->load->view('profile/setting/basic_view');
        $this->load->view('profile/setting/fb_view');
	?>

	<!--
	<a href='/auth/unregister' class='underline'>회원탈퇴</a>
	-->
	
	<input type='hidden' name='submitting' value='1'/>

	<div class='center'>
		<br/>
		<a id='btnSubmit' class='pure-button-big pure-button'>Submit</a>
	</div>

</form>

<script>
	var workSpace = 'setting';
	
	$('#btnSubmit').click(function(e){ // submit클릭에 의해서만 전송된다.
		// stage4(profile)에서 전송된 경우이다.
		
		check_profile();
		check_keywords();
		check_basic();
		
		// 에러난 것이 있다면
		if($('span.error').length > 0){
			$('span.error').eq(0).parents('.control-group').find('input:first').focus(); // 에러난 필드 중 가장 처음 것을 선택한다.
			msg.error('한번 더 확인해주세요.');
			return false;
		}

		// 마지막 단계에서 마지막 스테이지가 에러가 없다면, 모두 에러가 없다고 가정한다.
		// 폼을 전송한다.
		document.register_form.submit();
	});

	$('*[rel=tooltip]').tooltip({
		placement : 'bottom',
		trigger : 'focus'
	});
	
</script>