
<?php if (!$this->input->is_ajax_request()): ?>
<section class="listing" style="margin-top:20px;">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
<?php endif ?>

				<?php if (empty($rows)): ?>
				
					<div class="alert alert-info">
					  <strong>아직 콜렉트 한 작품이 없습니다.</strong>
					</div>

				<?php else: ?>
				<ul class="thumbnail-list infinite-list">
					<!-- list -->
					<?php foreach ($rows as $key => $row): ?>
					<?php $this->load->view('gallery/thumbnail_inc_view', array('row'=>$row)) ?>
					<?php endforeach ?>
				</ul>

				<a href="/<?php echo $this->uri->segment(1); ?>/<?php echo $this->uri->segment(2); ?>/<?php echo ($page)?$page+1:2; ?>" class="more-link">more</a>
<?php if (!$this->input->is_ajax_request()): ?>
				<?php endif; ?>
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