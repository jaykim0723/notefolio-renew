<li id="work-recent-<?php echo $row->work_id ?>">
	<img src="/data/covers/<?php echo $row->work_id?>_t2.jpg?_=<?php echo substr($row->moddate, -2) ?>"/>
	<?php echo htmlentities($row->title, ENT_COMPAT, 'utf-8'); ?>
</li>
