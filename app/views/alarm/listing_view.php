<?php if (!$this->input->is_ajax_request()): ?>

<section class="listing">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				
<?php endif ?>
				<ul id="alarm-list" class="feed-activity-list alarm-list clearfix">
					<div id="alarm-popup-title">Alarm</div>
					<!-- list -->
					<?php foreach ($rows as $key => $row): ?>
					<?php $this->load->view('feed/activity_inc_view', array('row'=>$row)) ?>
					<?php endforeach ?>
				</ul>
				<a href="/alarm/listing/<?php echo $page+1; ?>" id="alarm-more" class="alarm-more-link btn btn-default btn-block btn-alarm-more" style="line-height: 230%;"><i class="spi spi-down"></i><i class="spi spi-down_point"></i>see more</a>
<?php if (!$this->input->is_ajax_request()): ?>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>

