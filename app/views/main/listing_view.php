<?php if (!$this->input->is_ajax_request()): ?>
<section class="listing">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<?php if ($page==1): ?>
				<ul id="main-list-top" class="main-thumbnail-list">
					<?php $this->load->view('main/thumbnail_inc_view', array('row' => $first)) ?>
					<li class="thumbbox hidden-xs hidden-sm">
						<h2 id="main-hot-creators-title" class="nofol-title">Hot Creators</h2>
						<ul id="main-hot-creators">
							<?php foreach ($creators as $key => $row): ?>
							<li>
								<a href="<?php echo site_url($row->username) ?>">
									<span class="hot-arrow">
										<i class="spi spi-next">next</i>
										<i class="spi spi-next_white">next</i>
									</span>
									<span class="hot-face"> <!-- bg here -->
										<img class="icon-round" src="/data/profiles/<?=$row->username?>_face.jpg?_=<?php echo substr($row->modified,-2) ?>" alt="" onerror="this.src='/img/default_profile_face.png'"/>
										<!--[if lte IE 9]><i class="si si-face-medium"></i><![endif]-->
										<i class="si si-face-medium_point"></i>
									</span>
									<span class="hot-center">
										<span class="hot-username">
											<?php echo $row->realname; ?>
										</span>
										<span class="hot-keywords">
											<?php echo $this->nf->category_to_string($row->user_keywords); ?>
										</span>
										<span class="hot-go">
											Go To profile
										</span>
									</span>
								</a>
							</li>	
							<?php endforeach ?>
						</ul>
					</li>
				</ul>
				<?php endif ?>

<?php endif ?>

				<ul class="main-thumbnail-list infinite-list">
					<!-- list -->
					<?php foreach ($rows as $key => $row):
					$row->key = $key;
					?>
					<?php $this->load->view('main/thumbnail_inc_view', array('row' => $row)) ?>
					<?php endforeach ?>
				</ul>

				<a href="/main/listing/<?php echo ($page)?$page+1:2; ?>" class="btn btn-default btn-block more-link btn-more">more</a>


				
<?php if (!$this->input->is_ajax_request()): ?>

			</div>
		</div>
	</div>
</section>

<?php if ($page==1): ?>
<script>
	site.restoreInifiniteScroll();
</script>
<?php endif ?>


<?php endif; ?>