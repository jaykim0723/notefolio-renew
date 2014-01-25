
<?php if (!$this->input->is_ajax_request()): ?>
<section>
	<div class="container">
		<div class="row">
			<div class="col-md-12">
<?php endif ?>
			
			<?php if($this->nf->get('user')->username == $user->row->username): ?>
			<a href="./about/update" class="pull-right btn btn-default">수정</a>
			<?php endif; ?>

			<div id="about-cont">
				<?php echo nl2br(htmlentities($row->contents, ENT_COMPAT, 'utf-8')); ?>
			</div>			

<?php if (!$this->input->is_ajax_request()): ?>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>