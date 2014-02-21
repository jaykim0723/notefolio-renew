<?php
$new_password = array(
	'name'	=> 'new_password',
	'id'	=> 'new_password',
	'maxlength'	=> $this->config->item('password_max_length', 'tank_auth'),
	'size'	=> 30,
);
$confirm_new_password = array(
	'name'	=> 'confirm_new_password',
	'id'	=> 'confirm_new_password',
	'maxlength'	=> $this->config->item('password_max_length', 'tank_auth'),
	'size' 	=> 30,
);
?>
<?php echo form_open($this->uri->uri_string()); ?>
<div class="biggroup">
	<div id="form-new-password" class="form-group <?php echo isset($errors['new_password']) ? 'error' : ''?>">
		<label for="" class="labeltext">새로운 비밀번호</label>
		<input type="password" name="new_password" class="form-control" id="new_password" value="<?php echo set_value('new_password') ?>" maxlength="<?php echo $this->config->item('password_max_length', 'tank_auth') ?>"/>
		<div class="form-error"><?php echo isset($errors['new_password']) ? '↑ '.$errors['new_password'] : '' ?></div>
	</div>

	<div id="form-confirm-new-password" class="form-group <?php echo isset($errors['confirm_new_password']) ? 'error' : ''?>">
		<label for="" class="labeltext">새로운 비밀번호 확인</label>
		<input type="password" name="confirm_new_password" class="form-control" id="confirm_new_password" value="<?php echo set_value('confirm_new_password') ?>" maxlength="<?php echo $this->config->item('password_max_length', 'tank_auth') ?>"/>
		<div class="form-error"><?php echo isset($errors['confirm_new_password']) ? '↑ '.$errors['confirm_new_password'] : '' ?></div>
	</div>
</div>

	<div class='center'>
	
		<button type="submit" id='btnSubmit' class='btn btn-darkgray btn-block pure-button-big pure-button'>Submit</button>
	</div>
<script>
	$(function(){
		$('input[type="password"]','#form-confirm-new-password').on('keyup keypress blur change', function(){
			var val = $(this).val();
			if(val==$('input[type="password"]','#form-new-password').val()){
            	$('#form-confirm-new-password').removeClass('error');
            	$('.form-error','#form-confirm-new-password').text('');
            }else{
            	$('#form-confirm-new-password').addClass('error');
            	$('.form-error','#form-confirm-new-password').text('↑ '+'비밀번호를 똑같이 입력해주세요.');
            }
			
		});
	});

</script>
<?php echo form_close(); ?>