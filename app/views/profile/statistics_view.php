
<?php if (!$this->input->is_ajax_request()): ?>
<section>
	<div class="container" style="margin-top:20px;">
		<div class="row">
			<div class="col-md-12">
<?php endif ?>
			

		


			<div id="statistics-chart-area" style="background: #fff;">
				<div class="row">
					<div class="col-sm-6">
						<h4 id="statistics-header">
							조회수 <span id="statistics-total-hit"></span>
							/
							노트수 <span id="statistics-total-note"></span>
							/
							콜렉트수 <span id="statistics-total-collect"></span>
						</h4>
					</div>
					<div class="col-sm-6" id="statistics-toolbars">
						<div class="btn-group pull-right">
							<button type="button" class="btn btn-default btn-sm dropdown-toggle btn-smallgray" data-toggle="dropdown">
								<span id="statistics-period">기간설정</span> <span class="caret"></span>
							</button>
							<ul class="dropdown-menu" role="menu">
								<li><a data-type="period" data-value="latest1">최근 1개월</a></li>
								<li><a data-type="period" data-value="latest3">최근 3개월</a></li>
								<li><a data-type="period" data-value="this_m">이번 달</a></li>
								<li><a data-type="period" data-value="prev_m">저번 달</a></li>
								<li class="divider"></li>
								<li><a data-type="period" data-value="user">임의설정</a></li>
							</ul>
						</div>
						<div class="btn-group pull-right">
							<button type="button" class="btn btn-default btn-sm dropdown-toggle btn-smallgray" data-toggle="dropdown">
								<span id="statistics-type">기간내 조회수 변동</span> <span class="caret"></span>
							</button>
							<ul class="dropdown-menu" role="menu">
								<li><a data-type="type" data-value="hit">조회수 변동</a></li>
								<li><a data-type="type" data-value="note">노트수 변동</a></li>
								<li><a data-type="type" data-value="collect">콜렉트수 변동</a></li>
								<li><a data-type="type" data-value="work">작품수 변동</a></li>
							</ul>
						</div>
					</div>
				</div>
				<div id="statistics-chart" style="height:300px;">
				</div>
				<div id="statistics-tooltip"></div>
			</div>

			<div id="statistics-table-area">
			</div>

			<script src="/js/libs/bootstrap-datepicker.js"></script>
			<script src="/js/libs/jquery.flot.js"></script>
			<script src="/js/libs/jquery.dataTables.js"></script>
			<script src="/js/libs/dataTables.bootstrap.js"></script>
			<script>
				if($('#style_datatable').length==0)
					$('head').append('<link id="style_datatable" href="/css/dataTables.bootstrap.css" rel="stylesheet"/>');
				if($('#style_datepicker').length==0)
					$('head').append('<link id="style_datepicker" href="/css/datepicker.css" rel="stylesheet"/>');

				$(function(){
					profileUtil.statistics.setGround();
				})
			</script>
<?php if (!$this->input->is_ajax_request()): ?>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>