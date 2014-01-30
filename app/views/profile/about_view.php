
<?php if (!$this->input->is_ajax_request()): ?>
<section>
	<div class="container">
		<div class="row">
			<div class="col-md-12">
<?php endif ?>
			
			<?php if($this->nf->get('user')->username == $user->row->username): ?>
			<a id="btn-update-about" class="pull-right btn btn-default">수정</a>
			<?php endif; ?>

			<div id="about-cont">
				<?php echo nl2br(htmlentities($row->contents, ENT_COMPAT, 'utf-8')); ?>
			</div>			

			<?php if($this->nf->get('user')->username == $user->row->username): ?>
			<div id="about-edit-area">
				<textarea name="about-text" id="about-text" cols="30" rows="10"></textarea>
				<ul id="about-attachments">
				</ul>
				<div class="centered">
					<button id="btn-submit-about" class="btn btn-primary">수정완료</button>
					<button id="btn-cancel-about" class="btn btn-default">취소</button>
				</div>
			</div>
			<script src="/js/libs/wysihtml5-0.3.0.min.js"></script>
			<script src="/js/libs/bootstrap-wysihtml5-0.0.2.js"></script>
			<script>
				if($('#style_wysihtml').length==0)
					$('head').append('<link id="style_wysihtml" rel="stylesheet" type="text/css" href="/css/bootstrap-wysihtml5-0.0.2.css"/>');
				$(function(){
					profileUtil.about.setGround();
				});
			</script>
			<?php endif; ?>


<?php if (!$this->input->is_ajax_request()): ?>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>