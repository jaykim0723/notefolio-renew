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
				<p>개인 정보를 변경하였습니다. <a id="status-close" href="javascript:$('#status').remove();">닫기</a></p>
			</div>
			<script type="text/javascript">
				$('a#status-close').on('click', function(e){ e.preventDefault(); e.stopPropagation(); javascript:$('#status').remove(); });
			</script>
		</div>
	</div>

	<?php
		}
	?>
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
		e.preventDefault();
		e.stopPropagation();
		
		document.register_form.submit();
	});
	
	$(function(){
		$('input[type="text"]','#form-username').on('keyup keypress blur change', function(){
			var val = $(this).val();
			$.post('/auth/check_username_available', {username: val}, function(data, textStatus, xhr) {
                var response = $.parseJSON(data);
                if(response.status=='done'){
                	$('#form-username').removeClass('error');
                	$('.form-error','#form-username').text('');
                }else{
                	$('#form-username').addClass('error');
                	$('.form-error','#form-username').text('↑ '+response.error);
                }
				
			});
			$('span.example', '#form-username').text(val);
		});
	});
</script>