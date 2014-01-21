<?php if (!$this->input->is_ajax_request()): ?>
<section id="search_form">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<form action="" class="well form-inline" role="form">
					<div class="input-group col-xs-2">
	  					<span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
						<select class="" name="sort_option" id="sort_option">
							<option value="total">전체 기간</option>
							<option value="daily">오늘</option>
							<option value="1week">이번 주</option>
							<option value="monthly">이번 달</option>
						</select>
					</div>
					<select class="col-xs-6" name="work_categories" id="work_categories" multiple title="Choose one of the following...">
						<?php 
						$this->load->config('keyword', TRUE);
						$keyword_list = $this->config->item('thumbnail_'.$type, 'keyword');

						foreach ($keyword_list as $keyword)
						?>
						<option value="<?php echo $keyword['key'];?>"><?php echo $keyword['val'];?></option>
						<?php ?>
					</select>
					<select class="col-xs-2" name="" id="">
						<option value="newest">최신순</option>
						<option value="noted">인기순</option>
						<option value="viewed">조회순</option>
						<option value="featured">추천순</option>
					</select>
					<button type="submit" class="btn btn-primary">조회</button>
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

				<a href="/gallery/listing/<?php echo ($this->uri->segment(3))?$this->uri->segment(3)+1:2; ?>" class="more-link">more</a>
<?php if (!$this->input->is_ajax_request()): ?>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>