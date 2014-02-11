<?php
$login = array(
	'name'	=> 'login',
	'id'	=> 'login',
	'value' => set_value('login'),
	'maxlength'	=> 80,
	'size'	=> 30,
	'class' => 'form-control',
	'autofocus' => 'autofocus',
	'placeholder' => 'email'

);
if ($login_by_username AND $login_by_email) {
	$login_label = 'Email';
} else if ($login_by_username) {
	$login_label = 'Login';
} else {
	$login_label = 'Email';
}
$password = array(
	'name'	=> 'password',
	'id'	=> 'password',
	'size'	=> 30,
	'class' => 'form-control',
	'placeholder' => 'password'
);
$remember = array(
	'name'	=> 'remember',
	'id'	=> 'remember',
	'value'	=> 1,
	'checked'	=> set_value('remember'),
	'style' => 'margin:0;padding:0',
);
$captcha = array(
	'name'	=> 'captcha',
	'id'	=> 'captcha',
	'maxlength'	=> 8,
);

if ($this->input->get('go_to')) {
    $site_to = $this->input->get('go_to');
} else if ($go_to) {
    $site_to = $go_to;
} else {
    $site_to = $this->input->server('HTTP_REFERER');
}
?>

<section>
	<?php if(isset($auth_error)){?>
	<!--
	<div class="center" id="status">
		<div class="panel panel-warning">
			<div class="panel-heading">
			  <h3 class="panel-title">확인이 필요합니다</h3>
			</div>
			<div class="panel-body">
				<p>필요한 정보를 잘못 입력하셨습니다.
					<br/>가입하지 않았다면, <a href="/auth/register">지금 가입하세요</a>.
					<br/><a href="javascript:$('#status').remove();">닫기</a></p>
			</div>
		</div>
	</div>
	-->
	<?php } ?>				
	<?php echo form_open($this->uri->uri_string(), array('role'=>'form','id'=>'login-form'), array(
	    'go_to' => $site_to,
	)); ?>
	<div class="form-group fblogin">
		<span class="btn btn-primary" id="login-with-fb">Login with Facebook</span>
		<div class="or-line"></div><span class="auth-or">or</span>
	</div>
	<div class="biggroup">
		<div class="form-group <?php echo isset($errors['login']) ? 'error' : ''?>">
			<!--<?php echo form_label($login_label, $login['id']); ?>-->
			<?php echo form_input($login); ?>
			<div class="form-error"><?php echo isset($errors['login']) ? '↑ '.$errors['login'] : '' ?></div>
		</div>
		<div class="form-group <?php echo isset($errors['password']) ? 'error' : ''?>">
			<!--<?php echo form_label('Password', $password['id']); ?>-->
			<?php echo form_password($password); ?>
			<div class="form-error"><?php echo isset($errors['password']) ? '↑ '.$errors['password'] : '' ?></div>
		</div>
		<?php echo form_checkbox($remember); ?>
		<?php echo form_label('Remember me', $remember['id']); ?>
	</div>

	<button type="submit" class="btn btn-darkgray">Login</button>	
	
	<div class="form-group auth-sub">
		
		<?php echo anchor('/auth/forgot_password/', 'Forgot password'); ?>
		<?php if ($this->config->item('allow_registration', 'tank_auth')) echo anchor('/auth/register/', 'Register'); ?>
	</div>
							
				
	<?php echo form_close(); ?>

</section>

<script>
    $('#login-with-fb').on('click',function(e){
        e.preventDefault();
        var fb_diag = window.open('<?=$this->config->item('base_url')?>fbauth/login<?=($this->input->is_ajax_request())?"ajax/":"" ?>','fb_diag','width=600,height=300,scrollbars=yes,resizable=no');
        fb_diag.focus();
        //$.fn.dialog2.helpers.alert("현재 준비중입니다.");
    });
</script>
