<li class="thumbbox infinite-item">
	<div class="follow-recent-works">
		<?php foreach ($row->recent_works as $key => $row): ?>
			<a href="/<?php echo $row->username ?>/<?php echo $row->work_id ?>">
				<img src="/data/covers/<?php echo $row->work_id ?>-t1.jpg?_=<?php echo substr($row->modified, -2); ?>" alt="">
			</a>
		<?php endforeach ?>
	</div>
	<div class="follow-face">
		<img src="/data/profiles/<?php echo $row->username ?>.jpg?_=<?php echo substr($row->modified, -2); ?>" alt="">
		<i class="si si-face-large"></i>
	</div>
	<div class="follow-center">
		<h3><?php echo $row->username; ?></h3>
		<p><?php echo @implode(', ', $row->keywords); ?></p>
		<button data-id="<?php echo $row->user_id ?>" class="btn btn-follow btn-nofol btn-hover <?php echo $row->is_follow=='y'?'activated' : '' ?>">
			<i class="spi spi-follow"></i>
			<span>Follow<?php echo $row->is_follow=='y'?'ing' : '' ?></span>
		</button>
	</div>
</li>
