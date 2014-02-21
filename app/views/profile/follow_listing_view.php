
<?php if (!$this->input->is_ajax_request()): ?>
<section class="listing">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
<?php endif ?>
			<?php if (empty($rows)): ?>
				
				<div class="alert alert-info" style="color:#7a7880;text-align:center;">
					<?php $is_me = (isset($this->nf->get('user')->username) && $this->nf->get('user')->username == $row->username); ?>
				  	<?php if($this->uri->segment(2)=="followings"){ ?>
				  		<strong>아직 <?=($is_me)?$row->realname.'님이 ':'';?>팔로우하는 사람이 없습니다.</strong>
				  	<?php } else if($this->uri->segment(2)=="followers"){ ?>
				  		<strong>아직 <?=($is_me)?$row->realname.'님을 ':'나를 ';?>팔로우 하는 사람이 없습니다.</strong>
				  	<?php } else { ?>
				  		<strong>응? 이걸 어떻게 보셨나요? <a href="/info/contact_us">알려주세요...</a></strong>
				  	<?php } ?>
				</div>

			<?php else: ?>

				<ul class="follow-list infinite-list mode-<?php echo $mode ?>">
					<!-- list -->
					<?php foreach ($rows as $key => $row): ?>
					<?php $this->load->view('profile/follow_inc_view', array('row'=>$row)) ?>
					<?php endforeach ?>
				</ul>
			<?php endif ?>
				<a href="/<?php echo $this->uri->segment(1) ?>/<?php echo $mode ?>/<?php echo ($page)?$page+1:2; ?>" class="more-link btn btn-default btn-block btn-more" style="float:left;">more</a>
<?php if (!$this->input->is_ajax_request()): ?>
			</div>
		</div>
	</div>
</section>

<?php if ($page==1): ?>
<script>
	site.restoreInifiniteScroll();
</script>
<?php endif ?>

<?php endif; ?>