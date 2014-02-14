
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

				<a href="/<?php echo $this->uri->segment(1) ?>/<?php echo $mode ?>/<?php echo ($page)?$page+1:2; ?>" class="more-link btn btn-default btn-block btn-more" style="float:left;">more</a>
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