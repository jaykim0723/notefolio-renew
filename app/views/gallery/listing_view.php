<?php if (!$this->input->is_ajax_request()): ?>
<section id="search_form">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<form action="" class="well" style="height:200px;">
					조건입력창
				</form>
			</div>
		</div>
	</div>
</section>



<section class="listing">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
<?php endif ?>
				<div class="thumbnail_list">
					<!-- list -->
					<?php foreach ($rows as $key => $row): ?>
					<?php $this->load->view('gallery/thumbnail_inc_view', $row) ?>
					<?php endforeach ?>
				</div>
				<a href="/gallery/listing/2" class="infinite-more-link">more</a>
<?php if (!$this->input->is_ajax_request()): ?>
			</div>
		</div>
	</div>
</section>
<script>
	$(function() {
		$('.thumbnail_list').waypoint('infinite', {
  			container: '.thumbnail_list',
			items: '.thumbbox',
			onAfterPageLoad : function(){
				console.log($.now());
			}
		});
	});
</script>
<?php endif; ?>