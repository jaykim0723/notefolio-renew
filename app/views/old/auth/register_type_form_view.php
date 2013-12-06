<h1>회원가입 방법 선택</h1>

<br/><br/>

<?php
    $ci = get_instance();
	echo form_open('/auth/register', array('method'=>'post', 'id'=>'register_type', 'class'=>'center'));
?>
<a href="#" id='register_with_facebook'>
	<img src="/images/signup/signup-email_09.jpg" width="319" height="35" rel='tooltip' title="페이스북 사용자인 경우 더욱 간소하게 가입할 수 있습니다.">
</a>



<img src="/images/signup/signup-email_11.jpg" width="319" height="90">

<a href="#" id='register_with_email'>
	<img src="/images/signup/signup-email_12.jpg" width="319" height="37" rel='tooltip' title="E-mail로 가입할 수 있습니다.">
</a>

<input type='hidden' name='register_type' value=''/>
<input type='hidden' name='invite_code' value="<?php echo $invite_code?>"/>
<input type='hidden' name='step' value='2'/>

<?php echo form_close(); ?>

<script>
	$('#register_with_facebook').click(function(){
        var fb_diag = window.open('<?=$ci->config->item('base_url')?>auth/fb/link/for-register','fb_diag','width=600,height=300,scrollbars=yes,resizable=no');
        fb_diag.focus();
		//$.fn.dialog2.helpers.alert("현재 준비중입니다.");
	});
	$('#register_with_email').click(function(){
		var f = $('#register_type');
		$('input[name=register_type]', f).val('email');
		f.submit();
	});
	
	$('*[rel=tooltip]').tooltip({
		placement : 'bottom'
	});		

</script>