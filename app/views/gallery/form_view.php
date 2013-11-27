<script>
	NFview = {};
</script>

<section class="visible-md visible-lg">
	<?php echo form_open('/gallery/save', array('id'=>'gallery_form', 'class'=>'container')); ?>
		<div class="row">
			<!-- 작품영역 시작 -->
			<div class="col-md-8">
				<div class="well" style="height: 60px;">
					작품제목
				</div>
				<div class="well" style="height:600px;">
					작품 블럭영역
				</div>
				<div class="well" style="height:100px;">
					작품 블럭영역
				</div>
				<div class="well" style="height:500px;">
					작품 블럭영역
				</div>
				<div class="well" style="height:600px;">
					작품 블럭영역
				</div>
				<div class="well" style="height:60px;">
					작품 블럭영역
				</div>
				<div class="well" style="height:400px;">
					작품 블럭영역
				</div>
				<div class="well" style="height:200px;">
					작품 블럭영역
				</div>
				<?php echo form_open(''); ?>
				<?php echo form_close(); ?>
			</div>
			<!-- 작품영역 끝 -->


			<!-- 사이드바 시작 -->
			<div class="col-md-4 ">
				<div class="sticky">
				<div class="well" style="height:100px;">
					키워드(최대 3개)
				</div>
				<div class="well" style="height:50px;">
					태그
				</div>
				<div class="well" style="height:100px;">
					CCL
				</div>
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

