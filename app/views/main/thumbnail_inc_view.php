<?php
$wide = in_array($row->key, array(4,11));
?>
<li class="thumbbox infinite-item <?php echo $wide ? 'wide' : '' ?>">
	<a class="go-to-work-info" href="/<?php echo $row->user->username ?>/<?php echo $row->work_id ?>">
		<img src="/data/covers/<?=($row->work_id).(($wide)?'_t3':'_t2')?>.jpg"/>
		<span class="main-work-info si-main-info-bg" style="">
			<i class="spi spi-view" style="margin-top:-2px;margin-right:5px;">Hit</i> <?php echo $row->hit_cnt ?>
			<i class="spi spi-love" style="margin-top:-2px;margin-right:5px;margin-left:5px;">Note</i> <?php echo $row->note_cnt ?>
			<!-- <i class="spi spi-comment" style="margin-top:-2px;margin-right:5px;">comment</i> <?php echo $row->comment_cnt ?> -->
		</span>
		<span class="main-work-title">
			<span class="pull-right">
				<span class="pull-right ellipsis" style="margin-top: 6px;margin-left: 5px;"><?php echo $row->user->realname; ?></span>
				<span class="pull-right main-work-face">
					<img src="/data/profiles/<?=$row->user->username?>_face.jpg?h=1385655105" alt=""/>
					<i class="si si-face-small"></i>
				</span>
			</span>
			<span style="line-height:240%"><?php echo $row->title; ?></span>
		</span>
	</a>
</li>
