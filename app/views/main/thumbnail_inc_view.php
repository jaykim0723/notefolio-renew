<?php
$wide = in_array($key, array(4,11));
?>
<li class="thumbbox infinite-item <?php echo $wide ? 'wide' : '' ?>">
	<a class="go_to_work_info" href="/<?php echo $user->username ?>/<?php echo $work_id ?>">
		<img src="/img/thumb<?php echo $wide ? '_wide' : '' ?><?php echo rand(0,7) ?>.jpg"/>
	</a>
</li>
