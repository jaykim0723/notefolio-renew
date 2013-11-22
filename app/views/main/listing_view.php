<?php if (!$this->input->is_ajax_request()): ?>


<section class="listing">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="well" style="height:400px;background: #eee;">
					복합구성
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-9 col-sm-12 col-xs-12">
<?php endif ?>
				<div class="thumbnail_list">
					<!-- list -->
					<?php foreach ($rows as $key => $row): ?>
					<?php $this->load->view('gallery/thumbnail_inc_view', $row) ?>
					<?php endforeach ?>
				</div>

				<a href="/gallery/listing/<?php echo ($this->uri->segment(3))?$this->uri->segment(3)+1:2; ?>" class="more-link">more</a>
<?php if (!$this->input->is_ajax_request()): ?>
			</div>
			<div class="col-md-3 hidden-sm hidden-xs">
				<div class="well" style="background: #eee;height:500px;">
					최근작품
				</div>
				<div class="well" style="background: #eee;height:500px;">
					HOT Creator
				</div>
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
		/*$('.more-link').waypoint(function(){
			if(typeof noNextPage=='undefined') callPage();
		});*/
		$('.thumbnail_list').waypoint('infinite', {
			container: '.thumbnail_list',
			items: '.thumbbox',
  			more: '.more-link',
    		offset: 'bottom-in-view',
			onAfterPageLoad : function(){
				console.log($.now());
			}
		});
	});
</script>
<?php endif; ?>