<li id="work-recent-<?php echo $row->work_id ?>" class="<?php echo $row->status ?>">
	<a href="/<?php echo $row->user->username ?>/<?php echo $row->work_id ?>" class="ellipsis">
		<img src="/data/covers/<?php echo $row->work_id; ?>_t1.jpg?_=<?php echo substr($row->moddate, -3) ?>"/>
		<?php echo htmlentities($row->title, ENT_COMPAT, 'utf-8'); ?>
	</a>
</li>
