<?php
if (!is_file($this->input->server('DOCUMENT_ROOT')."/data/profiles/{$row->info->user_A['username']}_face.jpg"))
    $profile_image = '/img/default_profile_face.png';
else
    $profile_image = "/data/profiles/{$row->info->user_A['username']}_face.jpg";

/*
AAA님이 회원님의 작품 "작품 제목"을 NOTE 하였습니다.
AAA님이 회원님의 작품 "작품 제목"에 댓글을 남겼습니다. "댓글 내용"
AAA님이 회원님의 작품 "작품 제목"을 콜렉션에 담았습니다.
BBB님도 CCC님의 작품 "작품 제목"에 댓글을 남겼습니다. "댓글 내용"
BBB님이 회원님의 포럼 게시물 "게시물 제목"에 댓글을 남겼습니다. "댓글 내용"
BBB님도 CCC님의 포럼 게시물 "게시물 제목"에 댓글을 남겼습니다. "댓글 내용"
BBB님이 회원님을 팔로우합니다.
BBB님이 회원님과 작품 "작품 제목"을 함께 만들었다고 알렸습니다.
BBB님이 회원님의 방명록에 댓글을 남겼습니다.
*/

if(empty($row->info->user_A['realname'])){
    $row->info->user_A['realname'] = "비회원";
}

switch($row->area){
    case "user":
        switch($row->type){
            case "follow":
                $link="/{$row->info->user_A['username']}";
                $text="<b>{$row->info->user_A['realname']}</b>님이 <b>{$row->info->user_B['realname']}</b>님을 팔로우합니다.";
                break;
        }
        break;
    case "work":
        switch($row->type){
            case "note":
                $link="/{$row->info->user_B['username']}/{$row->info->work['work_id']}";
                $work_title = "\"{$row->info->work['title']}\"";
                $prep=($row->info->user_B['id']==USER_ID)?'이':'도';
                $text="<b>{$row->info->user_A['realname']}</b>님{$prep} <b>{$row->info->user_B['realname']}</b>님의 작품 <b>{$work_title}</b> 을 좋아합니다.";
                break;
            case "collect":
                $link="/{$row->info->user_A['username']}/collect";
                $work_title = "\"{$row->info->work['title']}\"";
                $comment = "\"{$row->info->comment}\"";
                $prep=($row->info->user_B['id']==USER_ID)?'이':'도';
                $text="<b>{$row->info->user_A['realname']}</b>님{$prep} <b>{$row->info->user_B['realname']}</b>님의 작품<b> {$work_title}</b> 을 콜렉션에 담았습니다. {$row->info->comment}";
                break;
            case "comment":
                $link="/{$row->info->user_B['username']}/{$row->info->work['work_id']}";
                $work_title = "\"{$row->info->work['title']}\"";
                $comment = "\"{$row->info->comment}\"";
                $prep=($row->info->user_B['id']==USER_ID)?'이':'도';
                $text="<b>{$row->info->user_A['realname']}</b>님{$prep} <b>{$row->info->user_B['realname']}</b>님의 작품<b> {$work_title} </b>에 댓글을 남겼습니다. {$row->info->comment}";
                break;
        }
        break;        
}

$is_read = (is_null($row->readdate))?"unread":'';


?>


<li class="activity-infinite-item <?php echo $this->uri->segment(1); ?>-item clearfix">
	<a href="<?=$link?>" class="<?=$is_read?>">
		<div class="activity-infinite-icon">
			<img src="<?=$profile_image?>" alt="">
			<i class="si si-face-medium">face-medium</i>
		</div>
		<span>
			<?=$text?>
		</span>
		<small><?=$this->nf->print_time($row->regdate)?></small>
	</a>
</li>