<?php
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
$hidden_field = array(
    'go_to' => $site_to,
);
?>
<section>
				
	<?php echo form_open($this->uri->uri_string(), array('role'=>'form','id'=>'elevate-form')); ?>
	<?php echo form_hidden($hidden_field); ?>
	<div class="panel panel-warning">
		<div class="panel-heading">
		  <h3 class="panel-title">관리자 기능 접근</h3>
		</div>
		<div class="panel-body">
			<p>관리자 페이지에 접근하려면 다시 로그인하십시오.</p>
			<div class="form-group">
				<?php echo form_label('Login', 'login'); ?>
				<p class="lead"><?=$admin['realname']?>(<?=$admin['username']?>)</p>
				<p><small>이 아이디가 내 것이 아니라면? <a href="/auth/logout/">로그아웃</a></small></p>
			</div>
			<div class="form-group">
				<?php echo form_label('Password', $password['id']); ?>
				<?php echo form_password($password); ?>
				<?php echo form_error($password['name']); ?><?php echo isset($errors[$password['name']])?$errors[$password['name']]:''; ?>
			</div>
		</div>
	</div>
									
	<button type="submit" class="btn btn-primary">Sign in</button>
	<?php echo form_close(); ?>

</section>
