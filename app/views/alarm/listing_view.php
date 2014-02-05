<?php if (!$this->input->is_ajax_request()): ?>

<section class="listing">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
<?php endif ?>
				<ul id="alarm-list" class="feed-activity-list alarm-list clearfix">
					<!-- list -->
					<?php foreach ($rows as $key => $row): ?>
					<?php $this->load->view('feed/activity_inc_view', array('row'=>$row)) ?>
					<?php endforeach ?>
				</ul>
				<a href="/alarm/listing/<?php echo $page+1; ?>" class="alarm-more-link btn btn-default btn-block">more</a>
<?php if (!$this->input->is_ajax_request()): ?>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>

