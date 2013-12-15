<?php
$wide = in_array($key, array(4,11));
?>
<li class="thumbbox infinite-item <?php echo $wide ? 'wide' : '' ?>">
	<a class="go-to-work-info" href="/<?php echo $user->username ?>/<?php echo $work_id ?>">
		<img src="/img/thumb<?php echo $wide ? '_wide' : '' ?><?php echo rand(0,7) ?>.jpg"/>
		<span class="main-work-info spi-main-info-bg" style="">
			<i class="spi spi-view"></i> 234
			<i class="spi spi-love"></i> 2342
			<i class="spi spi-love_hover"></i> 2342
		</span>
		<span class="main-work-title">
			<span class="pull-right"><?php echo $user->username; ?></span>
			<?php echo $title; ?>
		</span>
	</a>
</li>
