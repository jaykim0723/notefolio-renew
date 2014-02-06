
<?php if (!$this->input->is_ajax_request()): ?>
<section class="listing">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
<?php endif ?>

				<?php if (empty($rows)): ?>
				
					<div class="alert alert-info">
					  <strong>콜렉트 된 작품이 없습니다.</strong>
					  <br/>
					  작품을 콜렉트해주세요.
					</div>

				<?php else: ?>
				<ul class="thumbnail-list infinite-list">
					<!-- list -->
					<?php foreach ($rows as $key => $row): ?>
					<?php $this->load->view('gallery/thumbnail_inc_view', array('row'=>$row)) ?>
					<?php endforeach ?>
				</ul>
				<?php endif; ?>

				<a href="/<?php echo $this->uri->segment(1); ?>/<?php echo $this->uri->segment(2); ?>/<?php echo ($this->uri->segment(3))?$this->uri->segment(3)+1:2; ?>" class="more-link">more</a>
<?php if (!$this->input->is_ajax_request()): ?>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>