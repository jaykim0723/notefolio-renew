<?php if (!$this->input->is_ajax_request()): ?>
<script>
	NFview = {};
</script>
<section class="listing">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<ul class="main-thumbnail-list">
					<li class="thumbbox infinite-item wide">
						<a class="go_to_work_info" href="/maxzidell/1004">
							<img src="/img/thumb_wide.gif"/>
						</a>
					</li>
					<li class="thumbbox infinite-item">
						<a class="go_to_work_info" href="/maxzidell/1004">
							<img src="/img/thumb.gif"/>
						</a>
					</li>
				</ul>
<?php endif ?>
				<ul class="main-thumbnail-list infinite-list">
					<!-- list -->
					<?php foreach ($rows as $key => $row):
					$row->key = $key;
					?>
					<?php $this->load->view('main/thumbnail_inc_view', $row) ?>
					<?php endforeach ?>
				</ul>

				<a href="/main/listing/<?php echo ($page)?$page+1:2; ?>" class="more-link">more</a>
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
		$(document).on('click', '.go_to_work_info', function(){
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