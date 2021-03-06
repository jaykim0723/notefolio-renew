<?php if (!$this->input->is_ajax_request()): ?>
<section id="search_form">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<form action="" class="form-inline" role="form" id="gallery-search-form">

					<div class="row">


						<div class="col-md-2 col-sm-6 gal-cate" style="border-bottom: 1px solid #efefef;">
							<select name="work_categories[]" id="work_categories" multiple title="카테고리 선택" onchange="$('#gallery-search-form').submit()">
								<?php 
								$this->load->config('keyword', TRUE);
								$keyword_list = $this->config->item('keyword', 'keyword');

								foreach ($keyword_list as $key => $keyword) { ?>
									<option value="<?php echo $key?>"<?=(in_array($key, $work_categories))?' selected':''?>><?php echo $keyword;?></option>
								<?php }	?>
							</select>
						</div>

						<div class="col-md-2 col-sm-6" style="border-bottom: 1px solid #efefef;">
							<select name="order" id="order" onchange="$('#gallery-search-form').submit()">
								<option value="newest"<?=($order=="newest")?' selected':''?>>최신순</option>
								<option value="noted"<?=($order=="noted")?' selected':''?>>인기순</option>
								<option value="viewed"<?=($order=="viewed")?' selected':''?>>조회순</option>
								<option value="comment_desc"<?=($order=="comment_desc")?' selected':''?>>댓글순</option>
							</select>
						</div>
						
						<div class="col-md-2 col-sm-6" style="border-bottom: 1px solid #efefef;">
							<div class="input-group">
			  					<!-- <span class="input-group-addon"></span> -->
								<select class="" name="from" id="from" onchange="$('#gallery-search-form').submit()">
									<option value="all"<?=($from=="all")?' selected':''?>>전체 기간</option>
									<option value="day"<?=($from=="day")?' selected':''?>>오늘</option>
									<option value="week"<?=($from=="week")?' selected':''?>>이번 주</option>
									<option value="month"<?=($from=="month")?' selected':''?>>이번 달</option>
									<option value="month3"<?=($from=="month3")?' selected':''?>>최근 3달</option>
								</select>
							</div>
						</div>

						<div class="col-md-2 col-sm-6"></div>

						<div class="col-md-4 col-sm-6">
							<div class="col-md-10 col-sm-10 search-center pull-left">
								<input class="form-control" type="text" name="q" placeholder="검색어" value="<?=$q?>"/>
							</div>
							<div class="col-md-2 col-sm-2 search-center pull-right">
								<button type="submit" class="btn btn-pointgreen search-green"><i class="spi spi-search_white" style="margin-top: -3px;margin-bottom: 3px;">search_white</i></button>
							</div>
						</div>
					</div>

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
				<ul id="gallery-list" class="thumbnail-list infinite-list">
					<?php if (empty($rows)): ?>
						<div class="centered" style="padding: 100px 0;">어이쿠! 검색 결과가 없습니다.</div>
					<?php else: ?>
						<!-- list -->
						<?php foreach ($rows as $key => $row): ?>
						<?php $this->load->view('gallery/thumbnail_inc_view', array('row'=>$row)) ?>
						<?php endforeach ?>
					<?php endif ?>
				</ul>
<?php
$querystring = (!empty($_SERVER['QUERY_STRING']))?'?'.$_SERVER['QUERY_STRING']:'';
?>
				<a href="/gallery/listing/<?php echo ($page)?$page+1:2; ?><?=$querystring?>" class="more-link btn btn-more btn-default btn-block">more</a>
<?php if (!$this->input->is_ajax_request()): ?>
			</div>
		</div>
	</div>
</section>
<script>
	$('#search_form').slideUp(0);
	$(function(){
		$('#search_form').stop(true, true).slideDown(500);
		$('.more-link').trigger('click'); // more버튼을 무조건 한 번 발생시켜준다.
	})
</script>
<?php if ($page==1): ?>
<script>
	site.restoreInifiniteScroll();
</script>
<?php endif ?>




<?php endif; ?>