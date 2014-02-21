<?php
if ($use_username) {
	$username = array(
		'name'	=> 'username',
		'id'	=> 'username',
		'class' => 'form-control',
		'value' => set_value('username'),
		'maxlength'	=> $this->config->item('username_max_length', 'tank_auth'),
		'size'	=> 30,
		'placeholder' => 'URL'
	);
}
$realname = array(
	'name'	=> 'realname',
	'id'	=> 'realname',
	'class' => 'form-control',
	'value' => set_value('realname'),
	'maxlength'	=> 45,
	'size'	=> 30,
	'placeholder' => 'User name'
);
$email = array(
	'name'	=> 'email',
	'id'	=> 'email',
	'class' => 'form-control',
	'value'	=> set_value('email'),
	'maxlength'	=> 80,
	'size'	=> 30,
	'placeholder' => 'Email address'
);
$password = array(
	'name'	=> 'password',
	'id'	=> 'password',
	'class' => 'form-control',
	'value' => '',
	'maxlength'	=> $this->config->item('password_max_length', 'tank_auth'),
	'size'	=> 30,
	'placeholder' => 'Password'
);
$confirm_password = array(
	'name'	=> 'confirm_password',
	'id'	=> 'confirm_password',
	'class' => 'form-control',
	'value' => '',
	'maxlength'	=> $this->config->item('password_max_length', 'tank_auth'),
	'size'	=> 30,
	'placeholder' => 'Confirm password'
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
$birth = array(
	'year' =>(!empty($value['year'])) ?$value['year'] :1990,
	'month'=>(!empty($value['month']))?$value['month']:8,
	'day'  =>(!empty($value['day']))  ?$value['day']  :8
);

$value['gender'] = set_value('gender');
$gender = array(
	'm' => set_radio('gender', 'm', ''),
	'f'	=> set_radio('gender', 'f', '')
);

$mailing = isset($error)?set_checkbox('mailing', '1', ''):"checked=\"checked\"";

if(isset($fb_num_id)) {
	$username['value'] = 
		(!empty($username['value']))?
			$username['value']
			:
			(isset($fb_info->username) ? $fb_info->username : '')
			;
	$realname['value'] = 
		(!empty($realname['value']))?
			$realname['value']
			:
			(isset($fb_info->realname) ? $fb_info->name : '')
			;
	$email['value'] = 
		(!empty($email['value']))?
			$email['value']
			:
			$fb_info->email
			;
	//$email['disabled'] = 'disabled';
	$gender[substr($fb_info->gender, 0, 1)] = 
		(!empty($value['gender']))?
			$gender[substr($fb_info->gender, 0, 1)]:'checked';

	if(empty($value['year'])&&empty($value['month'])&&empty($value['day'])){
		$birthday = explode('/', $fb_info->birthday);
		$birth = array(
			'year'=>$birthday[2],
			'month'=>$birthday[0],
			'day'=>$birthday[1]
		);
	}
}

$value['fb_num_id'] = set_value('fb_num_id');
if(isset($error)&&!empty($value['fb_num_id'])){
	$fb_num_id = $value['fb_num_id'];
}
?>
	<?php if(isset($error)){?>
<!--
	<div class="center" id="status">
		<div class="panel panel-danger">
			<div class="panel-heading">
			  <h3 class="panel-title">확인이 필요합니다</h3>
			</div>
			<div class="panel-body">
				<p>필요한 정보를 입력하지 않았거나,<br/>사용할 수 없어 수정해야 합니다.<br/>아래를 확인해 주세요.<br/><a href="javascript:$('#status').remove();">닫기</a></p>
			</div>
		</div>
	</div>
-->
	<?php } else if(isset($submit_error)){?>
	<div class="center" id="status">
		<div class="panel panel-danger">
			<div class="panel-heading">
			  <h3 class="panel-title">전송 오류</h3>
			</div>
			<div class="panel-body">
				<p>올바른 방법으로 접근하세요.<br/><a href="javascript:$('#status').remove();">닫기</a></p>
			</div>
		</div>
	</div>
	<?php }?>
<?=form_open($this->uri->uri_string(), array('role'=>'form')); ?>
	<div class="form-group">
	<?php if(isset($fb_num_id)){?>
		<?=form_hidden('fb_num_id', $fb_num_id); ?>
		<a href="javascript:window.location.reload()" class="btn btn-info btn-block">Now with facebook</a>
	<?php }else{?>
		<a href="" class="btn btn-info btn-block" id="signup-with-fb">Signup with facebook</a>
	<?php }?>
	<?=form_hidden('submit_uuid', $submit_uuid); ?>
	<div class="or-line"></div><span class="auth-or">or</span>
	</div>


<div class="biggroup">
	<div id="form-email" class="form-group <?=isset($errors[$email['name']]) ? 'error' : ''?>">
		<!--<?=form_label('Email Address', $email['id']); ?>-->
		<?=form_input($email); ?>
		<div class="form-error"><?=isset($errors[$email['name']])?$errors[$email['name']]:''; ?></div>
	</div>
	<div id="form-password" class="form-group <?=isset($errors[$password['name']]) ? 'error' : ''?>">
		<!--<?=form_label('Password', $password['id']); ?>-->
		<?=form_password($password); ?>
		<div class="form-error"><?=isset($errors[$password['name']])?$errors[$password['name']]:''; ?></div>
	</div>
	<div id="form-confirm-password" class="form-group <?=isset($errors[$confirm_password['name']]) ? 'error' : ''?>">
		<!--<?=form_label('Confirm Password', $confirm_password['id']); ?>-->
		<?=form_password($confirm_password); ?>
		<div class="form-error"><?=isset($errors[$confirm_password['name']])?$errors[$confirm_password['name']]:''; ?></div>
	</div>	
	<div class="form-group <?=isset($errors[$realname['name']]) ? 'error' : ''?>">
		<!--<?=form_label('realname', $realname['id']); ?>-->
		<?=form_input($realname); ?>
		<div class="form-error"><?=isset($errors[$realname['name']])?$errors[$realname['name']]:''; ?></div>
	</div>
	<?php if ($use_username) { ?>
	<div id="form-username" class="form-group <?=isset($errors[$username['name']]) ? 'error' : ''?>">
		<!--<?=form_label('Username', $username['id']); ?>-->
		<p><?=$this->input->server('HTTP_HOST')?>/<span class="example" style="color: #333 !important;font-weight: bold;">URL</span></p>
		<?=form_input($username); ?>
		<div class="form-error"><?=isset($errors[$username['name']])?$errors[$username['name']]:''; ?></div>
	</div>
	<?php } ?>
	<div class="form-group <?=isset($errors['gender']) ? 'error' : ''?>">
		<!--<label>성별</label><br/>-->
		<label class="radio-inline">
			<input type='radio' name='gender' value='f' <?=$gender['f']?> /> 여
		</label>
		<label class="radio-inline">
			<input type='radio' name='gender' value='m' <?=$gender['m']?> /> 남
		</label>
		<div class="form-error"><?=$errors['gender']; ?></div>
	</div>

	<div class="form-group <?=isset($errors['year'])&&isset($errors['month'])&&isset($errors['day']) ? 'error' : ''?>">
		
		<div id='birth_field'>
			<label class="labeltext" style="margin-right:10px">생년월일</label>
			<select name='year' class='no-jquery'>
				<?php for($i=date('Y'); $i>1900; $i--): ?>
					<option value="<?=$i?>"<?if($birth['year']==$i){?> selected<?}?>><?=$i?>년</option>
				<?php endfor;?>
			</select>
			<select name='month' class='no-jquery'>
				<?php for($i=1; $i<13; $i++): ?>
					<option value="<?=$i?>"<?if($birth['month']==$i){?> selected<?}?>><?=$i?>월</option>
				<?php endfor;?>
			</select>
			<select name='day' class='no-jquery'>
				<?php for($i=1; $i<32; $i++): ?>
					<option value="<?=$i?>"<?if($birth['day']==$i){?> selected<?}?>><?=$i?>일</option>
				<?php endfor;?>
			</select>
		</div>
	</div>

    <div class="form-group checkbox" style="margin-top:-10px;">
        <label class="labeltext2">
        	<input type="checkbox" name="mailing" value="1" <?=$mailing?>>노트폴리오의 최신 소식 및 작가/작품 소개를 메일로 받겠습니다.
        </label> 
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
		<?=form_error('recaptcha_response_field'); ?>
		<?=$recaptcha_html; ?>
	</div>
	<?php } else { ?>
	<div class="form-group">
		<p>Enter the code exactly as it appears:</p>
		<?=$captcha_html; ?>
	</div>
	<div class="form-group">
		<?=form_label('Confirmation Code', $captcha['id']); ?>
		<?=form_input($captcha); ?>
		<?=form_error($captcha['name']); ?>
	</div>
	<?php }
	} ?>

	<div class="auth-sub labeltext2 auth-agree" style="">버튼을 클릭함으로써 노트폴리오의 <a href="/info/terms" class="register-ajax">약관</a>과 <a href="/info/privacy" class="register-ajax">개인정보보호정책</a>에 동의합니다.</div>
	<button type="submit" name="register" class="btn btn-darkgray">Sign up</button>
<?=form_close(); ?>


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
				        label: 'OK',
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



	$(function(){
		$('input[type="text"]','#form-username').on('keyup keypress blur change', function(){
			var val = $(this).val();
			$.post('/auth/check_username_available', {username: val}, function(data, textStatus, xhr) {
	            var response = $.parseJSON(data);
	            if(response.status=='done'){
	            	$('#form-username').removeClass('error');
	            	$('.form-error','#form-username').text('');
	            }else{
	            	$('#form-username').addClass('error');
	            	$('.form-error','#form-username').text('↑ '+response.error);
	            }
				
			});
			$('span.example','#form-username').text(val);
		});
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
		$('input[type="password"]','#form-confirm-password').on('keyup keypress blur change', function(){
			var val = $(this).val();
			if(val==$('input[type="password"]','#form-password').val()){
            	$('#form-confirm-password').removeClass('error');
            	$('.form-error','#form-confirm-password').text('');
            }else{
            	$('#form-confirm-password').addClass('error');
            	$('.form-error','#form-confirm-password').text('↑ '+'비밀번호를 똑같이 입력해주세요.');
            }
			
		});
	});

</script>