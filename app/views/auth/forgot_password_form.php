<?php
if ($this->config->item('use_username', 'tank_auth')) {
	$login_label = 'Email을 적어주세요!';
} else {
	$login_label = 'Email';
}
?>

<?php
echo form_open('', array(
	'name' => 'forgot_password_form',
	'role' => 'form',
	'id'   => 'forgot_password'
), array(
	'go_to'      => '', 
	'submitting' => 1
));
?>
<div class="biggroup big-left">
	<div class="form-group <?php echo isset($errors['login']) ? 'error' : ''?>">
		<label for="" class="labeltext"><?php echo $login_label ?></label>
		<input class="form-control" type="text" name="login" value="" id="login" maxlength="30" size="30" value="<?php echo set_value('login') ?>">
		<div class="form-error"><?php echo isset($errors['login']) ? '↑ '.$errors['login'] : '' ?></div>
	</div>
	<div class='center'>
		<button type="submit" id='btnSubmit' class='btn btn-darkgray2 btn-block pure-button-big pure-button'>Submit</button>
	</div>
</div>


<?php echo form_close(); ?>