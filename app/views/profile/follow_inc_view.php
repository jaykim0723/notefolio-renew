<li class="thumbbox infinite-item">
	<div class="follow-recent-works">
		<?php foreach ($recent_works as $key => $row): ?>
			<a href="/<?php echo $username ?>/<?php echo $row->work_id ?>">
				<img src="/data/covers/<?php echo $row->work_id ?>-t1.jpg?_=<?php echo substr($row->modified, -2); ?>" alt="">
			</a>
		<?php endforeach ?>
	</div>
	<div class="follow-face">
		<img src="/data/profiles/<?php echo $username ?>.jpg?_=<?php echo substr($modified, -2); ?>" alt="">
		<i class="si si-face-100"></i>
	</div>
	<div class="follow-center">
		<h3><?php echo $username; ?></h3>
		<p><?php echo @implode(', ', $keywords); ?></p>
		<button class="btn btn-nofol btn-hover <?php echo $is_follow=='y'?'activated' : '' ?>">
			<i class="spi spi-alarm"></i>
			Follow
		</button>
	</div>
</li>
