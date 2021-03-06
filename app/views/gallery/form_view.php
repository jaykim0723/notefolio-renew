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
				<h4 class="hide-h2">제목</h4>
				<input id="title" name="title" type="text" class="form-control input-lg col-md-12" style="border-color: #fff;box-shadow: none !important;"placeholder="Title"/>
				<br>
				<h4 class="hide-h2">내용</h4>
				<ul style="margin-top:0;" id="content-block-list" class="work-info list-unstyled work-contents work-editing">
				</ul>
				<div id="content-multiple">
					<img src="/img/uploadcont2.png" alt=""/>
				</div>
			</div>
			<!-- 작품영역 끝 -->

			<div id="work-sidebar-inner" class="col-md-3">

				<h4 class="hide-h2">카테고리</h4>
				<select name="keywords" id="keywords" multiple title="카테고리 선택 (최대 2개)">
					<?php 
					$this->load->config('keyword', TRUE);
					$keyword_list = $this->config->item('keyword', 'keyword');

					foreach ($keyword_list as $key => $keyword) { ?>
						<option value="<?php echo $key?>"<?=(in_array($key, $keywords_list_key))?' selected':''?>><?php echo $keyword;?></option>
					<?php }	?>
				</select>

				<div id="ccl-wrapper">
					<div style="position:absolute;top: 60px;right: -15px;"><a class="tip" href="/info/faq#ccl" target="_blank"><i class="spi spi-q"></i></a></div>
					<h4 class="hide-h2">CCL <a class="tip" href="/info/faq#ccl" target="_blank">자세히보기</a></h4>
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





				<h4 class="hide-h2">태그</h4>
				<input id="tags" name="tags" type="text" class="form-control" placeholder="태그입력(Tab, Enter로 구분)">




				

				<div class="row" style="margin-bottom: -10px;background: #fff;width: 263px;margin-left: 0px;">
					<div class="pull-right btn-group" style="">
						<button id="btn-upload-cover-wrapper" type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" style="height: 36px;padding: 1px 18px 1px 171px;border-color: #fff;">
						 	커버업로드
						  <span class="caret"></span>
						</button>
						<ul class="dropdown-menu">
						  <li><a id="btn-upload-cover" href="#">커버 업로드</a></li>
						  <li><a id="btn-select-cover" href="#">작품내용 중 선택</a></li>
						</ul>
					</div>	
					<h4 style="padding-top: 10px;color: #fff;height: 26px;">커버</h4>
				</div>

				<div class="row" id="cover-preview" style="margin-bottom: 0px;background: #fff;width: 263px;margin-left: 0px;padding-bottom: 20px;">
					<div class="col-md-12" style="display:none;">
						<div>
							커버업로드 버튼을 클릭해주세요
							<!-- <img src="/img/coverupload.png" alt=""/> -->
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


				<h4 class="pad7" style="background: #fff;margin-bottom: 0;padding-top: 20px;padding-left: 15px;padding-bottom: 20px;">영상콘텐츠를 포함하고 있습니다.</h4>
				<div class="control-group" style="background: #fff;margin-top: -38px;padding-right: 15px;float: right;">
                    <label class="notefolio-radio inline<?if($row->is_video=='y'){?> checked<?}?>">
                        <input type="checkbox" name="is_video" value="y" <?if($row->is_video=='y'){?> checked<?}?>>
                    </label>
				</div>


				
				<h4 class="pad7" style="background: #fff;margin-top: 0;border-top: 1px solid #efefef;padding-top: 18px;padding-bottom: 17px;padding-left: 15px;">공개여부</h4>
				<div class="control-group" style="float: right;margin-top: -45px;padding-right: 15px;">
                    <label class="notefolio-radio inline<?php echo $row->status!='disabled' ? 'checked' : ''?>">
                        <input type="radio" name="status" value="enabled" <?php echo $row->status!='disabled' ? 'checked' : ''?>> 공개
                    </label>
                    &nbsp; &nbsp; &nbsp;
                    <label class="notefolio-radio inline<?php echo $row->status=='disabled' ? 'checked' : ''?>">
                        <input type="radio" name="status" value="disabled" <?php echo $row->status=='disabled' ? 'checked' : ''?>> 비공개
                    </label>
				</div>
				


				<h4 class="pad7">Likability <a class="tip" href="/info/faq#discoverbility" target="_blank">자세히보기</a></h4>
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
						업로드
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
					모바일에서는 작품 업로드 및 수정이 불가능합니다.
				</div>
			</div>
		</div>
	</div>
</section>



<script src="/js/libs/jquery.Jcrop.min.js"></script>
<script src="/js/libs/bootstrap-tagsinput.min.js"></script>
<script src="/js/libs/cleditor/jquery.cleditor.js"></script>
<script src="/js/libs/jquery.notebook.js"></script>
