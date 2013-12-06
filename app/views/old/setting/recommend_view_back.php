<fieldset id='auth_recommend'>
	<legend>나와 비슷한 사람들</legend>
	<!--
	<a herf='javascript:;' class='pure-button btn-follow follow' id='btnFollowAll'>Follow All</a>
	<script>
		$('#btnFollowAll').click(function(){
			currentFollowing = [];
			if($(this).hasClass('pure-button-selected')){ // unfollow all
				$('#follow_area a.follow').removeClass('pure-button-selected').html('Follow');
			}else{
				$('#follow_area a.follow').addClass('pure-button-selected').html('Following').each(function(){
					currentFollowing.push($(this).data('id'));
				});
			}
			$(this).toggleClass('pure-button-selected');
		});
	</script>
	-->
	<input type='hidden' id='recommend' name='recommend' value=""/>
	
	<div id='follow_area'>
	</div>
	<div style='clear:left;'>
	</div>

	<ul class="pager">
		<li class="previous"><a href="#" class="prev" data-field="keywords">Prev</a></li>
		<li class="next"><a href="#" class="next" data-field="profile">Next</a></li>
	</ul>		

	<script>
		var currentFollowing = []; // follow 리스트가 새로 불러지더라도, 기존에 선택된 것은 유지되도록 하기 위하여 여기에 저장한다. 추후에 전송할 때도 이값을 기준으로 한다.
		
		// 이전 단계에서 선택된 키워드에 따라서,
		// 매번 탭이 열릴 때마다 불러온다.
		// 아래 함수는 view/profile/register_form_view.php 에서 호출한다.
		var sRecommend = function(){
			var categories = [];
			$('#work_categories > .selected').each(function(){
				categories.push($(this).data('key'));
			});
			blockObj.block('follow_area', '#follow_area');
			$.get('/auth/recommend', {
				categories : categories
			}, function(d){
				blockObj.unblock('follow_area');
				$('#follow_area').html(d).find('a.follow').bind('click', function(){
					// 클릭이 먹히도록 바인딩한다.
					if($(this).hasClass('pure-button-selected')){
						$(this).text('follow').removeClass('pure-button-selected');
						/*var index = currentFollowing.indexOf($(this));
						currentFollowing.splice(index,1);*/
						var resource = $(this);
						currentFollowing = $.grep(currentFollowing, function(value) {
							  return value != $(resource).data('id');
						});
						
						/*currentFollowing.slice( $.inArray($(this).data('id'),currentFollowing) , 0); //currentFollowing 에서 제거*/
					}else{
						$(this).text('following').addClass('pure-button-selected');
						currentFollowing.push($(this).data('id')); // currentFollowing에 추가
					}
				}).each(function(){
					if($.inArray($(this).data('id'), currentFollowing) > -1)
						$(this).addClass('pure-button-selected').text('following');
				});
			});
		};
		
		
		// recommend에 관한 폼 검증
		// register_form_view나 setting_form_view에서 호출된다.
		var check_recommend = function(){
			// 옵션이니 아무것도 선택 안해도 된다
			
			// 값만 할당
			$('#recommend').val(currentFollowing.join(','));
			
			// 무조건 통과
			return true;
		}
		
	</script>

</fieldset>