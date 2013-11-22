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
			<div class="col-md-9">
				<div class="thumbnail_list">
<?php endif ?>
					<!-- list -->
					<?php foreach ($rows as $key => $row): ?>
					<?php $this->load->view('feed/thumbnail_inc_view', $row) ?>
					<?php endforeach ?>

<?php if (!$this->input->is_ajax_request()): ?>
				</div>
			</div>
			<div class="col-md-3">
				<ul class="feed_activity_list clearfix">
<?php endif ?>
					<!-- list -->
					<?php foreach ($rows as $key => $row): ?>
					<?php $this->load->view('feed/activity_inc_view', $row) ?>
					<?php endforeach ?>

<?php if (!$this->input->is_ajax_request()): ?>
				</ul>
			</div>
		</div>
	</div>
</section>
<script>
	$(function() {
		$('.thumbnail_list').waypoint('infinite', {
			items: '.thumbbox',
			onAfterPageLoad : function(){
				console.log($.now());
			}
		});
	});
	$(function() {
		$('.feed_activity_list').waypoint('sticky', {
		  stuckClass: 'stuck',
		  handler: function(){
		  	var offset = $(this).offset();
		  	$(this).css('top', offset.top+'px').css('left', offset.left+'px')
		  }
		});
	});
</script>
<?php endif; ?>