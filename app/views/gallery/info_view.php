<?php if (!$this->input->is_ajax_request() OR $this->input->post('no_ajax')=='y'): ?>
<script>
	NFview = {
		area : 'work-info'
	};
</script>
<div id="work-sidebar" class="hidden-xs hidden-sm">
	<div class="container">
		<div class="row">
			<div class="col-md-8">
				<!-- empty -->
			</div>
			<div class="col-md-4">
				<div>
					<div class="well" style="height:200px;">
						프로필
					</div>
					<div class="well visible-md visible-lg">
						최신작품
					</div>
				</div>
			</div>
		</div>
	</div>	
</div>
<section>
	<div class="container">
		<div class="row">
			<div class="col-md-8">
<?php endif ?>
				<div class="work-list infinite-list">
					<div class="work-wrapper infinite-item">
						<div class="work_info well" style="height: 100px;">
							<div class="btn-group pull-right">
								<?php if (USER_ID): ?>
									
								<?php endif ?>
								<a href="/<?php echo $user->username ?>/<?php echo $work_id ?>/update" class="btn btn-default">
									<i class="glyphicon glyphicon-cog"></i>
								</a>
								<a id="btnDelete" href="/<?php echo $user->username ?>/<?php echo $work_id ?>/delete" class="btn btn-default">
									<i class="glyphicon glyphicon-trash"></i>
								</a>

							</div>
							작품정보
						</div>

						<div class="work_contents well" style="height: 1200px;">
							작품내용
						</div>


						<div class="row">
							<div class="col-xs-4">
								<div class="well" style="height:50px;">
									<a href="" class="btn btn-default">코멘트 열기(13)</a>
								</div>
							</div>
							<div class="col-xs-4">
								<div class="well" style="height:50px;">
									<a href="" class="btn btn-default">좋아요</a>
								</div>
							</div>
							<div class="col-xs-4">
								<div class="well" style="height:50px;">
									SNS 공유하기
								</div>
							</div>
						</div>

						<div class="well visible-xs visible-sm" style="height:100px;">
							프로필
						</div>

					</div>
				</div>

				<a href="/gallery/<?php echo ($this->uri->segment(2))?$this->uri->segment(2)+1:2; ?>" class="more-link">more</a>
				


<?php if (!$this->input->is_ajax_request() OR $this->input->post('no_ajax')=='y'): ?>
			</div>
			<div class="col-md-4">
				<!-- empty -->
			</div>
		</div>
	</div>
</section>
<script>
	$(function() {
		$('#btnDelete').on('click', function(e){
			var url = $(this).attr('href');
			BootstrapDialog.confirm('Hi Apple, are you sure?', function(result){
				if(result){
					site.redirect(url);
				}
			}, 'danger');
			return false;
		});
	});
</script>
<?php endif ?>

