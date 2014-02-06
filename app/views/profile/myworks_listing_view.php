<?php if (!$this->input->is_ajax_request()): ?> 

<section class="listing">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<h1>작품리스트</h1>
<?php endif ?>

				<?php if (empty($rows)): ?>
				
					

				<?php else: ?>
				<ul class="profile-thumbnail-list infinite-list">
					<!-- list -->
					<?php foreach($rows as $key => $row): ?>
					<?php $this->load->view('profile/thumbnail_inc_view', array('row'=>$row)) ?>
					<?php endforeach ?>
				</ul>
				<a href="/profile/myworks/<?php echo $username ?>/<?php echo ($page)?$page+1:2; ?>" class="more-link">more</a>
				<?php endif ?>

  
<?php if (!$this->input->is_ajax_request()): ?>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>