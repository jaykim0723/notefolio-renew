<div id="comment-<?php echo $row->comment_id ?>" data-id="<?php echo $row->comment_id ?>" class="comment-block">

	<div class="comment-inner">
		<div class="comment-control-area">
			<?php if (USER_ID!=0 && $row->user->user_id==USER_ID): ?>
			<a href="javascript:;" class="btn btn-link btn-update-comment">modify</a>
			<a href="javascript:;" class="btn btn-link btn-delete-comment">delete</a>
			<?php endif ?>
			<?php if (USER_ID!=0): ?>
			<a href="javascript:;" class="btn btn-link btn-reply-comment">reply</a>
			<?php endif ?>
		</div>
		<a class="comment-profile-area" target="_blank" href="<?php echo site_url($row->user->username); ?>">
			<img src="<?php echo site_url('data/profiles/'.$row->user->username) ?>.jpg" alt="">
			<i class="si si-face-medium"></i>
		</a>
		<div class="comment-textarea">
			<?php echo htmlentities($row->content, ENT_COMPAT, 'UTF-8'); ?>
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