<li class="thumbbox infinite-item">
	<a href="/<?php echo $row->user->username ?>/<?php echo $row->work_id ?>">
		<img src="/data/covers/<?=($row->work_id)?>_t2.jpg"/>

	<!-- 	<span class="videoicon <?php echo $row->is_video ?>">
			<i class="spi spi-video_white <?php echo $row->is_video ?>"></i>video
		</span> -->
		<em class="pi pi-videoy <?php echo $row->is_video ?>">videoy</em>
		<span class="main-work-title">
			<span class="pull-right go-profile-area" data-username="<?php echo $row->user->username ?>">
				<span class="pull-right ellipsis" style="margin-top: 6px;margin-left: 5px;"><?php echo $row->user->realname; ?></span>
				<span class="pull-right main-work-face">
					<img src="/data/profiles/<?=$row->user->username?>_face.jpg?_=<?php echo substr($row->user->modified,-2) ?>" alt="" onerror="this.src='/img/default_profile_face.png'"/>
					<i class="si si-face-small"></i>
				</span>
			</span>
			<span class="work-title-char"><?php echo $row->title; ?></span>
		</span>

	</a>
</li>