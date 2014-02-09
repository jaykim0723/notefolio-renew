<?php if (!$this->input->is_ajax_request()): ?> 

<section class="listing" style="padding-top:20px;">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
<?php endif ?>

				<?php if (empty($rows)): ?>
				
					<div class="alert alert-info">
					  	<strong>아직 업로드한 작품이 없습니다.<strong>
						<br/>
						<a href="/gallery/create">지금 등록해보세요</a>
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
<?php if ($page==1): ?>
<script>
	site.restoreInifiniteScroll();
</script>
<?php endif ?>



<?php endif; ?>