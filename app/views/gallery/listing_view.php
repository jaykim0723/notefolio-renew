<?php if (!$this->input->is_ajax_request()): ?>
<section id="search_form">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<form action="" class="form-inline" role="form">

					<div class="row">
						
						<div class="col-md-3 col-sm-6">
							<div class="input-group">
			  					<span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
								<select class="" name="from" id="from">
									<option value="all">전체 기간</option>
									<option value="day">오늘</option>
									<option value="week">이번 주</option>
									<option value="month">이번 달</option>
								</select>
							</div>
						</div>


						<div class="col-md-3 col-sm-6">
							<select name="work_categories[]" id="work_categories" multiple title="Choose one of the following...">
								<?php 
								$this->load->config('keyword', TRUE);
								$keyword_list = $this->config->item('keyword', 'keyword');

								foreach ($keyword_list as $key => $keyword) { ?>
									<option value="<?php echo $key?>"<?=(in_array($key, $work_categories))?' selected':''?>><?php echo $keyword;?></option>
								<?php }	?>
							</select>
						</div>

						

						<div class="col-md-3 col-sm-6">
							<input class="col-xs-2 form-control" type="text" name="q" placeholder="검색어" value="<?=$q?>"/>
						</div>



						<div class="col-md-2 col-sm-6">
							<select name="order" id="order">
								<option value="newest"<?=($order=="newest")?' selected':''?>>최신순</option>
								<option value="noted"<?=($order=="noted")?' selected':''?>>인기순</option>
								<option value="viewed"<?=($order=="viewed")?' selected':''?>>조회순</option>
								<option value="featured"<?=($order=="featured")?' selected':''?>>추천순</option>
							</select>
						</div>

						<div class="col-md-1 col-sm-12">
							<button type="submit" class="btn btn-primary">조회</button>
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
				<ul class="thumbnail-list infinite-list">
					<!-- list -->
					<?php foreach ($rows as $key => $row): ?>
					<?php $this->load->view('gallery/thumbnail_inc_view', array('row'=>$row)) ?>
					<?php endforeach ?>
				</ul>

				<a href="/gallery/listing/<?php echo ($this->uri->segment(3))?$this->uri->segment(3)+1:2; ?>" class="more-link btn btn-more btn-default btn-block">more</a>
<?php if (!$this->input->is_ajax_request()): ?>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>