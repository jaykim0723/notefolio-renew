<div id="comment-<?php echo $row->comment_id ?>" data-id="<?php echo $row->comment_id ?>" class="comment-block">
	<?php if ($row->parent_id==0 && USER_ID!=0): ?>
	<a href="javascript:;" class="btn btn-nofol btn-no-border btn-reply-comment">reply</a>
	<?php endif ?>

	<div class="comment-inner">
		<div class="comment-control-area">
			<?php if (USER_ID!=0 && $row->user->user_id==USER_ID): ?>
			<a href="javascript:;" class="btn btn-nofol btn-no-border btn-update-comment"><i class="spi spi-edit">edit</i></a>
			<a href="javascript:;" class="btn btn-nofol btn-no-border btn-delete-comment"><i class="spi spi-delete">delete</i></a>
			<?php endif ?>
		</div>
		<div class="comment-control-area-mobile visible-xs visible-sm">
			<?php if (USER_ID!=0): ?>
			<div class="btn-group pull-right">
				<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="cursor:default;<?php if (USER_ID!=0 && $row->user->user_id==USER_ID){echo 'cursor:pointer !important';} ?>">
					<span class="caret" style="border-top-color:#fff;<?php if (USER_ID!=0 && $row->user->user_id==USER_ID){echo 'border-top-color:#ccc !important;';} ?>"></span>
				</button>
				<ul class="dropdown-menu">
					<?php if (USER_ID!=0 && $row->user->user_id==USER_ID): ?>
						<li><a href="javascript:;" class="btn btn-nofol btn-no-border btn-update-comment">edit</a></li>
						<li><a href="javascript:;" class="btn btn-nofol btn-no-border btn-delete-comment">delete</a></li>
					<?php endif ?>
				</ul>
			</div>
			<?php endif; ?>
		</div>
		<a class="comment-profile-area" target="_blank" href="<?php echo site_url($row->user->username); ?>">
			<img class="icon-round" src="<?php echo site_url('data/profiles/'.$row->user->username) ?>_face.jpg?_=<?php echo substr($row->user->modified,-2) ?>" alt="" onerror="this.src='/img/default_profile_face.png'"/>
			<!--[if lte IE 9]><i class="si si-face-medium"></i><![endif]-->
		</a>
		<div class="comment-username">
			<a target="_blank" href="<?php echo site_url($row->user->username); ?>"><?php echo $row->user->realname; ?></a>
			<span><?php echo $this->nf->print_time($row->moddate); ?></span>
		</div>
		<div class="comment-textarea">
			<?php echo nl2br($row->content); ?>
		</div>
	</div>

	<?php if ( ($row->parent_id==0) && ($row->children_cnt > 0) ): ?>
	<div class="comment-replies">
			<?php foreach ($row->children as $key => $child): ?>
				<?php $this->load->view('comment/comment_block_view', array('row' => $child), FALSE); ?>
			<?php endforeach ?>
	</div>
	<?php else: ?>
	<div class="comment-replies blank">
	</div>
	<?php endif ?>
</div>