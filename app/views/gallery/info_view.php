<?php if (!$this->input->is_ajax_request() OR $this->input->post('no_ajax')=='y'): ?>
<script>
	NFview = {
		username : '<?php echo $row->user->username ?>',
		area : 'work-info',
		infiniteCallback : function(){
			var $work = $('#work-list').children('.infinite-item').last();
			commentUtil.open($work); // 코멘트 열기

			// 사이드바 불러오기
			workInfoUtil.getRecentList($work.data('id'));
		}
	};
</script>
<div id="work-sidebar" class="hidden-xs hidden-sm sticky">
	<div class="container" style="height:100%;">
		<div class="row" style="height:100%;">
			<div class="col-md-9">
				<!-- empty -->
			</div>
			<div class="col-md-3" style="height:100%;">
				<?php
				$filename = '/data/profiles/'.$row->user->username.'_bg.jpg';
				if(!file_exists($this->input->server('DOCUMENT_ROOT').$filename)){
					$filename = '/img/bg4.png';
				}
				?>
				<div id="work-profile-image-wrapper" style="background-image:url(<?=$filename?>?_=<?php echo substr($row->user->modified,-2) ?>);">
					<div id="work-profile-image" style="background-color:<?php echo $row->user->face_color ?>">
						<a id="profile-image" href="<?php echo site_url($row->user->username) ?>">
							<img src="/data/profiles/<?php echo $row->user->username ?>_face.jpg?_=<?php echo substr($row->user->modified,-2) ?>" alt="" onerror="this.src='/img/default_profile_face.png'"/>
						</a>
						<div id="profile-info">
							<h2><a href="<?php echo site_url($row->user->username) ?>"><?php echo $row->user->realname ?></a></h2>
							<h4>&nbsp;<?php echo $this->nf->category_to_string($row->user->user_keywords, true); ?>&nbsp;</h4>
							<div id="profile-sns-link">
								<?php echo $this->nf->sns_to_string($row->user->sns); ?>
							</div>

							<?php if (USER_ID!=$row->user_id): ?>
							<div class="centered">
								<a href="javascript:;" data-id="<?php echo $row->user_id ?>" class="btn btn-follow btn-nofol2 btn-hover <?php echo $row->is_follow=='y'?'activated' : '' ?>" style="border:none;">
									<i class="spi spi-following_white"></i>
									<i class="spi spi-follow_point"></i>
									<i class="spi spi-follow_white"></i>
									<span>Follow<?php echo $row->is_follow=='y'?'ing' : '' ?></span>
								</a>
							</div>
							<?php endif ?>
						</div>
					</div>
				</div>
				<div>&nbsp;</div>
				<div id="work-recent-works">
					<h2 class="nofol-title2">Recent Works</h2>
					<ul id="work-recent-list">
					</ul>
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

				<div id="work-list" class="work-list infinite-list">
					<div class="work-wrapper infinite-item" id="work-<?php echo $row->work_id ?>" data-id="<?php echo $row->work_id ?>" data-noted="<?php echo $row->noted; ?>" data-collected="<?php echo $row->collected; ?>" data-moddate="<?php echo substr($row->regdate, -2); ?>">
						<div class="work-small-profile bg1 visible-xs visible-sm">
							<a href="<?php echo site_url($row->user->username) ?>">
								<img src="/data/profiles/<?php echo $row->user->username ?>_face.jpg?_=<?php echo substr($row->user->modified,-2) ?>" onerror="this.src='/img/default_profile_face.png'"/>
							</h2>
							<h2><a href="<?php echo site_url($row->user->username) ?>"><?php echo $row->user->realname ?></a></h2>
							<span><?php echo $this->nf->category_to_string($row->user->user_keywords, true); ?></span>
							<div class="work-url" style="display:none;"><?php echo site_url($row->user->username.'/'.$row->work_id); ?></div>
						</div>
						<div class="work-info">
							<div class="row">
								<div class="col-md-7">
									<div class="work-info-title">

										<!-- <div class="pull-right">
											<?php if (USER_ID==$row->user_id): ?>
											<a href="/<?php echo $row->user->username ?>/<?php echo $row->work_id ?>/update" class="btn btn-nofol">
												<i class="glyphicon glyphicon-cog"></i>
											</a>
											<a href="/<?php echo $row->user->username ?>/<?php echo $row->work_id ?>/delete" class="btn btn-delete-work btn-nofol">
												<i class="spi spi-close">delete</i>
											</a>
											<?php endif ?>
										</div> -->

										<!-- 제목 -->
										<h2 class="work-title"><?php echo $row->title; ?>
											<?php if (USER_ID==$row->user_id): ?>
											<a href="/<?php echo $row->user->username ?>/<?php echo $row->work_id ?>/update" class="work-btn btn-update-work">
												edit
											</a>
											<a href="/<?php echo $row->user->username ?>/<?php echo $row->work_id ?>/delete" class="work-btn btn-delete-work">
												delete
											</a>
											<?php endif ?>
											
										</h2>
										<div class="work-info-time">
											<?php echo $this->nf->print_time($row->regdate) ?>
											/
											<?php echo $this->nf->category_to_string($row->keywords, true); ?>
										</div>
										
									</div>
								</div>
								<div class="col-md-5">
									<div class="work-info-icons">
										<div class="view">
											<i class="spi spi-view">View</i>
											<br/>
											<?php echo $row->hit_cnt ?>
										</div>
										<div class="love">
											<i class="spi spi-love">Love</i>
											<br/>
											<?php echo $row->note_cnt ?>
										</div>
										<div class="comment">
											<i class="spi spi-comment">comment</i>
											<br/>
											<?php echo $row->comment_cnt ?>
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
								<div class="col-xs-12 centered">
									<a href="javascript:;" class="btn btn-nofol2 btn-note <?php echo $row->noted=='y' ? 'noted' : '' ?>">
										<i class="spi spi-love_point">love</i>
										<i class="spi spi-love_white">love</i>
										<span><?php echo $row->note_cnt ?></span> like it !
									</a>
									<div class="add-collection centered <?php echo $row->collected=='y' ? 'collected' : '' ?>">
										<div class="collect-question">
											이 작품을 콜렉션하시겠습니까?
											<a href="javascript:;" onclick="collectUtil.add(this);">예</a> / <a href="javascript:;" onclick="collectUtil.hide(this);">아니오</a>
										</div>
										<div class="collect-collected">
											콜렉션에 추가되었습니다.
											<a href="javascript:;" onclick="collectUtil.cancel(this);">취소</a>
											
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									<div class="work-tags">
										<?php if (!empty($row->tags)): ?>
										<i class="spi spi-tag" style="margin-top: -3px;"></i>
										<?php endif ?>
										<?php 
										foreach($row->tags as $key=>$val){
											echo ($key>0)?', ':'';
											echo "<a class=\"tag\" href=\"/gallery/listing?from=all&q=$val&order=newest\">$val</a>";
										}
										?>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-6" style="padding-left: 50px;">
									<i data-toggle="tooltip" data-placement="bottom" title="<?php
									switch($row->ccl){
										case 'BY':
											echo '저작자표시';
											break;
										case 'BY-NC':
											echo '저작자표시-비영리';
											break;
										case 'BY-ND':
											echo '저작자표시-변경금지';
											break;
										case 'BY-SA':
											echo '저작자표시-동일조건변경허락';
											break;
										case 'BY-NC-SA':
											echo '저작자표시-비영리-동일조건변경허락';
											break;
										case 'BY-NC-ND':
											echo '저작자표시-비영리-변경금지';
											break;
										default:
											echo 'CCL 표시 안함';
									}
									?>" class="pi pi-ccl-cc-<?php echo strtolower($row->ccl) ?>">CCL</i>
								</div>
								<div class="col-xs-6 righted work-sns">
									<a href="javascript:;" onclick="snsUtil.twitter(this);">
										<i class="pi pi-twitter">twit_hover</i>
									</a>
									<a href="javascript:;" onclick="snsUtil.facebook(this);">
										<i class="pi pi-facebook">fb_hover</i>
									</a>
									<a href="javascript:;" onclick="snsUtil.pinterest(this);">
										<i class="pi pi-pinterest">pin_hover</i>
									</a>
									<a href="javascript:;" onclick="snsUtil.tumblr(this);">
										<i class="pi pi-tumblr">tumblr_hover</i>
									</a>
									<!-- <a href="javascript:;" onclick="snsUtil.kakaotalk(this);" class="pi pi-fb_hover visible-xs visible-sm">kakaotalk</a> -->
								</div>
							</div>
						</div>

						<div class="work-actions">
							<div class="row">
								<div class="comment-wrapper" data-id="<?php echo $row->work_id ?>">
									<div class="col-xs-12">
										<a href="javascript:;" class="btn-comment-prev btn btn-link btn-block"><i class="spi spi-up">up</i> 이전 댓글보기</a>
										<!-- comment-block will be displayed here -->
										<?php echo $this->load->view('comment/comment_form_view'); ?>
									</div>
								</div>					
								<div class="note-wrapper" data-id="<?php echo $row->work_id ?>">>
									<div class="col-xs-12">
										<div class="collect-question">
											이 작품을 콜렉션에 담겠습니까?
											<a href="#">예</a>
											/
											<a href="#">아니오</a>
										</div>
										<div class="collect-collected">
											콜렉션에 담았습니다.
											<a href="">취소</a>
										</div>
									</div>
								</div>
							</div>
						</div>

					</div>
				</div>

				<?php if ($row->prev_work_id!=0): ?>
					<a href="/<?php echo $row->user->username; ?>/<?php echo $row->prev_work_id; ?>" class="more-link btn btn-default btn-block btn-more">more</a>
				<?php endif ?>
				


<?php if (!$this->input->is_ajax_request() OR $this->input->post('no_ajax')=='y'): ?>
			</div>
			<div class="col-md-3">
				<!-- empty -->
			</div>
		</div>
	</div>
</section>
<?php if ($this->tank_auth->is_logged_in()): ?>
	<script src="/js/member.js"></script>
	<script>
		$(function(){
			$('#work-info-wrapper').on('click', '.btn-delete-work', function(e){
				workUtil.delete(this);
				return false;
			});
		});
	</script>
<?php endif ?>
<script src="/js/libs/jquery.scrollTo.min.js"></script>
<script>
	$(function() {
		NFview.infiniteCallback();
		workInfoUtil.setGround();		
	});
	$(window).on('load', function(){
		workInfoUtil.initRecentList();
	});
</script>
<?php endif ?>

