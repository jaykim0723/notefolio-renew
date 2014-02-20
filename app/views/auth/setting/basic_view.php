<div class="biggroup">
<fieldset id='auth_basic'>
	<div class="labeltext3">기본정보</div>

	<div id="form-username" class="form-group <?php echo isset($errors['username']) ? 'error' : ''?>">
		<label class="labeltext">URL</label>
		<p><?=$this->input->server('HTTP_HOST')?>/<span class="example" style="color: #333 !important;font-weight: bold;"><?=$username?></span></p>
		<input class="form-control" type='text' id='username' name='username' value="<?php echo $username?>" minlength='3' maxlength='20' rel='tooltip' placeholder="영문자,숫자,_,-"/>
		<div class="form-error"><?php echo isset($errors['username']) ? '↑ '.$errors['username'] : '' ?></div>
	</div>
	<div class="form-group">
		<label class="labeltext">성별</label><br/>
		<label class="radio-inline">
			<input type='radio' name='gender' value='f'/> 여
		</label>
		<label class="radio-inline">
			<input type='radio' name='gender' value='m'/> 남
		</label>
		<script>
			$('#auth_basic :radio[name=gender]').filter('input[value=<?php echo $gender?>]').attr('checked', true).trigger('change');
		</script>
	</div>

	<div class="row">
		<div class="form-group" style="margin-bottom:20px;margin-left:15px;">
			<p style="margin: 10px 0px;"><span class="labeltext">이메일</span><a href="/auth/change_email" class="btn btn-link labeltext2 auth-smallbtn" style="">이메일 변경하기</a></p>
			<?php echo $email?>
		</div>
		<div class="form-group" style="margin-bottom: 0px;margin-left:15px;">
			<span class="labeltext">비밀번호</span>
			<a href="/auth/change_password" class="btn btn-link호 labeltext2 auth-smallbtn">비밀번호 변경하기</a>
		</div>
	</div>
	<!-- 
	<div class="form-group">
		<label>이메일</label>
		<input type='text' class="form-control" name='email' data-last='' id='email' value="<?php echo $email?>"/>
		<span class="check" id='email_checker' rel='tooltip' title="로그인시 ID로 활용됩니다."></span>
	</div> 
	-->


    <?php
    if(isset($birth) && !empty($birth)) {
		$birth_temp=explode('-', $birth);
		$birth = array();
		list($birth['year'],$birth['month'],$birth['day']) = $birth_temp;
		unset($birth_temp);
	}else
		$birth=array('year'=>1990,'month'=>8,'day'=>8);
	?>
	<div class="form-group">
		<label class="labeltext">생년월일</label>
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

    <div class="form-group checkbox">
        <label class=" <?if($mailing==1){?> checked<?}?> labeltext2">
        	<input type="checkbox" name="mailing" value="1" <?if($mailing==1){?>checked="checked"<?}?>>노트폴리오의 최신 소식 및 작가/작품 소개를 메일로 받겠습니다.
        </label> 
    </div>
	
</fieldset>
</div>