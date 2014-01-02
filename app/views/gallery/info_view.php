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
					<a id="profile-image" href="<?php echo site_url($row->user->username) ?>">
						<img src="/data/profiles/<?php echo $row->user->username ?>.jpg?_=<?php echo substr($row->user->modified,-2) ?>" alt=""/>
					</a>
					<div id="profile-info">
						<h2><?php echo $row->user->username ?></h2>
						<h4>&nbsp;<?php echo @implode('·', $row->user->keywords); ?>&nbsp;</h4>
						<a href="" class="btn btn-nofol btn-follow">
							<i class="spi spi-follow"></i>
							Follow
						</a>
					</div>
					<ul id="profile-sns-link">
						<?php foreach ($row->user->sns as $service => $id):
						$tmp = $this->nf->sns($service, $id);
						?>
						<li>
							<a href="<?php echo $tmp->link  ?>" class="<?php echo $service ?>" class="btn-hover">
								<i class="spi spi-fb"></i>
								<?php echo $tmp->label ?>
							</a>
						</li>
						<?php endforeach ?>
					</ul>
				</div>
				<div>&nbsp;</div>
				<div id="work-recent-works">
					<h2 class="nofol-title">Recent Works</h2>
				</div>
			</div>
		</div>
	</div>	
</div>
<section id="work-info-wrapper">
	<div class="container">
		<div class="row">
			<div class="col-md-9">
<?php endif ?>

				<div class="work-list infinite-list">
					<div class="work-wrapper infinite-item" id="work-<?php echo $row->work_id ?>" data-id="<?php echo $row->work_id ?>">
						<div class="work-small-profile visible-xs visible-sm">
							<i class="spi spi-follow"></i>
							<img src="/data/profiles/<?php echo $row->user->username ?>.jpg"/>
							<h2><?php echo $row->user->username ?></h2>
							<span><?php echo @implode(', ', $row->user->keywords); ?></span>
						</div>
						<div class="work-info">
							<div class="row">
								<div class="col-md-7">
									<div class="work-info-title">
										<div class="btn-group pull-right">
											<?php if (USER_ID==$row->user_id): ?>
											<a href="/<?php echo $row->user->username ?>/<?php echo $row->work_id ?>/update" class="btn btn-default">
												<i class="glyphicon glyphicon-cog"></i>
											</a>
											<a href="/<?php echo $row->user->username ?>/<?php echo $row->work_id ?>/delete" class="btn btn-delete-work btn-default">
												<i class="glyphicon glyphicon-trash"></i>
											</a>
											<?php endif ?>
										</div>

										<!-- 제목 -->
										<h2><?php echo $row->title; ?></h2>
										<div class="work-info-time">
											<?php echo $this->nf->print_time($row->regdate) ?>
											/
											<?php echo @implode(', ', $row->keywords); ?>
										</div>
									</div>
								</div>
								<div class="col-md-5">
									<div class="work-info-icons">
										<div class="view bg1">
											<i class="spi spi-view2">View</i>
											<br/>
											<?php echo $row->hit_cnt ?>
										</div>
										<div class="comment bg2">
											<i class="spi spi-comment">Comment</i>
											<br/>
											<?php echo $row->comment_cnt ?>
										</div>
										<div class="love bg3">
											<i class="spi spi-love2">Love</i>
											<br/>
											<?php echo $row->note_cnt ?>
										</div>
									</div>
								</div>
							</div>
						</div>

						<ul class="work-contents">
							<?php foreach ($row->contents as $index => $block): ?>
								<?php echo $this->load->view('gallery/info_block_view', $block); ?>
							<?php endforeach ?>
						</ul>

						<div class="work-addinfo">
							<div class="row">
								<div class="col-xs-6">
									<div class="work-tags">
										<i class="spi spi-tag"></i> <?php echo @implode(', ', $tags) ?>
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
									<a href="javascript:;" class="btn btn-nofol btn-open-comment bg2">
										<i class="spi spi-comment"></i>
										코멘트 열기(13)
									</a>
									<a href="javascript:;" class="btn btn-nofol bg1">
										<i class="spi spi-love2"></i>
										좋아요
									</a>
								</div>
								<div class="col-md-6 col-xs-3 righted">
									share
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									<div class="comment-wrapper" data-id="<?php echo $row->work_id ?>">
										<a href="javascript:;" class="comment-prev btn btn-link btn-block">▲ 이전 댓글보기</a>
										<!-- comment-block will be displayed here -->
										<?php echo $this->load->view('comment/comment_form_view'); ?>
									</div>									
									<div class="love-wrapper">
										love에 대한 후속조치
									</div>
								</div>
							</div>
						</div>

					</div>
				</div>

				<a href="/<?php echo ($this->uri->segment(1))?>/<?php echo ($this->uri->segment(2))?$this->uri->segment(2)-1:0; ?>" class="more-link">more</a>
				


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
		$('#work-info-wrapper').on('click', '.btn-delete-work', function(e){
			var url = $(this).attr('href');
			BootstrapDialog.confirm('Hi Apple, are you sure?', function(result){
				if(result){
					site.redirect(url);
				}
			}, 'danger');
			return false;
		}).on('click', '.btn-open-comment', function(){
			commentUtil.open(this);
		}).on('submit', '.comment-block', function(){
			commentUtil.submitComment(this);
		}).on('submit', '.btn-comment-delete', function(){
			commentUtil.delet(this);
		});
	});
</script>
<?php endif ?>

