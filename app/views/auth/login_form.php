<?php
$login = array(
	'name'	=> 'login',
	'id'	=> 'login',
	'value' => set_value('login'),
	'maxlength'	=> 80,
	'size'	=> 30,
	'class' => 'form-control',
	'autofocus' => 'autofocus'
);
if ($login_by_username AND $login_by_email) {
	$login_label = 'Email or login';
} else if ($login_by_username) {
	$login_label = 'Login';
} else {
	$login_label = 'Email';
}
$password = array(
	'name'	=> 'password',
	'id'	=> 'password',
	'size'	=> 30,
	'class' => 'form-control'
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
				
	<?php echo form_open($this->uri->uri_string(), array('role'=>'form','id'=>'login-form'), array(
	    'go_to' => $site_to,
	)); ?>
	<div class="form-group">
		<?php echo form_label($login_label, $login['id']); ?>
		<?php echo form_input($login); ?>
		<?php echo form_error($login['name']); ?><?php echo isset($errors[$login['name']])?$errors[$login['name']]:''; ?>
	</div>
	<div class="form-group">
		<?php echo form_label('Password', $password['id']); ?>
		<?php echo form_password($password); ?>
		<?php echo form_error($password['name']); ?><?php echo isset($errors[$password['name']])?$errors[$password['name']]:''; ?>
	</div>
	<div class="form-group">
		<?php echo form_checkbox($remember); ?>
		<?php echo form_label('Remember me', $remember['id']); ?>
		<?php echo anchor('/auth/forgot_password/', 'Forgot password'); ?>
		<?php if ($this->config->item('allow_registration', 'tank_auth')) echo anchor('/auth/register/', 'Register'); ?>
	</div>
							
	<button type="submit" class="btn btn-primary">Let me in</button>				
	<span class="btn btn-primary" id="login-with-fb">Facebook</span>
	<?php echo form_close(); ?>

</section>

<script>
    $('#login-with-fb').on('click',function(e){
        e.preventDefault();
        var fb_diag = window.open('<?=$this->config->item('base_url')?>auth/fb/link/for-login','fb_diag','width=600,height=300,scrollbars=yes,resizable=no');
        fb_diag.focus();
        //$.fn.dialog2.helpers.alert("현재 준비중입니다.");
    });
</script>
