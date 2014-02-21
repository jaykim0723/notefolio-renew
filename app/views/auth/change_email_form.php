
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
<div class="biggroup">
	<div class="form-group <?php echo isset($errors['password']) ? 'error' : ''?>">
		<label for="" class="labeltext">비밀번호</label>
		<input type="password" name="password" class="form-control" id="password" value=""/>
		<div class="form-error"><?php echo isset($errors['password']) ? '↑ '.$errors['password'] : '' ?></div>
	</div>

	<div id="form-email" class="form-group <?php echo isset($errors['email']) ? 'error' : ''?>">
		<label for="" class="labeltext">변경할 이메일 주소</label>
		<input type="text" name="email" class="form-control" id="email" value="<?php echo set_value('email') ?>" maxlength="80">
		<div class="form-error"><?php echo isset($errors['email']) ? '↑ '.$errors['email'] : '' ?></div>
	</div>
</div>

	<div class='center'>
		
		<button type="submit" id='btnSubmit' class='btn btn-darkgray btn-block pure-button-big pure-button'>Submit</button>
	</div>

<script>
	$(function(){
		$('input[type="text"]','#form-email').on('keyup keypress blur change', function(){
			var val = $(this).val();
			$.post('/auth/check_email_available', {email: val}, function(data, textStatus, xhr) {
                var response = $.parseJSON(data);
                if(response.status=='done'){
                	$('#form-email').removeClass('error');
                	$('.form-error','#form-email').text('');
                }else{
                	$('#form-email').addClass('error');
                	$('.form-error','#form-email').text('↑ '+response.error);
                }
				
			});
		});
	});

</script>
<?php echo form_close(); ?>