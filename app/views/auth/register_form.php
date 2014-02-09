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

$value['year'] = set_value('year');
$value['month'] = set_value('month');
$value['day'] = set_value('day');
$value['birth'] = explode('-', set_value('birth'));
$valuedata = var_export($value,true);
$birth = array(
	'year' =>(!empty($value['birth'][0])) ?$value['birth'][0] :1990,
	'month'=>(!empty($value['birth'][1]))?$value['birth'][1]:8,
	'day'  =>(!empty($value['birth'][2]))  ?$value['birth'][2]  :8
);

$value['gender'] = set_value('gender');
$gender = array(
	'm' => (!empty($value['gender'])&&$value['gender']=='m') ?'checked' :'',
	'f'	=> (!empty($value['gender'])&&$value['gender']=='f') ?'checked' :''
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
	//$email['disabled'] = 'disabled';
	$gender[substr($fb_info->gender, 0, 1)] = 
		(!empty($gender[substr($fb_info->gender, 0, 1)]))?
			$gender[substr($fb_info->gender, 0, 1)]:'checked';

	if(empty($birth['year'])&&empty($birth['month'])&&empty($birth['day'])){
		$birthday = explode('/', $fb_info->birthday);
		$birth = array(
			'year'=>$birthday[2],
			'month'=>$birthday[0],
			'day'=>$birthday[1]
		);
	}
}
?>
<?php echo form_open($this->uri->uri_string(), array('role'=>'form')); ?>
	<div class="form-group">
	<?php if(isset($error)){?>
	<?=$error?>
	<?php }?>
	<?php if(isset($fb_num_id)){?>
		<?php echo form_hidden('fb_num_id', $fb_num_id); ?>
		<a href="javascript:window.location.reload()" class="btn btn-info btn-block">Now with facebook</a>
	<?php }else{?>
		<a href="" class="btn btn-info btn-block" id="signup-with-fb">Signup with facebook</a>
	<?php }?>
	<?php echo form_hidden('submit_uuid', $submit_uuid); ?>
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
		<?php echo form_error('gender'); ?>
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
	<?=$valuedata?>

    <div class="form-group checkbox">
        <label class="checked">
        	<input type="checkbox" name="mailing" value="1" checked="checked">노트폴리오의 최신 소식 및 작가/작품 소개를 메일로 받겠습니다.
        </label> 
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

	<div style="color:#888;font-size:12px;margin-top:5px;">버튼을 클릭함으로써 노트폴리오의 <a href="/info/terms" class="register-ajax">약관</a>과 <a href="/info/privacy" class="register-ajax">개인정보보호정책</a>에 동의합니다.</div>
	<button type="submit" name="register" class="btn btn-primary">Register</button>
<?php echo form_close(); ?>


<script>
    $('#signup-with-fb').on('click',function(e){
        e.preventDefault();
        var fb_diag = window.open('<?=$this->config->item('base_url')?>fbauth/register','fb_diag','width=600,height=300,scrollbars=yes,resizable=no');
        fb_diag.focus();
        //$.fn.dialog2.helpers.alert("현재 준비중입니다.");
    });
    $(function(){
    	$('.register-ajax').on('click', function(event){
    		event.preventDefault();
    		event.stopPropagation();
			var dialog = new BootstrapDialog({
			    title: $(this).text(),
			    message: '<div class="loading" style="text-align:center;padding:50px 0;"><img src="/img/loading.gif"/></div>',
			    buttons: [
				    {
				        label: 'Done',
				        cssClass: 'btn-primary',
				        action: function(dialogRef){    
				            dialogRef.close();
				        }
				    }
			    ]
			});
			// dialog.realize();
			// dialog.getModal().prop('id', options.id); // cssClass 버그로 인해서 이 꼼수로..
			dialog.open();

			$.get($(this).attr('href'), {}).done(function(responseHTML){
				dialog.getModalBody().html(responseHTML);
			});
    	})
    })




</script>