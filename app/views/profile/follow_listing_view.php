
<?php if (!$this->input->is_ajax_request()): ?>
<section class="listing">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
<?php endif ?>
				<ul class="follow-list infinite-list mode-<?php echo $mode ?>">
					<!-- list -->
					<?php foreach ($rows as $key => $row): ?>
					<?php $this->load->view('profile/follow_inc_view', array('row'=>$row)) ?>
					<?php endforeach ?>
				</ul>

				<a href="/<?php echo $this->uri->segment(1) ?>/<?php echo $mode ?>/<?php echo ($this->uri->segment(3))?$this->uri->segment(3)+1:2; ?>" class="more-link">more</a>
<?php if (!$this->input->is_ajax_request()): ?>
			</div>
		</div>
	</div>
</section>

<?php endif; ?>