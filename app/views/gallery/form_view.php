<script>
	NFview = <?php
		echo json_encode($row); // view내의 스크립트에서 편리하게 사용하기 위하여 미리 할당
	?>;
	NFview.area = 'work-form';
</script>

<?php echo form_open('/gallery/save', array('id'=>'gallery_form', 'role'=>'form')); ?>

<div id="work-sidebar">
	<div class="container">
		<div class="row">
			<div class="col-md-9">
				<!-- empty -->
			</div>

			<!-- 사이드바 시작 -->
			<div class="col-md-3 ">
				<h4>공개여부</h4>
				<div class="control-group">
                    <label class="notefolio-radio inline<?if($row->status=='enabled'){?> checked<?}?>">
                        <input type="radio" name="status" value="enabled" <?if($row->status=='enabled'){?> checked<?}?>> 공개
                    </label>
                    &nbsp; &nbsp; &nbsp;
                    <label class="notefolio-radio inline<?if($row->status=='disabled'){?> checked<?}?>">
                        <input type="radio" name="status" value="disabled" <?if($row->status=='disabled'){?> checked<?}?>> 비공개
                    </label>
				</div>

				<h4>키워드</h4>

				<select name="keywords" id="keywords" multiple title="Choose one of the following..." >
					<?php 
					$this->load->config('keyword', TRUE);
					$keyword_list = $this->config->item('keyword', 'keyword');

					foreach ($keyword_list as $key => $keyword) { ?>
						<option value="<?php echo $key?>"><?php echo $keyword;?></option>
					<?php }	?>
				</select>

				<h4>태그</h4>
				<input id="tags" name="tags" type="text" class="form-control">

				
				<h4>CCL</h4>
				<select name="ccl" id="ccl" class="" title="Choose one of the following..." >
					<option value="">CCL 표시 안함</option>
					<option data-content='<i class="spi spi-ccl-cc-by"></i>저작자표시' value="BY">저작자표시</option>
					<option data-content='<i class="spi spi-ccl-cc-by-nc"></i>저작자표시-비영리' value="BY-NC">저작자표시-비영리</option>
					<option data-content='<i class="spi spi-ccl-cc-by-nd"></i>저작자표시-변경금지' value="BY-ND">저작자표시-변경금지</option>
					<option data-content='<i class="spi spi-ccl-cc-by-sa"></i>저작자표시-동일조건변경허락' value="BY-SA">저작자표시-동일조건변경허락</option>
					<option data-content='<i class="spi spi-ccl-cc-by-nc-sa"></i>저작자표시-비영리-동일조건변경허락' value="BY-NC-SA">저작자표시-비영리-동일조건변경허락</option>
					<option data-content='<i class="spi spi-ccl-cc-by-nc-nd"></i>저작자표시-비영리-변경금지' value="BY-NC-ND">저작자표시-비영리-변경금지</option>
				</select>					


				<div>
					<div class="pull-right btn-group">
						<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
						 	커버업로드
						  <span class="caret"></span>
						</button>
						<ul class="dropdown-menu">
						  <li><a id="btn-upload-cover" href="#">커버 업로드</a></li>
						  <li><a id="btn-select-cover" href="#">작품내용 중 선택</a></li>
						  <li><a href="javascript:memberUtil.popCrop({'message':['정사각형 메인을 정해주세요.','직사각형 보고 커버를 정해주세요.']});">크롭테스트(임시)</a></li>
						</ul>
					</div>	
					<h4>커버</h4>
				</div>

				<div class="row" id="cover-preview">
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


				<input type="hidden" name="work_id" value="<?php echo $row->work_id ?>"/>
				<input type="hidden" name="cover_upload_id" value="<?php echo $row->work_id ?>"/>

				<button id="work-submit" type="submit" class="btn btn-primary btn-block btn-lg">
					<span id="work-discoverbility"><span style="width:70%;"></span></span>
					전송
				</button>
			</div>
			<!-- 사이드바 끝 -->
		</div>
	</div>
</div>

<section class="visible-md visible-lg">
	<div class="container">
		<div class="row">
			<!-- 작품영역 시작 -->
			<div class="col-md-9">
				<h4>제목</h4>
				<input id="title" type="text" class="form-control input-lg col-md-12" placeholder="Title"/>
				<br>
				<h4>내용</h4>
				<ul id="content-block-list" class="work-info list-unstyled work-contents">
				</ul>
			</div>
			<!-- 작품영역 끝 -->

			<div class="col-md-3">
				<!-- empty -->	
			</div>
		</div>
	</div>
</section>

<?php echo form_close(); ?>


<ul class="list-unstyled" id="work-content-blockadder">
	<li class="block-text"><i class="glyphicon glyphicon-pencil"></i></li>
	<li class="block-image"><i class="glyphicon glyphicon-picture"></i></li>
	<li class="block-video"><i class="glyphicon glyphicon-film"></i></li>
	<li class="block-line"><i class="glyphicon glyphicon-minus"></i></li>
	<li id="trash-bin" class="glyphicon glyphicon-remove">&nbsp;</li>
</ul>
<script>
	$(function() {
		// form이 전송이 되면 hook하여 ajax로 호출을 한다.
		$('#gallery_form').on('submit', function(e){
			e.preventDefault();
			e.stopPropagation();
			workUtil.save($(this));
		})
		//Content Ground Setting 살림.
		workUtil.content.setGround('#content-block-list', '.trashable');
		workUtil.content.setTool('.block-text, .block-image, .block-video, .block-line', '#work-content-blockadder', '#content-block-list');
		workUtil.content.setTrashBin('#trash-bin');
		workUtil.content.restoreContents();

	});
	if($('#style_tagsinput').length==0)
		$('head').append('<link id="style_tagsinput" href="/css/bootstrap-tagsinput.css" rel="stylesheet"/>');
	if($('#style_wysihtml').length==0)
		$('head').append('<link id="style_wysihtml" rel="stylesheet" type="text/css" href="/css/bootstrap-wysihtml5-0.0.2.css"/>');
	if($('#style_crop').length==0)
		$('head').append('<link id="style_crop" rel="stylesheet" type="text/css" href="/css/crop/jquery.Jcrop.css"/>');

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



<script src="/js/member.js"></script>
<script src="/js/libs/jquery.Jcrop.min.js"></script>
<script src="/js/libs/bootstrap-tagsinput.min.js"></script>
<script src="/js/libs/wysihtml5-0.3.0.min.js"></script>
<script src="/js/libs/bootstrap-wysihtml5-0.0.2.min.js"></script>
