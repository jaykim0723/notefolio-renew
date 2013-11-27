<?php if (!$this->input->is_ajax_request()): ?>
<section id="search_form">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<form action="" class="well form-inline" role="form">
					<select class="col-xs-2" name="sort_option" id="sort_option">
						<option value="total">전체 기간</option>
						<option value="daily">오늘</option>
						<option value="1week">이번 주</option>
						<option value="monthly">이번 달</option>
					</select>
					<select class="col-xs-6" name="work_categories" id="work_categories" multiple title="Choose one of the following...">
						<option value="A7">가구디자인</option>
						<option value="B7">그리픽디자인</option>
						<option value="C7">디지털아</option>
						<option value="D7">산업디자인</option>
						<option value="E7">실내디자인</option>
						<option value="F7">웹디자인</option>
						<option value="G7">제품디자인</option>
						<option value="H7">페인팅</option>
						<option value="I7">건축디자인</option>
						<option value="J7">금속디자인</option>
						<option value="K7">모션그래픽</option>
						<option value="L7">설치</option>
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
				<ul class="thumbnail_list infinite_list">
					<!-- list -->
					<?php foreach ($rows as $key => $row): ?>
					<?php $this->load->view('gallery/thumbnail_inc_view', $row) ?>
					<?php endforeach ?>
				</ul>

				<a href="/gallery/listing/<?php echo ($this->uri->segment(3))?$this->uri->segment(3)+1:2; ?>" class="more-link">more</a>
<?php if (!$this->input->is_ajax_request()): ?>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>