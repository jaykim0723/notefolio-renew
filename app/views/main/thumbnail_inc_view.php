<?php
$wide = in_array($row->key, array(4,11));
?>
<li class="thumbbox infinite-item <?php echo $wide ? 'wide' : '' ?>">
	<a class="go-to-work-info" href="/<?php echo $row->user->username ?>/<?php echo $row->work_id ?>">
		<img src="/img/thumb<?php echo $wide ? '_wide' : '' ?><?php echo rand(0,7) ?>.jpg"/>
		<span class="main-work-info si-main-info-bg" style="">
			<i class="spi spi-view"></i> <?php echo $row->hit_cnt ?>
			<i class="spi spi-love"></i> <?php echo $row->note_cnt ?>
			<i class="spi spi-love_hover"></i> <?php echo $row->collect_cnt ?>
		</span>
		<span class="main-work-title">
			<span class="pull-right">
				<span class="main-work-face">
					<img src="http://notefolio.net/profiles/147?h=1385655105" alt=""/>
					<i class="si si-face-small"></i>
				</span>
				<?php echo $row->user->username; ?>
			</span>
			<?php echo $row->title; ?>
		</span>
	</a>
</li>
