
<?php
echo form_open('', array(
	'name' => 'change_password_form',
	'role' => 'form',
	'id'   => 'change_password'
), array(
	'go_to'      => '', 
	'submitting' => 1
));
?>
<div class="biggroup">
	<div class="form-group <?php echo isset($errors['old_password']) ? 'error' : ''?>">
		<label for="" class="labeltext">현재 비밀번호</label>
		<input type="password" name="old_password" class="form-control" id="old_password" value="<?php echo set_value('old_password') ?>"/>
		<div class="form-error"><?php echo isset($errors['old_password']) ? '↑ '.$errors['old_password'] : '' ?></div>
	</div>

	<div class="form-group <?php echo isset($errors['new_password']) ? 'error' : ''?>">
		<label for="" class="labeltext">새로운 비밀번호</label>
		<input type="password" name="new_password" class="form-control" id="new_password" value="<?php echo set_value('new_password') ?>" maxlength="<?php echo $this->config->item('password_max_length', 'tank_auth') ?>"/>
		<div class="form-error"><?php echo isset($errors['new_password']) ? '↑ '.$errors['new_password'] : '' ?></div>
	</div>

	<div class="form-group <?php echo isset($errors['confirm_new_password']) ? 'error' : ''?>">
		<label for="" class="labeltext">새로운 비밀번호 확인</label>
		<input type="password" name="confirm_new_password" class="form-control" id="confirm_new_password" value="<?php echo set_value('confirm_new_password') ?>" maxlength="<?php echo $this->config->item('password_max_length', 'tank_auth') ?>"/>
		<div class="form-error"><?php echo isset($errors['confirm_new_password']) ? '↑ '.$errors['confirm_new_password'] : '' ?></div>
	</div>
</div>

	<div class='center'>
	
		<button type="submit" id='btnSubmit' class='btn btn-darkgray btn-block pure-button-big pure-button'>Submit</button>
	</div>
	
<?php echo form_close(); ?>