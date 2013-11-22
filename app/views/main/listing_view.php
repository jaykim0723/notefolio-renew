<?php if (!$this->input->is_ajax_request()): ?>


<section class="listing">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="well" style="height:400px;background: #eee;">
					복합구성
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-9 col-sm-12 col-xs-12">
<?php endif ?>
				<div class="thumbnail_list">
					<!-- list -->
					<?php foreach ($rows as $key => $row): ?>
					<?php $this->load->view('gallery/thumbnail_inc_view', $row) ?>
					<?php endforeach ?>
				</div>

				<a href="/gallery/listing/<?php echo ($this->uri->segment(3))?$this->uri->segment(3)+1:2; ?>" class="more-link">more</a>
<?php if (!$this->input->is_ajax_request()): ?>
			</div>
			<div class="col-md-3 hidden-sm hidden-xs">
				<div class="well" style="background: #eee;height:500px;">
					최근작품
				</div>
				<div class="well" style="background: #eee;height:500px;">
					HOT Creator
				</div>
			</div>
		</div>
	</div>
</section>

<?php endif; ?>