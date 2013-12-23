<?php if (!$this->input->is_ajax_request() OR $this->input->post('no_ajax')=='y'): ?>
<script>
	NFview = {
		area : 'work-info'
	};
</script>
<div id="work-sidebar" class="hidden-xs hidden-sm">
	<div class="container">
		<div class="row">
			<div class="col-md-9">
				<!-- empty -->
			</div>
			<div class="col-md-3">
				<div id="work-profile-image">
					<div id="profile-image">
						<img src="/data/profiles/<?php echo $user->username ?>.jpg?_=<?php echo substr($user->modified,-2) ?>" alt=""/>
					</div>
					<div id="profile-info">
						<h2><?php echo $user->username ?></h2>
						<h4>&nbsp;<?php echo @implode('·', $user->keywords); ?>&nbsp;</h4>
						<a href="" class="btn btn-nofol btn-follow">
							<i class="spi spi-follow"></i>
							Follow
						</a>
					</div>
					<div id="profile-sns-link">
						<?php print_r($user) ?>
					</div>
				</div>
				<div>&nbsp;</div>
				<div id="work-recent-works">
					<h2 class="nofol-title">Recent Works</h2>
				</div>
			</div>
		</div>
	</div>	
</div>
<section>
	<div class="container">
		<div class="row">
			<div class="col-md-9">
<?php endif ?>
				<div class="work-small-profile visible-xs visible-sm">
					<i class="spi spi-follow"></i>
					<img src="/data/profiles/<?php echo $user->username ?>.jpg"/>
					<h2><?php echo $user->username ?></h2>
					<span><?php echo @implode(', ', $user->keywords); ?></span>
				</div>

				<div class="work-list infinite-list">
					<div class="work-wrapper infinite-item">
						<div class="work-info">
							<div class="row">
								<div class="col-md-7">
									<div class="work-info-title">
										<div class="btn-group pull-right">
											<?php if (USER_ID==$user_id): ?>
											<a href="/<?php echo $user->username ?>/<?php echo $work_id ?>/update" class="btn btn-default">
												<i class="glyphicon glyphicon-cog"></i>
											</a>
											<a id="btnDelete" href="/<?php echo $user->username ?>/<?php echo $work_id ?>/delete" class="btn btn-default">
												<i class="glyphicon glyphicon-trash"></i>
											</a>
											<?php endif ?>
										</div>

										<!-- 제목 -->
										<h2><?php echo $title; ?></h2>
										<div class="work-info-time">
											<?php echo $this->nf->print_time($regdate) ?>
											/
											<?php echo @implode(', ', $keywords); ?>
										</div>
									</div>
								</div>
								<div class="col-md-5">
									<div class="work-info-icons">
										<div class="view bg1">
											<i class="spi spi-view2"></i>
											<br/>
											<?php echo $hit_cnt ?>
										</div>
										<div class="comment bg2">
											<i class="spi spi-comment"></i>
											<br/>
											<?php echo $comment_cnt ?>
										</div>
										<div class="love bg3">
											<i class="spi spi-love2"></i>
											<br/>
											<?php echo $note_cnt ?>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="work-contents" style="height: 500px;">
							작품내용
						</div>

						<div class="work-addinfo">
							<div class="row">
								<div class="col-xs-6">
									<div class="work-tags">
										<i class="spi spi-tag"></i> tags
									</div>
								</div>
								<div class="col-xs-6">
									<div class="work-ccl righted">
										ccl
									</div>
								</div>
							</div>
						</div>

						<div class="work-actions">
							<div class="row">
								<div class="col-md-6 col-xs-9">
									<a href="" class="btn btn-nofol bg2">
										<i class="spi spi-comment"></i>
										코멘트 열기(13)
									</a>
									<a href="" class="btn btn-nofol bg1">
										<i class="spi spi-love2"></i>
										좋아요
									</a>
								</div>
								<div class="col-md-6 col-xs-3 righted">
									share
								</div>
							</div>
						</div>

					</div>
				</div>

				<a href="/gallery/<?php echo ($this->uri->segment(2))?$this->uri->segment(2)+1:2; ?>" class="more-link">more</a>
				


<?php if (!$this->input->is_ajax_request() OR $this->input->post('no_ajax')=='y'): ?>
			</div>
			<div class="col-md-3">
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

