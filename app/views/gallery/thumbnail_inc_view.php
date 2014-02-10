<li class="thumbbox infinite-item">
	<a href="/<?php echo $row->user->username ?>/<?php echo $row->work_id ?>">
		<img src="/data/covers/<?=($row->work_id)?>_t2.jpg"/>
		<i class="spi spi-video_white <?php echo $row->is_video ?>">video_white</i>
		<span class="main-work-title">
			<span class="pull-right">
				<!-- <span class="pull-right ellipsis" style="margin-top: 6px;margin-left: 5px;"><?php echo $row->user->realname; ?></span> -->
				<span class="pull-right main-work-face">
					<img src="/data/profiles/<?=$row->user->username?>_face.jpg?_=<?php echo substr($row->user->modified,-2) ?>" alt=""/>
					<i class="si si-face-small"></i>
				</span>
			</span>
			<span class="work-title-char"><?php echo $row->title; ?></span>
		</span>

	</a>
</li>