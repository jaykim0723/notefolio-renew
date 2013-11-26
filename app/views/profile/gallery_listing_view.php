<?php if (!$this->input->is_ajax_request()): ?>

<?php $this->load->view('profile/header_view'); ?>


<section class="listing">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<h1>작품리스트</h1>
<?php endif ?>
				<ul class="thumbnail_list infinite_list">
					<!-- list -->
					<?php foreach ($rows as $key => $row): ?>
					<?php $this->load->view('gallery/thumbnail_inc_view', $row) ?>
					<?php endforeach ?>
				</ul>

				<a href="/gallery/listing/<?php echo ($this->uri->segment(3))?$this->uri->segment(3)+1:2; ?>" class="more-link">more</a>
<?php if (!$this->input->is_ajax_request()): ?>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>