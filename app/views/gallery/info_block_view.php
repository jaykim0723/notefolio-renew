<?php switch($t){ case 'image':  ?>
<li class="block-image" data-id="<?php echo $i ?>">
	<a href="<?php echo htmlentities(str_replace('_v2', '_v1', $c), ENT_COMPAT, 'utf-8') ?>" target="_blank"><img src="<?php echo htmlentities($c, ENT_COMPAT, 'utf-8') ?>" alt=""></a>
</li>





<?php break; case 'video': ?>
<li class="block-video">
	<iframe src="<?php echo $c ?>" frameborder="0"></iframe>
</li>






<?php break; case 'text': ?>
<li class="block-text">
	<?php echo nl2br($c); ?>
</li>







<?php break; case 'line': ?>
<li class="block-line">
</li>







<?php } ?>