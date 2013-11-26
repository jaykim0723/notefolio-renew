<?php if (!$this->input->is_ajax_request()): ?>


<section class="listing">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
<?php endif ?>
				<ul class="main_thumbnail_list infinite_list">
					<!-- list -->
					<?php foreach ($rows as $key => $row): ?>
					<?php $this->load->view('main/thumbnail_inc_view', $row) ?>
					<?php endforeach ?>
				</ul>

				<a href="/main/listing/<?php echo ($this->uri->segment(3))?$this->uri->segment(3)+1:2; ?>" class="more-link">more</a>
<?php if (!$this->input->is_ajax_request()): ?>
			</div>
		</div>
	</div>
</section>
<script>
	$(function(){
		
	})
</script>
<?php endif; ?>