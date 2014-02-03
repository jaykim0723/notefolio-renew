<?php if (!$this->input->is_ajax_request()): ?>

<section class="listing">
	<div class="container">
		<div class="row visible-sm visible-xs">
			<div class="col-md-12">
				<ul class="nav nav-tabs">
					<li class="active"><a href="#">팔로워의 작품</a></li>
					<li><a href="#">팔로워의 활동</a></li>
				</ul>
			</div>
		</div>
		<div class="row">
			<div class="col-md-9 col-sm-12 col-xs-12">
<?php endif ?>



				<ul id="feed-list" class="thumbnail-list infinite-list">
					<!-- list -->
					<?php foreach ($rows as $key => $row): ?>
					<?php $this->load->view('feed/thumbnail_inc_view', $row) ?>
					<?php endforeach ?>

				</ul>
				<a href="/feed/listing/<?php echo ($page)?$page+1:2; ?>" class="more-link btn btn-default btn-block btn-more">more</a>



<?php if (!$this->input->is_ajax_request()): ?>
			</div>
			<div class="col-md-3 hidden-sm hidden-xs">
				<?php echo $this->load->view('feed/activity_listing_view', $activity); ?>
			</div>
		</div>
	</div>
</section>

<?php if ($page==1): ?>
<script>
	$(function(){
		$(document).on('click', '.activity-more-link', function(event){
			event.preventDefault();
			event.stopPropagation();
			$.get($(this).attr('href'), {}).done(function(responseHTML){
				var $container = $('#feed-activity-list');
				var $response = $('<div>'+responseHTML+'</div>');
				var $lis = $('li.activity-infinite-item', $response);
				if($lis.length > 0){
					$('.activity-more-link', $response).insertAfter($container);
					$lis.appendTo($container);
					NFview.infiniteCallback();
				}
			});
			$(this).remove();
		});
		NFview.infiniteCallback = function(){
			// 두 리스트의 길이차를 분석하여, 너무 차이가 많이 나면 하나씩 등록을 해준다.
			var gap = $('#feed-list').outerHeight() - $('#feed-activity-list').outerHeight();
			if(gap>400){
				$('.activity-more-link').trigger('click');
			}else if(gap < -400){
				$('.more-link').trigger('click');
			}
		};
	});
</script>
<?php endif; ?>


<?php endif; ?>