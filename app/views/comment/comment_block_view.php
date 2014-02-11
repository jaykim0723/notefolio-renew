<div id="comment-<?php echo $row->comment_id ?>" data-id="<?php echo $row->comment_id ?>" class="comment-block">
	<?php if (USER_ID!=0): ?>
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
				<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
					<span class="caret"></span>
				</button>
				<ul class="dropdown-menu">
					<?php if (USER_ID!=0 && $row->user->user_id==USER_ID): ?>
						<li><a href="javascript:;" class="btn btn-nofol btn-no-border btn-update-comment"><i class="spi spi-edit">edit</i></a></li>
						<li><a href="javascript:;" class="btn btn-nofol btn-no-border btn-delete-comment"><i class="spi spi-delete">delete</i></a></li>
						<?php endif ?>
						<?php if (USER_ID!=0): ?>
						<li><a href="javascript:;" class="btn btn-nofol btn-no-border btn-reply-comment"><i class="spi spi-reply">reply</i></a></li>
					<?php endif ?>
				</ul>
			</div>
			<?php endif; ?>
		</div>
		<a class="comment-profile-area" target="_blank" href="<?php echo site_url($row->user->username); ?>">
			<img src="<?php echo site_url('data/profiles/'.$row->user->username) ?>_face.jpg" alt="">
			<i class="si si-face-medium"></i>
		</a>
		<div class="comment-username">
			<a target="_blank" href="<?php echo site_url($row->user->username); ?>"><?php echo $row->user->realname; ?></a>
			<span><?php echo $this->nf->print_time($row->moddate); ?></span>
		</div>
		<div class="comment-textarea">
			<?php echo nl2br(htmlentities($row->content, ENT_COMPAT, 'UTF-8')); ?>
		</div>
	</div>

	<?php if ($row->parent_id==0): ?>
	<div class="comment-replies">
		<?php if($row->children_cnt > 0): ?>
			<?php foreach ($row->children as $key => $child): ?>
				<?php $this->load->view('comment/comment_block_view', array('row' => $child), FALSE); ?>
			<?php endforeach ?>
		<?php endif; ?>
	</div>
	<?php endif ?>
</div>