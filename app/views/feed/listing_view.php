<?php if (!$this->input->is_ajax_request()): ?>

<section class="listing">
	<div class="container">
		<div class="row visible-sm visible-xs">
			<div class="col-md-12">
				<ul id="feed-tab" class="nav nav-tabs">
					<li rel="feed-list-wrapper" class="active"><a href="#">팔로워의 작품</a></li>
					<li rel="activity-list-wrapper"><a href="#">팔로워의 활동</a></li>
				</ul>
			</div>
		</div>
		<div class="row">
			<div id="feed-list-wrapper" class="col-md-9 col-sm-12 col-xs-12">
<?php endif ?>

				<ul id="feed-list" class="thumbnail-list infinite-list">
					<!-- list -->
					<?php foreach ($rows as $key => $row): ?>
					<?php $this->load->view('gallery/thumbnail_inc_view', array('row'=>$row->data)) ?>
					<?php endforeach ?>

				</ul>
				<a href="/feed/listing/<?php echo ($page)?$page+1:2; ?>" class="more-link btn btn-default btn-block btn-more">more</a>



<?php if (!$this->input->is_ajax_request()): ?>
			</div>
			<div id="activity-list-wrapper" class="col-md-3">
				<?php echo $this->load->view('feed/activity_listing_view', $activity); ?>
			</div>
		</div>
	</div>
</section>


<?php if ($page==1): ?>
<script>
	$(function(){
		$('.unread-feed').hide();
		
		$('#feed-tab li').on('click', function(){
			var $tab = $('#feed-tab');
			$tab.children('li').removeClass('active');
			$(this).addClass('active');
			var activated = $(this).attr('rel');
			$('#feed-list-wrapper, #activity-list-wrapper').toggle();
			if(activated=='feed-list-wrapper'){
				$('#feed-list').removeClass('disabled');
			}else{
				$('#feed-list').addClass('disabled');
			}
		});
		NFview.lastMode = $(window).width() > 991 ? 'large' : 'small';
		$(window).on('resize', function(){
			var currentWidth = $(this).width();
			if(currentWidth > 991 && NFview.lastMode=='small'){
				$('#feed-list-wrapper, #activity-list-wrapper').show();
				$('#feed-list').removeClass('disabled');
				NFview.lastMode = 'large';
			}else if(currentWidth <= 991 && NFview.lastMode=='large'){
				var activated = $('#feed-tab li.active').attr('rel');
				$('#feed-list-wrapper, #activity-list-wrapper').hide();
				$('#'+activated).show();
				if(activated=='feed-list-wrapper'){
					$('#feed-list').removeClass('disabled');
				}else{
					$('#feed-list').addClass('disabled');
				}
				NFview.lastMode = 'small';
			}
		});

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
			if($(window).width() <= 991) return;
			var gap = $('#feed-list').outerHeight() - $('#feed-activity-list').outerHeight();
			if(gap>400){
				$('.activity-more-link').trigger('click');
			}else if(gap < -400){
				$('.more-link').trigger('click');
			}
		};

		site.restoreInifiniteScroll();

		$('.more-link, .activity-more-link').trigger('click');
	});
</script>
<?php endif; ?>


<?php endif; ?>