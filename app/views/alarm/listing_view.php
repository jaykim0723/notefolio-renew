<?php if (!$this->input->is_ajax_request()): ?>

<section class="listing">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
<?php endif ?>
				<ul class="feed_activity_list alarm_list clearfix">
					<!-- list -->
					<?php foreach ($rows as $key => $row): ?>
					<?php $this->load->view('feed/activity_inc_view', $row) ?>
					<?php endforeach ?>
				</ul>
				<a href="/alarm/listing/<?php echo $page+1; ?>" class="alarm-more-link">more</a>
<?php if (!$this->input->is_ajax_request()): ?>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>

<?php if ($page==1): ?>
<script>
	$(function(){
		$('.alarm_list').waypoint('infinite', {
			items: '.alarm-item',
			more: '.alarm-more-link',
			offset: 'bottom-in-view'
		});	
	});
</script>
<?php endif ?>