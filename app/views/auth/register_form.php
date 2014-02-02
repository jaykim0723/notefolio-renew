<?php
if ($use_username) {
	$username = array(
		'name'	=> 'username',
		'id'	=> 'username',
		'class' => 'form-control',
		'value' => set_value('username'),
		'maxlength'	=> $this->config->item('username_max_length', 'tank_auth'),
		'size'	=> 30,
	);
}
$email = array(
	'name'	=> 'email',
	'id'	=> 'email',
	'class' => 'form-control',
	'value'	=> set_value('email'),
	'maxlength'	=> 80,
	'size'	=> 30,
);
$password = array(
	'name'	=> 'password',
	'id'	=> 'password',
	'class' => 'form-control',
	'value' => set_value('password'),
	'maxlength'	=> $this->config->item('password_max_length', 'tank_auth'),
	'size'	=> 30,
);
$confirm_password = array(
	'name'	=> 'confirm_password',
	'id'	=> 'confirm_password',
	'class' => 'form-control',
	'value' => set_value('confirm_password'),
	'maxlength'	=> $this->config->item('password_max_length', 'tank_auth'),
	'size'	=> 30,
);
$captcha = array(
	'name'	=> 'captcha',
	'id'	=> 'captcha',
	'class' => 'form-control',
	'maxlength'	=> 8,
);
$birth = array(
	'year'=>1990,
	'month'=>8,
	'day'=>8
);

$gender = array(
	'm' => '',
	'f'	=> ''
);

if(isset($fb_num_id)) {
	$username['value'] = 
		(!empty($username['value']))?
			$username['value']
			:
			(isset($fb_info->username) ? $fb_info->username : '')
			;
	$email['value'] = 
		(!empty($email['value']))?
			$email['value']
			:
			$fb_info->email
			;
	$email['disabled'] = 'disabled';
	$gender[substr($fb_info->gender, 0, 1)] = 'checked';
}
?>
<?php echo form_open($this->uri->uri_string(), array('role'=>'form')); ?>
	<div class="form-group">
	<?php if(isset($fb_num_id)){?>
		<?php echo form_hidden('fb_num_id', $fb_num_id); ?>
		<a href="javascript:window.location.reload()" class="btn btn-info btn-block">Now with facebook</a>
	<?php }else{?>
		<a href="" class="btn btn-info btn-block" id="signup-with-fb">Signup with facebook</a>
	<?php }?>
	</div>
	<?php if ($use_username) { ?>
	<div class="form-group">
		<?php echo form_label('Username', $username['id']); ?>
		<?php echo form_input($username); ?>
		<?php echo form_error($username['name']); ?><?php echo isset($errors[$username['name']])?$errors[$username['name']]:''; ?>
	</div>
	<?php } ?>
	<div class="form-group">
		<?php echo form_label('Email Address', $email['id']); ?>
		<?php echo form_input($email); ?>
		<?php echo form_error($email['name']); ?><?php echo isset($errors[$email['name']])?$errors[$email['name']]:''; ?>
	</div>
	<div class="form-group">
		<?php echo form_label('Password', $password['id']); ?>
		<?php echo form_password($password); ?>
		<?php echo form_error($password['name']); ?>
	</div>
	<div class="form-group">
		<?php echo form_label('Confirm Password', $confirm_password['id']); ?>
		<?php echo form_password($confirm_password); ?>
		<?php echo form_error($confirm_password['name']); ?>
	</div>
	<div class="form-group">
		<label>성별</label><br/>
		<label class="radio-inline">
			<input type='radio' name='gender' value='f' <?=$gender['f']?> /> 여
		</label>
		<label class="radio-inline">
			<input type='radio' name='gender' value='m' <?=$gender['m']?> /> 남
		</label>
	</div>

	<div class="form-group">
		<label>생년월일</label>
		<div id='birth_field'>
			<select name='year' class='no-jquery'>
				<?php for($i=date('Y'); $i>1900; $i--): ?>
					<option value="<?php echo $i?>"<?if($birth['year']==$i){?> selected<?}?>><?php echo $i?>년</option>
				<?php endfor;?>
			</select>
			<select name='month' class='no-jquery'>
				<?php for($i=1; $i<13; $i++): ?>
					<option value="<?php echo $i?>"<?if($birth['month']==$i){?> selected<?}?>><?php echo $i?>월</option>
				<?php endfor;?>
			</select>
			<select name='day' class='no-jquery'>
				<?php for($i=1; $i<32; $i++): ?>
					<option value="<?php echo $i?>"<?if($birth['day']==$i){?> selected<?}?>><?php echo $i?>일</option>
				<?php endfor;?>
			</select>
		</div>
	</div>

	<?php if ($captcha_registration) {
		if ($use_recaptcha) { ?>
	<div class="form-group">
		<div id="recaptcha_image"></div>
		<a href="javascript:Recaptcha.reload()">Get another CAPTCHA</a>
		<div class="recaptcha_only_if_image"><a href="javascript:Recaptcha.switch_type('audio')">Get an audio CAPTCHA</a></div>
		<div class="recaptcha_only_if_audio"><a href="javascript:Recaptcha.switch_type('image')">Get an image CAPTCHA</a></div>
	</div>
	<div class="form-group">
		<div class="recaptcha_only_if_image">Enter the words above</div>
		<div class="recaptcha_only_if_audio">Enter the numbers you hear</div>
		<input type="text" id="recaptcha_response_field" name="recaptcha_response_field" class="form-control"/>
		<?php echo form_error('recaptcha_response_field'); ?>
		<?php echo $recaptcha_html; ?>
	</div>
	<?php } else { ?>
	<div class="form-group">
		<p>Enter the code exactly as it appears:</p>
		<?php echo $captcha_html; ?>
	</div>
	<div class="form-group">
		<?php echo form_label('Confirmation Code', $captcha['id']); ?>
		<?php echo form_input($captcha); ?>
		<?php echo form_error($captcha['name']); ?>
	</div>
	<?php }
	} ?>
	<button type="submit" name="register" class="btn btn-primary">Register</button>
<?php echo form_close(); ?>


<script>
    $('#signup-with-fb').on('click',function(e){
        e.preventDefault();
        var fb_diag = window.open('<?=$this->config->item('base_url')?>fbauth/register','fb_diag','width=600,height=300,scrollbars=yes,resizable=no');
        fb_diag.focus();
        //$.fn.dialog2.helpers.alert("현재 준비중입니다.");
    });
</script>