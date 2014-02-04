
<?php if (!$this->input->is_ajax_request()): ?>
<section>
	<div class="container">
		<div class="row">
			<div class="col-md-12">
<?php endif ?>

			<div id="about-container">
				
				<?php if($this->nf->get('user')->username == $user->row->username): ?>
				<a id="btn-update-about" class="pull-right btn btn-default">수정</a>
				<?php endif; ?>

				<div id="about-cont">
					<?php echo nl2br($row->contents); ?>
				</div>			

				<?php if($this->nf->get('user')->username == $user->row->username): ?>
				<div id="about-edit-area">
					<textarea name="about-text" id="about-text" cols="30" rows="20"></textarea>
					<ul id="about-attachments">
						<li id="about-upload">
							<span class="spi spi-plus">plus</span>
							<br/>
							파일첨부
						</li>
						<br class="clearfix"/>
					</ul>
					<div class="centered clearfix">
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
			</div>


<?php if (!$this->input->is_ajax_request()): ?>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>