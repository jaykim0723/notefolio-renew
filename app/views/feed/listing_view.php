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
<?php endif ?>

				<div class="thumbnail_list infinite_list">
					<!-- list -->
					<?php foreach ($rows as $key => $row): ?>
					<?php $this->load->view('feed/thumbnail_inc_view', $row) ?>
					<?php endforeach ?>

				</div>
				<a href="/feed/listing/<?php echo ($this->uri->segment(3))?$this->uri->segment(3)+1:2; ?>" class="more-link">more</a>

<?php if (!$this->input->is_ajax_request()): ?>
			</div>
			<div class="col-md-3">
				<ul class="feed_activity_list infinite_list" data-name="feed">
<?php endif ?>
					<!-- list -->
					<?php foreach ($rows as $key => $row): ?>
					<?php $this->load->view('feed/activity_inc_view', $row) ?>
					<?php endforeach ?>

					<a href="/feed/activity_listing/<?php echo ($this->uri->segment(3))?$this->uri->segment(3)+1:2; ?>" class="more-link">more</a>
<?php if (!$this->input->is_ajax_request()): ?>
				</ul>
			</div>
		</div>
	</div>
</section>


<script>
	$(function() {
		// $('.feed_activity_list').waypoint('sticky', {
		//   stuckClass: 'stuck',
		//   handler: function(){
		//   	var offset = $(this).offset();
		//   	$(this).css('top', offset.top+'px').css('left', offset.left+'px')
		//   	$('.feed_activity_list', $(this)).css('width', $(this).width());
		//   }
		// });
	});
</script>
<?php endif; ?>