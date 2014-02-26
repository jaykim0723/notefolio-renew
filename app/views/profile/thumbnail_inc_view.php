<li class="thumbbox infinite-item <?php echo $row->status ?>">
	<a href="/<?php echo $row->user->username ?>/<?php echo $row->work_id ?>">
		<img src="/data/covers/<?=($row->work_id)?>_t1.jpg"/><br>
		<p><?php echo trim(htmlentities($row->title, ENT_COMPAT, 'utf-8')); ?></p>
	</a>
</li>
