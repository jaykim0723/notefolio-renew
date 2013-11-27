<?php if (!$this->input->is_ajax_request()): ?>
<script>
	NFview = {};
</script>
<section class="listing">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
<?php endif ?>
				<ul class="main_thumbnail_list infinite_list">
					<!-- list -->
					<?php foreach ($rows as $key => $row):
					$row->key = $key;
					?>
					<?php $this->load->view('main/thumbnail_inc_view', $row) ?>
					<?php endforeach ?>
				</ul>

				<a href="/main/listing/<?php echo ($this->uri->segment(3))?$this->uri->segment(3)+1:2; ?>" class="more-link">more</a>
<?php if (!$this->input->is_ajax_request()): ?>
			</div>
		</div>
	</div>
</section>
<script>

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
			// 기종 정보 백업
			back_url = location.href;
			back_top = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop;

			// 이동을 위한 사전 작업
			var url = $(this).attr('href');
			var p = $(this).parents('.listing').hide();
			p.after('<div id="ajax_work_wrapper"></div>');

			$('#ajax_work_wrapper').load(url, {'no_ajax':'y'});
			history.pushState(null, null, url);

			return false;
		})
	})
</script>
<?php endif; ?>