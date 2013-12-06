<fieldset id='auth_keywords' <?php echo $this->uri->segment(2) == 'setting' ? 'style="margin-bottom:30px;"' : ''?>>
	<legend>
		키워드
		<span class="hint">
			본인을 가장 잘 표현할 수 있는 키워드를 선택해주세요(최대 3개까지 가능합니다).
		</span>
	</legend>
	<div></div>
	<div id='work_categories'>
		<?	$this->load->config('notefolio');
			foreach($this->config->item('categories') as $k=>$v): ?>
			<div class='cate_option' data-key="<?php echo $k?>"><?php echo $v?></div>
		<?php endforeach; ?>
	</div>
	<br style='clear:left;'/>
	<input type='hidden' id='keywords' name='categories' value="<?php echo $categories?>"/>
	<script>
		// binding
		$('#work_categories > div').click(function(){
			if($(this).hasClass('selected')==false && $('#work_categories > .selected').length == 3){
				msg.error('최대 3개까지만 선택 가능합니다.');
				return false;
			}
			$(this).toggleClass('selected');
		});
		// set values
		$('#work_categories').children('div[data-key=<?php echo @implode("],div[data-key=", $categories)?>]').addClass('selected');
	</script>
	
	<?php if(MY_ID==0): ?>
	
	<ul class="pager">
		<li class="previous"><a href="#" class="prev" data-field="basic">Prev</a></li>
		<li class="next"><a href="#" class="next" data-field="recommend">Next</a></li>
	</ul>		
	<?php endif; ?>
	
	
	<script>
		// keywords에 관한 폼 검증
		// register_form_view나 setting_form_view에서 호출된다.
		var check_keywords = function(){
			// 하나 이상 선택을 했는지 검사를 한다.
			var o = $('#work_categories').prev().empty();
			if(o.next().children('.selected').length == 0){
				o.append('<span class="error">키워드를 하나 이상 선택하셔야 합니다.</span>');
			}else{
				// hidden에 값을 배정한다.
				var selected = [];
				o.next().children('.selected').each(function(){
					selected.push($(this).data('key'));
				});
				$('#keywords').val(selected.join(','));
			}
		}
	</script>
	
</fieldset>


