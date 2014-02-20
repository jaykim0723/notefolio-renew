
<?php if (!$this->input->is_ajax_request()): ?>
<section>
	<div class="container">
		<div class="row">
			<div class="col-md-12">
<?php endif ?>

			<div id="about-container">
				
				<?php if(isset($this->nf->get('user')->username) && $this->nf->get('user')->username == $user->row->username): ?>
				<a id="btn-update-about" class="pull-right btn btn-default">수정</a>
				<?php endif; ?>

				<?php if(!empty($row->contents)){ ?>
				<div id="about-cont">
					<?php echo nl2br($row->contents); ?>
				</div>
				<?php } else { ?>
				<div id="about-cont empty-about">
					About을 작성하지 않았습니다. 
					<?php if($this->nf->get('user')->username == $user->row->username){ ?>
					<a href="javascript:$('#btn-update-about').trigger('click');">지금 작성해 보세요.</a>
					<?php } ?>
				</div>
				<?php } ?>

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
				<script src="/js/libs/cleditor/jquery.cleditor.js"></script>
				<script>
					if($('#style_cleditor').length==0)
						$('head').append('<link id="style_cleditor" rel="stylesheet" type="text/css" href="/js/libs/cleditor/jquery.cleditor.css"/>');
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