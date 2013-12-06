<fieldset id='auth_profile' <?php echo $this->uri->segment(2) == 'setting' ? 'style="margin-bottom:30px;"' : ''?>>
	<legend>개인정보</legend>
	
	<div class="row-fluid">
		<div class="span8">
			<div class='control-group'>	
				<label>블로그(홈페이지)</label>
				<input type='text' name='homepage' value="<?php echo $homepage?>" maxlength='255'/>
			</div>
			
			
			<div class='control-group'>	
				<label>페이스북</label>
				<span>http://www.facebook.com/</span>
				<input type='text' name='facebook_url' value="<?php echo $facebook_url?>" maxlength='255' style="display: block;"/>
			</div>
		</div>
		<div class="span8">
			<div class='upload_area_wrapper'>
				<div style="position:relative;top:30px;">
					<img src='<?php echo $profile_image?>' id='profile_image' class='thumbnail'/>
					<div style="position:relative;top:25px;">
						<div style='margin-bottom:10px;' class='wf-active'>
							프로필을 업로드해주세요<br/>
							최적사이즈는 100*100입니다
						</div>
						<div class="upload_area">
							<a class="pure-button-upload pure-button" style="background: url(/images/select_file.png) #363636 12px 12px no-repeat;">SELECT FILE</a>
							<input type="file" size="45" id="upload_profile_image" name="fileToUpload" class="input input-upload" data-sub="profile">
						</div>
					</div>
				</div>
			</div>
			<input type='hidden' id='thumbnail_url' name='thumbnail_url'/>
		</div>
	</div>

	<div class="row-fluid">
		<div class='span8 control-group'>	
			<label>트위터</label>
			<span>http://www.twitter.com/</span><input type='text' name='twitter_screen_name' value="<?php echo $twitter_screen_name?>" maxlength='255' style="display: block;"/>
		</div>
		<div class='span8 control-group'>	
		</div>
	</div>
		
	
	
	<?php if(MY_ID==0): ?>
			
		<ul class="pager">
			<li class="previous"><a href="#" class="prev" data-field="recommend">Prev</a></li>
		</ul>		
		<ul class="sign_up_ul">
			<li class="next"><a id='submit' class='pure-button'>Submit</a></li>
		</ul>
		<div class='center'>
		</div>
	<?php endif; ?>
	
	
	
	
	
	
	<script src="/js/ajaxfileupload.js"></script>
	<script>
		$('.input-upload').bind('change', function(){
			return ajaxFileUpload($(this).attr('id'), $(this).data('sub'));
		});

		// profile에 관한 폼 검증
		// register_form_view나 setting_form_view에서 호출된다.
		var check_profile = function(){
			var f = $('#auth_profile');
			f.find('label').children('span').remove(); // reset
			var o = o2 = '';
				
			// 블로그
			o = f.find('input[name=homepage]');
			if(!validation(o.val(), false, 'text'))
				error(o, '정상적으로 입력하여 주십시오.');

			// 페이스북
			o = f.find('input[name=facebook_url]');
			if(!validation(o.val(), false, 'facebook'))
				error(o, '정상적으로 입력하여 주십시오.');

			// 트위터
			o = f.find('input[name=twitter_screen_name]');
			if(!validation(o.val(), false, 'arabicnumber'))
				error(o, '정상적으로 입력하여 주십시오.');
				
			$('#thumbnail_url').val($('#profile_image').attr('src'));
		}		
	</script>	
</fieldset>
