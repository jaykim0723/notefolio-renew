<?php foreach($row as $i => $r): ?>

	<li id="reco_layer_head" class='recommend_block' <?php if(($i+1)%3==0) echo 'style="margin-right:0;"'; ?>>
		<?php foreach($r['recent_works'] as $re): ?>
			<a class="recent_works"><img class='thumbnail' src="<?php echo isset($re['thumbnail_url'])?$re['thumbnail_url']:'/images/work_thumbnail'?>"/></a>
		<?php endforeach; ?>
		<img src="<?php echo $r['profile_image']?>" class='thumbnail'/>
		<div class='realname'><a href="http://notefolio.net/<?php echo $r['username']?>" target="_blank"><?php echo $r['realname']?></a></div>
		<div class='recom_keywords' style="margin-left: 130px;"><?php echo $this->notefolio->print_keywords_reco($r['categories'], TRUE)?></div>
		<a class='pure-button btn-follow follow' data-id="<?php echo $r['user_id']?>">Follow</a>
	</li>

<?php endforeach?>

	<script>
	
	var init_int=4;
	var scroll_count=1;
	var init_count=0;
	
	$('.recommend_block').slice(init_int*scroll_count).hide();
	
	$(function(){	
			$("#reco_layer_wrapper").on('scroll', function(e){
				
				//$('#reco_layer_wrapper').scrollTop()>= $('#reco_layer_list').height() - $('#reco_layer_wrapper').height()
			if($('#reco_layer_wrapper').scrollTop()>= 20 + (560*init_count)) {
				$('.recommend_block').slice(init_int*scroll_count,init_int*(scroll_count+1)).show();	
		
				scroll_count++;
				init_count++;
			}
		});
	});
		
	</script>
