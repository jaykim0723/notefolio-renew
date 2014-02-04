<ul id="feed-activity-list" class="feed-activity-list">
	<!-- list -->
	<?php foreach ($rows as $key => $row): ?>
	<?php $this->load->view('feed/activity_inc_view', array('row' => $row)) ?>
	<?php endforeach ?>

</ul>
<a href="/feed/activity_listing/<?php echo ($this->uri->segment(3))?$this->uri->segment(3)+1:2; ?>" class="activity-more-link btn btn-default btn-more btn-block">more</a>
