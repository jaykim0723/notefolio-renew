<?php
echo form_open('', array(
	'name' => 'change_email_form',
	'role' => 'form',
	'id'   => 'change_email'
), array(
	'go_to'      => '', 
	'submitting' => 1
));
?>

	<div class="form-group <?php echo isset($errors['password']) ? 'error' : ''?>">
		<label for="">비밀번호</label>
		<input type="password" name="password" class="form-control" id="password" value=""/>
		<div class="form-error"><?php echo isset($errors['password']) ? '↑ '.$errors['password'] : '' ?></div>
	</div>

	<div class="form-group <?php echo isset($errors['email']) ? 'error' : ''?>">
		<label for="">변경할 이메일 주소</label>
		<input type="text" name="email" class="form-control" id="email" value="<?php echo set_value('email') ?>" maxlength="80">
		<div class="form-error"><?php echo isset($errors['email']) ? '↑ '.$errors['email'] : '' ?></div>
	</div>

	<div class='center'>
		<br/>
		<button type="submit" id='btnSubmit' class='btn btn-primary btn-block pure-button-big pure-button'>Submit</button>
	</div>

<?php echo form_close(); ?>