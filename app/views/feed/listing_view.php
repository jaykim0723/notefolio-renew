<?php if (!$this->input->is_ajax_request()): ?>

<section class="listing">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<ul class="nav nav-tabs">
					<li class="active"><a href="#">팔로워의 작품</a></li>
					<li><a href="#">팔로워의 활동</a></li>
				</ul>
			</div>
		</div>
		<div class="row">
			<div class="col-md-9">
				<div class="thumbnail_list infinit_scroll">
<?php endif ?>
					<!-- list -->
					<?php foreach ($rows as $key => $row): ?>
					<?php $this->load->view('gallery/thumbnail_inc_view', $row) ?>
					<?php endforeach ?>

<?php if (!$this->input->is_ajax_request()): ?>
				</div>
				<a href="/gallery/listing/2" class="infinite-more-link">more</a>
			</div>
			<div class="col-md-3">
				<div class="thumbnail_list infinit_scroll">
<?php endif ?>
					<!-- list -->
					<?php foreach ($rows as $key => $row): ?>
					<?php $this->load->view('gallery/thumbnail_inc_view', $row) ?>
					<?php endforeach ?>

<?php if (!$this->input->is_ajax_request()): ?>
				</div>
				<a href="/gallery/listing/2" class="infinite-more-link">more</a>
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
</script>
<?php endif; ?>