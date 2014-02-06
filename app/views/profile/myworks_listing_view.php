<?php if (!$this->input->is_ajax_request()): ?> 

<section class="listing">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
<?php endif ?>

				<?php if (empty($rows)): ?>
				
					<div class="alert alert-info">
					  <strong>게시물이 없습니다.</strong>
					  작품을 등록해주세요.
					</div>

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