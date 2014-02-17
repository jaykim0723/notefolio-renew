<?php
if (!is_file($this->input->server('DOCUMENT_ROOT')."/data/profiles/{$row->info->user_A['username']}_face.jpg"))
    $profile_image = '/img/default_profile_face.png';
else
    $profile_image = "/data/profiles/{$row->info->user_A['username']}_face.jpg?";
?>
<li class="thumbbox infinite-item">
	<?=(isset($row->info->comment))?$row->info->comment:''; ?>
	<?=$this->nf->print_time($row->regdate) ?>
	<a href="/<?=$row->info->user_B['username']?>/<?=$row->info->work['work_id']?>">
		<img src="<?=$profile_image?>"/>
	</a>
</li>