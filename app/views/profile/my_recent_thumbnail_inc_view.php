<li id="work-recent-<?php echo $row->work_id ?>">
	<a href="/<?php echo $row->user->username ?>/<?php echo $row->work_id ?>" class="ellipsis">
		<img src="/img/dummy<?php echo rand(0,9) ?>.jpg"/>
		<?php echo htmlentities($row->title, ENT_COMPAT, 'utf-8'); ?>
	</a>
</li>
