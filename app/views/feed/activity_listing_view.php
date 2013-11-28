<ul class="feed_activity_list">
	<!-- list -->
	<?php foreach ($rows as $key => $row): ?>
	<?php $this->load->view('feed/activity_inc_view', $row) ?>
	<?php endforeach ?>

</ul>
<a href="/feed/activity_listing/<?php echo ($this->uri->segment(3))?$this->uri->segment(3)+1:2; ?>" class="activity-more-link">more</a>
