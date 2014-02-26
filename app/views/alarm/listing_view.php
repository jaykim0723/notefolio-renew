<?php if (!$this->input->is_ajax_request()): ?>

<section class="listing">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				
<?php endif ?>
				<ul id="alarm-list" class="feed-activity-list alarm-list clearfix">
					<div id="alarm-popup-title">Notifications</div>
					<!-- list -->
					<?php foreach ($rows as $key => $row): ?>
					<?php $this->load->view('feed/activity_inc_view', array('row'=>$row)) ?>
					<?php endforeach ?>
					<?php if($this->uri->segment(3)==1 && count($rows)==0) { ?>
					<li class="empty-list">
						새로운 알림이 없습니다.
					</li>
					<?php } ?>
				</ul>
				<?php if(count($rows)>0){ ?>
				<a href="/alarm/listing/<?php echo $page+1; ?>" id="alarm-more" class="alarm-more-link btn btn-default btn-block btn-alarm-more alarm-height"><i class="spi spi-down"></i><i class="spi spi-down_point"></i>see more</a>
				<?php } ?>
<?php if (!$this->input->is_ajax_request()): ?>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>

