<?php if (!$this->input->is_ajax_request()): ?>

<section class="listing">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<ul class="feed_activity_list clearfix">
<?php endif ?>
					<!-- list -->
					<?php foreach ($rows as $key => $row): ?>
					<?php $this->load->view('alarm/activity_inc_view', $row) ?>
					<?php endforeach ?>

<?php if (!$this->input->is_ajax_request()): ?>
				</ul>
			</div>
		</div>
	</div>
</section>

<?php endif; ?>