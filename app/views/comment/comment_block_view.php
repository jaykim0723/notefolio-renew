<div id="comment-<?php echo $row->comment_id ?>" data-id="<?php echo $row->comment_id ?>" class="comment-block">
	<div class="comment-control-area">
		<a href="" class="btn btn-link">modify</a>
		<a href="" class="btn btn-link btn-delete-comment">delete</a>
		<a href="" class="btn btn-link">reply</a>
	</div>
	<a class="comment-profile-area" target="_blank" href="<?php echo site_url($row->user->username); ?>">
		<img src="<?php echo site_url('data/profiles/'.$row->user->username) ?>.jpg" alt="">
		<i class="si si-face-medium"></i>
	</a>
	<div class="comment-textarea">
		<?php echo htmlentities($row->content, ENT_COMPAT, 'UTF-8'); ?>
	</div>
</div>