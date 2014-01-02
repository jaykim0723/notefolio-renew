<form action="" class="comment-block" data-mode="create" data-parent_id="">
	<a class="comment-profile-area" href="<?php echo site_url($this->session->userdata('username')) ?>" target="_blank">
		<img src="<?php echo '/data/profiles/'.$this->session->userdata('username').'.jpg' ?>" alt="">
		<i class="si si-face-medium"></i>
	</a>
	<textarea name="content" class="comment-textarea"></textarea>
	<div class="comment-action-area">
		<button class="btn btn-nofol bg1" type="submit"><i class="spi spi-write"></i></button>
		<a class="btn btn-nofol bg4"><i class="spi spi-write"></i></a>
	</div>
</form>
