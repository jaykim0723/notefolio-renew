<?php if (!$this->input->is_ajax_request()): ?>
<section id="search_form">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<form action="" class="well" style="height:200px;">
					조건입력창
				</form>
			</div>
		</div>
	</div>
</section>



<section class="listing">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
<?php endif ?>
				<div class="thumbnail_list">
					<!-- list -->
					<?php foreach ($rows as $key => $row): ?>
					<?php $this->load->view('gallery/thumbnail_inc_view', $row) ?>
					<?php endforeach ?>
				</div>

				<a href="/gallery/listing/2" class="more-link">more</a>
<?php if (!$this->input->is_ajax_request()): ?>
			</div>
		</div>
	</div>
</section>
<script>


	function callPage(){
		var url = window.location.href;
		var match = new RegExp("/listing/([0-9]+)").exec(url);
		if(match==null){
			url = url.replace("/listing", '/listing/2');
		}
		else {
			url = url.replace(match[0], '/listing/'+(Number(match[1])+1));
		}

		$.get(url).done(function(data){
			$('.thumbbox', data).each(function(){
				$(this).appendTo('.thumbnail_list');
			});
			if($('.more-link', data).length>0){
				$('.more-link').replaceWith($('.more-link', data));
			}
			else {
				noNextPage = null;
				$('.more-link').remove();
			}
		});
	}
	$(function() {
		$('.thumbnail_list').waypoint(function(){
			if(typeof noNextPage=='undefined') callPage();
		});
	});
</script>
<?php endif; ?>