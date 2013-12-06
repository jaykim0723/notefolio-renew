<?php foreach($row as $i => $r): ?>

	<div class='recommend_block' <?php if(($i+1)%3==0) echo 'style="margin-right:0;"'; ?>>
		<img src="<?php echo $r['profile_image']?>" class='thumbnail profile'/>
		<div class='realname'><?php echo $r['realname']?></div>
			<a class='pure-button btn-follow follow' data-id="<?php echo $r['user_id']?>">Follow</a>
	</div>

<?php endforeach?>