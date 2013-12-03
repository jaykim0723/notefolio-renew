<script>
	NFview = {};
</script>

<section class="visible-md visible-lg">
	<?php echo form_open('/gallery/save', array('id'=>'gallery_form', 'class'=>'container', 'role'=>'form')); ?>
		<div class="row">
			<!-- 작품영역 시작 -->
			<div class="col-md-8">
				<h4>제목</h4>
				<input type="text" class="form-control input-lg col-md-12" placeholder="Title"/>
				<br>
				<h4>내용</h4>
				<ul id="content-block-list" class="list-unstyled">
					<li>
						<div class="well" style="height:600px;">
							작품 블럭영역
						</div>
					</li>
					<li>
						<div class="well" style="height:100px;">
							작품 블럭영역
						</div>
					</li>
					<li>
						<div class="well" style="height:200px;">
							작품 블럭영역
						</div>
					</li>
				</ul>
				<?php echo form_open(''); ?>
				<?php echo form_close(); ?>
			</div>
			<!-- 작품영역 끝 -->


			<!-- 사이드바 시작 -->
			<div class="col-md-4 ">
				<div class="sticky">
					<h4>키워드</h4>
					<select name="work_categories" id="work_categories" multiple title="Choose one of the following..." >
						<option value="A7">가구디자인</option>
						<option value="B7">그리픽디자인</option>
						<option value="C7">디지털아</option>
						<option value="D7">산업디자인</option>
						<option value="E7">실내디자인</option>
						<option value="F7">웹디자인</option>
						<option value="G7">제품디자인</option>
						<option value="H7">페인팅</option>
						<option value="I7">건축디자인</option>
						<option value="J7">금속디자인</option>
						<option value="K7">모션그래픽</option>
						<option value="L7">설치</option>
					</select>

					<h4>태그</h4>
					<input id="tags" name="" type="text" class="form-control" data-role="tagsinput">
					<script>
						$.getScript('/js/libs/bootstrap-tagsinput.min.js');
						if($('#style_tagsinput').length==0)
							$('head').append('<link id="style_tagsinput" href="/css/bootstrap-tagsinput.css" rel="stylesheet"/>');
					</script>

					
					<h4>CCL</h4>
					<select name="work_ccl" id="work_ccl" class="show-tick" title="Choose one of the following..." >
						<option value="">CCL 표시 안함</option>
						<option data-content="<img src='http://dev.notefolio.net/images/ccl/y0.png'/>저작자표시" value="BY">저작자표시</option>
						<option value="BY-NC">저작자표시-비영리</option>
						<option value="BY-ND">저작자표시-변경금지</option>
						<option value="BY-SA">저작자표시-동일조건변경허락</option>
						<option value="BY-NC-SA">저작자표시-비영리-동일조건변경허락</option>
						<option value="BY-NC-ND">저작자표시-비영리-변경금지</option>
					</select>					


					<h4>커버</h4>

				<div class="row">
					<div class="col-md-4">
						<div class="well">
							커버
						</div>
					</div>
					<div class="col-md-4">
						<div class="well">
							커버
						</div>
					</div>
					<div class="col-md-4">
						<div class="well">
							커버
						</div>
					</div>
				</div>
				<input type="hidden" name="work_id" value="<?php echo $work_id ?>"/>
				<button type="submit" class="btn btn-primary btn-block btn-lg">
					전송
				</button>
				</div>
			</div>
			<!-- 사이드바 끝 -->
		</div>
	<?php echo form_close(); ?>
</section>
<div id="work-content-blockadder">
	<ul class="list-unstyled">
		<li class="block-text"><i class="glyphicon glyphicon-pencil"></i></li>
		<li class="block-image"><i class="glyphicon glyphicon-picture"></i></li>
		<li class="block-video"><i class="glyphicon glyphicon-film"></i></li>
		<li class="trash-can"><i class="glyphicon glyphicon-remove"></i></li>
	</ul>
</div>
<script>
	NFview = <?php
		echo json_encode($_ci_vars); // view내의 스크립트에서 편리하게 사용하기 위하여 미리 할당
	?>;
	$(function() {
		// form이 전송이 되면 hook하여 ajax로 호출을 한다.
		$('#gallery_form').on('submit', function(e){
			e.preventDefault();
			e.stopPropagation();
			workUtil.save($(this));
		})
		//Content Ground Setting 살림.
		workUtil.content.setGround('#content-block-list', '.trash-can');
		workUtil.content.setTool('.block-text, .block-image, .block-video', '#work-content-blockadder', '#content-block-list');
		workUtil.content.setForRemove('.trash-can');
	});
</script>

<!-- 데스크탑 모드에서만 업로드 관련하여 작업할 수 있도록 하기 -->
<section class="visible-xs visible-sm">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="alert alert-danger">
					작품 수정에 관한 것은 웹에서만 할 수 있습니다.
				</div>
			</div>
		</div>
	</div>
</section>

