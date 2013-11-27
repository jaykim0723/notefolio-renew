<?php
$wide = in_array($key, array(4,11));
?>
<li class="thumbbox infinite-item <?php echo $wide ? 'wide' : '' ?>">
	<a href="/gallery/<?php echo $work_id ?>">
		<img src="/img/thumb<?php echo $wide ? '_wide' : '' ?>.gif"/>
	</a>
</li>
