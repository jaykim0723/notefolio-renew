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

				<ul class="thumbnail_list infinite_list">
					<!-- list -->
					<?php foreach ($rows as $key => $row): ?>
					<?php $this->load->view('feed/thumbnail_inc_view', $row) ?>
					<?php endforeach ?>

				</ul>
				<a href="/feed/listing/<?php echo ($page)?$page+1:2; ?>" class="more-link">more</a>

<?php if (!$this->input->is_ajax_request()): ?>
			</div>
			<div class="col-md-3 hidden-sm hidden-xs">
				<?php echo $this->load->view('feed/activity_listing_view', $activity); ?>
			</div>
		</div>
	</div>
</section>


<script>
	$(function() {
		$('.feed_activity_list').waypoint('infinite', {
			items: '.activity-infinite-item',
			more: '.activity-more-link',
			offset: 'bottom-in-view',
			onAfterPageLoad : function(){
				console.log($.now());
			}
		});
	});
</script>
<?php endif; ?>