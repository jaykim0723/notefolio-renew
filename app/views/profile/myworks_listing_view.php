<?php if (!$this->input->is_ajax_request()): ?>

<section class="listing">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<h1>작품리스트</h1>
<?php endif ?>

				<ul class="profile-thumbnail-list infinite-list">
					<!-- list -->
					<?php foreach ($rows as $key => $row): ?>
					<?php $this->load->view('profile/thumbnail_inc_view', $row) ?>
					<?php endforeach ?>
				</ul>
				
				<a href="/profile/myworks/<?php echo $username ?>/<?php echo ($page)?$page+1:2; ?>" class="more-link">more</a>


<?php if (!$this->input->is_ajax_request()): ?>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>