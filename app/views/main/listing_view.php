<?php if (!$this->input->is_ajax_request()): ?>
<section class="listing">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<?php if ($page==1): ?>
				<ul id="main-list-top" class="main-thumbnail-list">
					<?php $this->load->view('main/thumbnail_inc_view', array('row' => $first)) ?>
					<li class="thumbbox hidden-xs hidden-sm">
						<h2 id="main-hot-creators-title" class="nofol-title">Hot Creators</h2>
						<ul id="main-hot-creators">
							<?php foreach ($creators as $key => $row): ?>
							<li>
								<a href="<?php echo site_url($row->username) ?>">
									<span class="hot-arrow">
										<i class="spi spi-next">next</i>
										<i class="spi spi-next_white">next</i>
									</span>
									<span class="hot-face"> <!-- bg here -->
										<img src="/data/profiles/<?=$row->username?>_face.jpg?h=1385655105" alt=""/>
										<i class="si si-face-medium"></i>
										<i class="si si-face-medium_point"></i>
									</span>
									<span class="hot-center">
										<span class="hot-username">
											<?php echo $row->username; ?>
										</span>
										<span class="hot-keywords">
											<?php echo $this->nf->category_to_string($row->user_keywords); ?>
										</span>
										<span class="hot-go">
											Go To profile
										</span>
									</span>
								</a>
							</li>	
							<?php endforeach ?>
						</ul>
					</li>
				</ul>
				<?php endif ?>

<?php endif ?>

				<ul class="main-thumbnail-list infinite-list">
					<!-- list -->
					<?php foreach ($rows as $key => $row):
					$row->key = $key;
					?>
					<?php $this->load->view('main/thumbnail_inc_view', array('row' => $row)) ?>
					<?php endforeach ?>
				</ul>

				<a href="/main/listing/<?php echo ($page)?$page+1:2; ?>" class="btn btn-default btn-block more-link">more</a>


				
<?php if (!$this->input->is_ajax_request()): ?>

			</div>
		</div>
	</div>
</section>
<script>

	/*
	var back_top = 0;
	var back_url = '';
	var back = function(){
		// 컨텐츠 복원
		$('#ajax_work_wrapper').prev().show().next().remove();

		// 기존정보 복원
		$('html,body').animate({scrollTop: back_top+'px'}, 0);
		history.pushState(null, null, back_url);
	}

	$(function(){
		$(document).on('click', '.go-to-work-info', function(){
			if(lteIE9) return true; // pushStatus지원하지 않으면 그냥 페이지 이동. modernizer 등으로 보다 더 정확히 체크할 것

			// 기종 정보 백업
			back_url = location.href;
			back_top = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop;

			// 이동을 위한 작업
			blockPage.block();			
			var url = $(this).attr('href');
			var p = $(this).parents('.listing').hide();
			p.after('<div id="ajax_work_wrapper"></div>');

			$('#ajax_work_wrapper').load(url, {'no_ajax':'y'});
			history.pushState(null, null, url);
			blockPage.unblock();

			return false;
		})
	})
	*/
</script>
<?php endif; ?>