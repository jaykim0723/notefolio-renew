<form action="" class="comment-block create" data-mode="create" data-parent_id="" data-comment_id="">
	<div class="comment-action-area">
		<button tabindex="2" class="btn btn-nofol" type="submit"><i class="spi spi-write"></i></button>
		<a class="btn btn-nofol btn-cancel-comment"><i class="spi spi-close">close</i></a>
	</div>
	<a class="comment-profile-area" href="<?=(USER_ID>0)?site_url($this->session->userdata('username')):'javascript:return;'?>" <?php if(USER_ID>0){?>target="_blank"<?php }?>>
		<?php if (USER_ID==0): ?>
			<img src="/img/default_profile_face.png"/>
		<?php else: ?>
			<img src="<?php echo '/data/profiles/'.$this->session->userdata('username').'_face.jpg?_='.substr($this->nf->get('user')->modified,-2) ?>" alt="" onerror="this.src='/img/default_profile_face.png'"/>
		<?php endif ?>
		<i class="si si-face-medium"></i>
	</a>
	<textarea tabindex="1" name="content" class="comment-textarea"></textarea>
</form>
