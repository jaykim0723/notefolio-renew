<?php
	$keywords_list_key = $this->nf->category_to_array($row->keywords, TRUE);
?>
<script src="/js/member.js"></script>
<script>
	NFview = <?php
		echo json_encode($row); // view내의 스크립트에서 편리하게 사용하기 위하여 미리 할당
	?>;
	NFview.keywords = <?php echo json_encode($keywords_list_key); ?>;
	NFview.area = 'work-form';
</script>

<?php echo form_open('/gallery/save', array('id'=>'gallery-form', 'role'=>'form')); ?>


<section id="work-form" class="visible-md visible-lg">
	<div class="container">
		<div class="row">
			<!-- 작품영역 시작 -->
			<div class="col-md-9">
				<h4>제목</h4>
				<input id="title" name="title" type="text" class="form-control input-lg col-md-12" placeholder="Title"/>
				<br>
				<h4>내용</h4>
				<ul style="margin-top:0;" id="content-block-list" class="work-info list-unstyled work-contents">
				</ul>
				<div id="content-multiple">
					<img src="/img/uploadcont2.png" alt=""/>
				</div>
			</div>
			<!-- 작품영역 끝 -->

			<div id="work-sidebar-inner" class="col-md-3">

				<h4>카테고리</h4>
				<select name="keywords" id="keywords" multiple title="최대 2개까지 선택">
					<?php 
					$this->load->config('keyword', TRUE);
					$keyword_list = $this->config->item('keyword', 'keyword');

					foreach ($keyword_list as $key => $keyword) { ?>
						<option value="<?php echo $key?>"<?=(in_array($key, $keywords_list_key))?' selected':''?>><?php echo $keyword;?></option>
					<?php }	?>
				</select>

				<div id="ccl-wrapper">
					<h4>CCL <a class="tip" href="/info/faq#ccl" target="_blank">자세히보기</a></h4>
					<select name="ccl" id="ccl" class="" title="Choose one of the following...">
						<option value="">CCL 표시 안함</option>
						<option value="BY">저작자</option>
						<option value="BY-NC">저작자-비영리</option>
						<option value="BY-ND">저작자-변경금지</option>
						<option value="BY-SA">저작자-동일조건변경허락</option>
						<option value="BY-NC-SA">저작자-비영리-동일조건변경허락</option>
						<option value="BY-NC-ND">저작자-비영리-변경금지</option>
					</select>
				</div>





				<h4>태그</h4>
				<input id="tags" name="tags" type="text" class="form-control">




				

				<div>
					<div class="pull-right btn-group">
						<button id="btn-upload-cover-wrapper" type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
						 	커버업로드
						  <span class="caret"></span>
						</button>
						<ul class="dropdown-menu">
						  <li><a id="btn-upload-cover" href="#">커버 업로드</a></li>
						  <li><a id="btn-select-cover" href="#">작품내용 중 선택</a></li>
						</ul>
					</div>	
					<h4>커버</h4>
				</div>

				<div class="row" id="cover-preview">
					<div class="col-md-12" style="display:none;">
						<div>
							<img src="/img/coverupload.png" alt=""/>
						</div>
					</div>

					<div class="col-md-4">
						<img class="preview" src="/data/covers/<?php echo $row->work_id ?>_t1.jpg?_=<?php echo substr($row->moddate, -2) ?>" alt="" onerror="workUtil.showCoverTip();">
					</div>
					<div class="col-md-4">
						<img class="preview" src="/data/covers/<?php echo $row->work_id ?>_t2.jpg?_=<?php echo substr($row->moddate, -2) ?>">
					</div>
					<div class="col-md-4">
						<img class="preview" src="/data/covers/<?php echo $row->work_id ?>_t3.jpg?_=<?php echo substr($row->moddate, -2) ?>">
					</div>

				</div>


				<h4 class="pad7">동영상여부</h4>
				<div class="control-group">
                    <label class="notefolio-radio inline<?if($row->is_video=='y'){?> checked<?}?>">
                        <input type="radio" name="is_video" value="y" <?if($row->is_video=='y'){?> checked<?}?>> 예
                    </label>
                    &nbsp; &nbsp; &nbsp;
                    <label class="notefolio-radio inline<?if($row->is_video=='n'){?> checked<?}?>">
                        <input type="radio" name="is_video" value="n" <?if($row->is_video=='n'){?> checked<?}?>> 아니오
                    </label>
				</div>


				
				<h4 class="pad7">공개여부</h4>
				<div class="control-group">
                    <label class="notefolio-radio inline<?php echo $row->status!='disbaled' ? 'checked' : ''?>">
                        <input type="radio" name="status" value="enabled" <?php echo $row->status!='disbaled' ? 'checked' : ''?>> 공개
                    </label>
                    &nbsp; &nbsp; &nbsp;
                    <label class="notefolio-radio inline<?php echo $row->status=='disbaled' ? 'checked' : ''?>">
                        <input type="radio" name="status" value="disabled" <?php echo $row->status=='disbaled' ? 'checked' : ''?>> 비공개
                    </label>
				</div>




				<h4 class="pad7">충실도 <a class="tip" href="/info/faq#discoverbility" target="_blank">자세히보기</a></h4>
				<div id="work-discoverbility"><span style="width:0%;"></span></div>



				<input type="hidden" name="work_id" value="<?php echo $row->work_id ?>"/>
				<input type="hidden" name="cover_upload_id" value=""/>
			</div>
		</div>
	</div>
</section>


<div id="work-sidebar" class="create-form">
	<div class="container">
		<div class="row">
			<div class="col-md-9">
				<!-- empty -->
			</div>

			<!-- 사이드바 시작 -->
			<div class="col-md-3">


				<div id="work-submit-wrapper">
					<button id="work-submit" type="submit" class="btn btn-pointgreen btn-block btn-lg">
						전송
					</button>
				</div>
			</div>
			<!-- 사이드바 끝 -->
		</div>
	</div>
</div>

<?php echo form_close(); ?>


<ul class="list-unstyled" id="work-content-blockadder">
	<li class="block block-text"><i class="spi spi-text">text</i></li>
	<li class="block block-image"><i class="spi spi-work">work</i></li>
	<li class="block block-video"><i class="spi spi-video">video</i></li>
	<li class="block block-line"><i class="spi spi-division">division</i></li>
	<li id="trash-bin"><i class="spi spi-delete">delete</i></li>
</ul>
<script>
	$(function() {
		// form이 전송이 되면 hook하여 ajax로 호출을 한다.
		$('#gallery-form').on('submit', function(e){
			e.preventDefault();
			e.stopPropagation();
			workUtil.save($(this));
		});
		setTimeout(function(){
			$('#gallery-form').fadeTo(500, 1);
		}, 500);

		//Content Ground Setting 살림.
		workUtil.content.setGround('#content-block-list', '.trashable');
		workUtil.content.setTool('.block-text, .block-image, .block-video, .block-line', '#work-content-blockadder', '#content-block-list');
		workUtil.content.setTrashBin('#trash-bin');
		workUtil.content.restoreContents();

	});
	if($('#style_tagsinput').length==0)
		$('head').append('<link id="style_tagsinput" href="/css/bootstrap-tagsinput.css" rel="stylesheet"/>');
	if($('#style_cleditor').length==0)
		$('head').append('<link id="style_cleditor" rel="stylesheet" type="text/css" href="/js/libs/cleditor/jquery.cleditor.css"/>');
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



<script src="/js/libs/jquery.Jcrop.min.js"></script>
<script src="/js/libs/bootstrap-tagsinput.min.js"></script>
<script src="/js/libs/cleditor/jquery.cleditor.js"></script>
