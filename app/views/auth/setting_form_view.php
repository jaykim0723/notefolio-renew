<?php
echo form_open('', array(
	'name' => 'register_form',
	'role' => 'form',
	'id'   => 'register'
), array(
	'go_to'      => '', // $site_to ???
	'submitting' => 1
));
?>
	<?php
		if($this->input->post('submitting')==1){
	?>

	<div class="center" id="status">
		<div class="panel panel-success">
			<div class="panel-heading">
			  <h3 class="panel-title"><i class="spi spi-check_white">check_white</i><br><br>정보 변경 완료</h3>
			</div>
			<div class="panel-body">
				<p>개인 정보를 변경하였습니다. <a href="javascript:$('#status').remove();">닫기</a></p>
			</div>
		</div>
	</div>

	<?php
		}
	?>
	<?php echo validation_errors();  ?>	
	<?php
		$this->load->view('auth/setting/basic_view');
        $this->load->view('auth/setting/fb_view');
	?>

	<!--
	<a href='/auth/unregister' class='underline'>회원탈퇴</a>
	-->

	<div class='center'>
		
		<button type="submit" id='btnSubmit' class='btn btn-block btn-darkgray pure-button-big pure-button'>Submit</button>
	</div>

<?php echo form_close(); ?>

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
	
</script>