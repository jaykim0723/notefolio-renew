<fieldset id='auth_basic'>
	<legend>기본정보</legend>

	<div class='row-fluid'>
		<div class="span8 control-group">
			<label>이름</label>
			<input type='text' id='realname' name='realname' value="<?php echo $realname?>" minlength='3' maxlength='20' rel='tooltip' title="실명을 권장합니다."/>
		</div>
		<div class="span8 control-group">
			<label>성별</label>
			<label class='notefolio-radio inline'>
				<input type='radio' name='gender' value='f'/>여
			</label>
			<label class='notefolio-radio inline'>
				<input type='radio' name='gender' value='m'/>남
			</label>
			<script>
				$('#auth_basic :radio[name=gender]').filter('input[value=<?php echo $gender?>]').attr('checked', true).trigger('change');
			</script>
		</div>
	</div>
		

	<div class='row-fluid'>
		<div class="span8 control-group">
			<label>이메일</label>
			<?php if(MY_ID==0): ?>
			<input type='text'  name='email' data-last='' id='email' value="<?php echo $email?>"/> <span class="check" id='email_checker' rel='tooltip' title="로그인시 ID로 활용됩니다."></span>

		</div>
		<script>
			var timeout = '';
			var check_reg_email = function(v){
				return /^[_a-zA-Z0-9\-\.\+]+@[\.\_a-zA-Z0-9\-\+]+\.[a-zA-Z]+$/.test(v); // 정규식 검사하기.	
			};
			var autoCheckEmail = function(){
				var o = $('#email');
				$.get('/auth/check_email_available', { email : o.val() }, function(d){
					o = o.data('last', o.val()).next();
					if(d=='y')
						o.html('<img src="/images/signup/check.png">').removeClass('unavailable');
					else
						o.html('<img src="/images/signup/x.png">').addClass('unavailable');
				});
			};
			$('#email').keyup(function(){
				// 키 입력마다 검사하기
				var v = $.trim($(this).val());
				if($(this).data('last') == v) return false;
				var o = $('#email_checker').html('').removeClass('unavailable');
				if(!check_reg_email(v)){
					if(v!='')
						$(this).focus();
					return false;
				}
				o.html('<img src="/images/block.gif"/>');
				clearTimeout(timeout);
				timeout = setTimeout(function(){
					autoCheckEmail();
				},800);
			}).trigger('keyup');				
		</script>		
		<div class="span8 control-group">
			<label>이메일 확인</label>
			<input type='text'  name='confirm_email' value="<?php echo $confirm_email?>"/>
			<?php else: ?>
			<?php echo $email?> <?php echo anchor('auth/change_email', '이메일 변경', array('class'=>'underline')); ?>
			<?php endif; ?>
		</div>
		
	<?php if(MY_ID==0): ?>
	</div>
	<div class='row-fluid'>
	<?php endif; ?>
	
		<div class="span8 control-group">
			<label>비밀번호</label>
			<?php if(MY_ID==0): ?>
			<input type='password' name='password' value="<?php echo $password?>"  minlength='3' maxlength='20'/>
		</div>
		
		<div class="span8 control-group">	
			<label>비밀번호 확인</label>
			<input type='password' name='confirm_password' value="<?php echo $confirm_password?>"  minlength='3' maxlength='20'/>
			<?php else: ?>
			<?php echo anchor('auth/change_password', '비밀번호 변경', array('class'=>'underline', 'style'=>'padding-left:1px;')); ?>
			<?php endif; ?>
		</div>	
	</div>
	
		
	
	
	<div class="control-group">
		<label>개인url</label>
		<span class='url_hint'>http://www.notefolio.net/</span><input type='text' id='username' name='username' value="<?php echo $username?>" minlength='3' maxlength='20'/>
		<span class="check" id='checker'></span>
		<script>
			var timeout='';
            var username_checked=false;
			var check_reg = function(v){
				return /^[a-zA-Z0-9\_\-]+$/.test(v); // 정규식 검사하기.	
			};
			var autoCheck = function(){
				var o = $('#username');
				$.get('/auth/check_username_available', { username : o.val() }, function(d){
					o = o.data('last', o.val()).next();
					if(d=='y'){
						o.html('<img src="/images/signup/check.png">').removeClass('unavailable');
						//'✔'
                        username_checked=true;
					}
                    else{
						o.html('<img src="/images/signup/x.png">').addClass('unavailable');
                        username_checked=false;
                    }
				});
			};
            
			$('#username').keyup(function(){
				// 키 입력마다 검사하기
				var v = $.trim($(this).val());
				if($(this).data('last') == v) return false;
				var o = $('#checker').html('').removeClass('unavailable');
				if(!check_reg(v)){
					if(v!='')
						$(this).focus();
					return false;
				}
				o.html('<img src="/images/block.gif"/>');
				clearTimeout(timeout);
				timeout = setTimeout(function(){
					autoCheck();
				},800);
			}).trigger('keyup');				
		</script>
	</div>


	<?php if(MY_ID>0):?>
	    <? if(isset($birth) && !empty($birth)) {
	           $birth_temp=explode('-', $birth);
               $birth = array();
               list($birth['year'],$birth['month'],$birth['day']) = $birth_temp;
               unset($birth_temp);
           }
	       else $birth=array('year'=>'1990','month'=>'8','day'=>'8');?>
		<div class="control-group">
			<label>생년월일</label>
			<div id='birth_field'>
				<select name='year' class='span3'>
					<?php for($i=date('Y'); $i>1900; $i--): ?>
						<option value="<?php echo $i?>"<?if($birth['year']==$i){?> selected<?}?>><?php echo $i?>년</option>
					<?php endfor;?>
				</select>
				<select name='month' class='span2'>
					<?php for($i=1; $i<13; $i++): ?>
						<option value="<?php echo $i?>"<?if($birth['month']==$i){?> selected<?}?>><?php echo $i?>월</option>
					<?php endfor;?>
				</select>
				<select name='day' class='span2'>
					<?php for($i=1; $i<32; $i++): ?>
						<option value="<?php echo $i?>"<?if($birth['day']==$i){?> selected<?}?>><?php echo $i?>일</option>
					<?php endfor;?>
				</select>
			</div>
		</div>

        <div class="row-fluid">
                <label>메일링</label>
            <div class="span9 control-group auth_check" style="margin-left:0">
                <label class="notefolio-checkbox inline<?if($mailing==1){?> checked<?}?>"><input type="checkbox" name="mailing" value="1" <?if($mailing==1){?>checked="checked"<?}?>>노트폴리오의 최신 소식 및 작가/작품 소개를 메일로 받겠습니다. <br></label> 
                
            </div>
        </div>
	<?php endif;?>	
	<?php if(MY_ID==0): ?>
	
		<div class='row-fluid'>
			<div class="span8 control-group">
				<label class='notefolio-checkbox inline'>
					<input type='checkbox' name='term' value='1'/> <a href='#' class='underline' id='open_term'>약관</a>에 동의합니다.
					<br/>
				</label>
			</div>
			<div class="span8 control-group">
				<label class='notefolio-checkbox inline'>
					<input type='checkbox' name='privacy' value='1'/> <a href='#' class='underline' id='open_privacy'>개인정보보호정책</a>에 동의합니다.
					<br/>
				</label>
			</div>
		</div>	
	
	<ul class="pager">
		<li class="next"><a href="#" class="next" data-field="keywords">Next</a></li>
	</ul>
	<script>
		$('#open_term, #open_privacy').click(function(e){
			var page = $(this).attr('id').replace('open_', '');
			$('<div/>').dialog2({
				title: page.replace(page.charAt(0), page.charAt(0).toUpperCase()), 
				content: "/info/"+page, 
				id: "cont"
			});			
			e.preventDefault();
		});
	</script>

	<?php endif; ?>
	
	
	
	
	<script>
		// basic에 관한 폼 검증
		// register_form_view나 setting_form_view에서 호출된다.
		var check_basic = function(){
			var f = $('#auth_basic');
			f.find('label').children('span').remove(); // reset
			var o = o2 = '';
			if(workSpace == 'register'){ 
				/* register form(step by step) */
				
				// 이메일
				o = f.find('input[name=email]');
				o2 = f.find('input[name=confirm_email]');
				if(!validation(o.val(), true, 'email'))
					error(o, '정상적으로 입력하여 주십시오.');
				else if(o.next().html() != '<img src="/images/signup/check.png">')
					error(o, '이미 사용중인 주소입니다.');
				else if(o.val() != o2.val())
					error(o2, '이메일이 동일하여야 합니다.');
						
				// 비밀번호
				o = f.find('input[name=password]');
				o2 = f.find('input[name=confirm_password]');
				if(empty(o.val()))
					error(o, '필수 입력입니다.');
				else if(o.val() != o2.val())
					error(o2, '비밀번호가 동일하여야 합니다.');
					
				// 약관 
				o = f.find('input[name=term]');
				if(! o.is(':checked'))
					error(o, '동의하셔야 진행할 수 있습니다.');
	
				// 개인보호정책 
				o = f.find('input[name=privacy]');
				if(! o.is(':checked'))
					error(o, '동의하셔야 진행할 수 있습니다.');
				
			}else{
				/* setting */
				
				// 생년월일
				// -- 검증 필요없음 --	
			
			}
			// 성별
			o = f.find(':radio[name=gender]');
			if(!o.is(':checked'))
				error(o, '성별을 선택하여 주십시오.');
			
			// 이름
			o = f.find('input[name=realname]');
			if(empty(o.val()))
				error(o, '필수 입력입니다.');
					
			// 개인url
			o = f.find('input[name=username]');
			if(empty(o.val()))
				error(o, '필수 입력입니다.');
			else if(!username_checked)
				error(o, '이미 가입한 주소입니다.');
		}
	</script>
	
</fieldset>
