<?php if (!$this->input->is_ajax_request()): ?> 

<section class="listing" style="margin-top:20px;">
	<div class="container" style="width: 100% !important;">
		<div class="row">
			<div class="col-md-12">
<?php endif ?>

				<?php if (empty($rows)): ?>
				
					<div class="alert alert-info" style="color:#7a7880;text-align:center;">
					  	<strong>아직 업로드한 작품이 없습니다<strong>
						<br/>
						<?php if(isset($this->nf->get('user')->username) && $this->nf->get('user')->username == $user->row->username){ ?>
						<a href="/gallery/create">지금 등록해보세요</a>
						<?php } ?>
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