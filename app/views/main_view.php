<?php if (!$this->input->is_ajax_request()): ?>
<section class="container" id="cont_container">
	<div class="row">
		<div class="col-md-9 col-sm-12">
<?php endif ?>

			<div class="thumbnail-list infinite-list">
				<!-- list -->
				<?php foreach ($rows as $key => $row): ?>
				<?php $this->load->view('gallery/thumbnail_inc_view', $row) ?>
				<?php endforeach ?>
			</div>

			<a href="/gallery/listing/<?php echo ($this->uri->segment(3))?$this->uri->segment(3)+1:2; ?>" class="more-link">more</a>
			
<?php if (!$this->input->is_ajax_request()): ?>
		</div>

		<div class="col-md-3 hidden-sm hidden-xs">
			sidebar
		</div>



	</div>   
</section>
<?php endif ?>