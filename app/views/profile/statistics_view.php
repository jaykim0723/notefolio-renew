
<?php if (!$this->input->is_ajax_request()): ?>
<section>
	<div class="container">
		<div class="row">
			<div class="col-md-12">
<?php endif ?>
			

			<table id="statistics-widgets" class="table table-bordered">
				<thead>
					<tr>
						<th>총 작품수</th>
						<th>총 조회수</th>
						<th>총 노트수</th>
						<th>총 콜렉트수</th>
						<th>총 팔로워수</th>
						<th>총 팔로윙수</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><?php echo number_format($total->work_cnt) ?></td>
						<td><?php echo number_format($total->view_cnt) ?></td>
						<td><?php echo number_format($total->note_cnt) ?></td>
						<td><?php echo number_format($total->collect_cnt) ?></td>
						<td><?php echo number_format($total->follower_cnt) ?></td>
						<td><?php echo number_format($total->following_cnt) ?></td>
					</tr>
				</tbody>
			</table>
		


			<div id="statistics-chart-area" style="background: #fff;height:400px;">
				<div class="row">
					<div class="col-sm-6">
						<h4>
							조회수 <span id="statistics-total-view"></span>
							/
							노트수 <span id="statistics-total-note"></span>
							/
							콜렉트수 <span id="statistics-total-collect"></span>
						</h4>
					</div>
					<div class="col-sm-6">
						<div class="btn-group pull-right">
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
								<span id="statistics-period">기간설정</span> <span class="caret"></span>
							</button>
							<ul class="dropdown-menu" role="menu">
								<li><a href="#">최근 1개월</a></li>
								<li><a href="#">최근 3개월</a></li>
								<li><a href="#">이번 달</a></li>
								<li><a href="#">저번 달</a></li>
								<li class="divider"></li>
								<li><a href="#">임의설정</a></li>
							</ul>
						</div>
						<div class="btn-group pull-right">
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
								<span id="statistics-type">기간내 조회수 보기</span> <span class="caret"></span>
							</button>
							<ul class="dropdown-menu" role="menu">
								<li><a href="#">조회수 보기</a></li>
								<li><a href="#">노트수 보기</a></li>
								<li><a href="#">작품수 보기</a></li>
								<li><a href="#">콜렉트수 보기</a></li>
							</ul>
						</div>
					</div>
				</div>
				<div id="statistics-chart">
				</div>
			</div>

			<div id="statistics-table-area">
			</div>


<?php if (!$this->input->is_ajax_request()): ?>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>