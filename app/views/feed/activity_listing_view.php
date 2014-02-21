<ul id="feed-activity-list" class="feed-activity-list">
	<!-- list -->
	<?php foreach ($rows as $key => $row): ?>
	<?php $this->load->view('feed/activity_inc_view', array('row' => $row)) ?>
	<?php endforeach ?>
	
	<?php if($this->uri->segment(2) != 'listing' && ($this->uri->segment(3)==FALSE || $this->uri->segment(3)==1) && count($rows)==0) { ?>
	<li class="empty-list">
		새로운 피드가 없습니다. 다른 회원을 팔로우해보세요.
	</li>
	<?php } ?>
</ul>
<a href="/feed/activity_listing/<?php echo ($this->uri->segment(3))?$this->uri->segment(3)+1:2; ?>" class="activity-more-link btn btn-default btn-more btn-block">more</a>
