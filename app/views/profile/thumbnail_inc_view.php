<li class="thumbbox infinite-item <?php echo $row->status ?>">
	<a href="/<?php echo $row->user->username ?>/<?php echo $row->work_id ?>">
		<?php echo htmlentities($row->title, ENT_COMPAT, 'utf-8'); ?>
		<img src="/data/covers/<?=($row->work_id)?>_t1.jpg"/>
	</a>
</li>
