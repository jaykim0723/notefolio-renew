<?php
if (!is_file($this->input->server('DOCUMENT_ROOT')."/data/profiles/{$row->info->user_A['username']}_face.jpg"))
    $profile_image = '';
else
    $profile_image = "/data/profiles/{$row->info->user_A['username']}_face.jpg";
?>
<li class="thumbbox infinite-item">
	<?=(isset($row->info->comment))?$row->info->comment:''; ?>
	<?=$this->nf->print_time($row->regdate) ?>
	<a href="/<?=$row->info->user_B['username']?>/<?=$row->info->work['work_id']?>">
		<img src="<?=$profile_image?>"/>
	</a>
</li>

<?/*
<?php
if(!empty($profile_image)) {
    $profile_image = $profile_image;
} else if (!is_file($this->input->server('DOCUMENT_ROOT').'/profiles/'.(isset($user_id)?$user_id:0)))
    $profile_image = '/images/profile_img';
else
    $profile_image = '/profiles/'.$user_id;

if(empty($user_link)){
    $user_link = '/'.$username;
}
if(empty($user_link_A)){
    $user_link_A = '/'.$username_A;
}
if(empty($user_link_B)){
    $user_link_B = '/'.$username_B;
}

if(empty($collect_link))
    $collect_link = '/'.$username_A.'/collection'; 
        


if(!empty($work_url)&&!empty($work_realname)&&!empty($work_title)&&!empty($work_keyword)){
    $work_url=$work_url;
    $work_realname=$work_realname;
    $work_title=$work_title;
    $work_keyword=$work_keyword;
    $work_img=$work_img;
    
} else if(isset($work[$work_id])){
    $work_url="/gallery/".$work_id;
    $work_realname=$work[$work_id]['realname'];
    $work_title=$work[$work_id]['title'];
    $work_keyword=$this->notefolio->print_keywords($work[$work_id]['keyword']);
    if (!is_file($this->input->server('DOCUMENT_ROOT').'/thumbnails/'.(isset($work_id)?$work_id:0)))
        $work_img = '/images/work_thumbnail';
    else
        $work_img = '/thumbnails/'.$work_id;
        
}
else {
    $work_url='#';
    $work_realname='';
    $work_title='작품이 삭제되었습니다.';
    $work_keyword='';
    $work_img = '/images/work_thumbnail';
}

?>
            <li id="f<?=$id?>" class="span4 thumbnail_feed">
                <div class="notifi_wrap">
                 <img class="user_pic" src="<?=$profile_image?>"/>
                    <div class="notifi_info"> 
<?php
    if($type=="new_upload"){
?>
                            <a class="info_link" href="<?=$user_link?>"><?=$realname?></a>님이 
                            <a class="info_link" href="<?=$work_url?>">새로운 작품</a>을 공개하였습니다.
<?php
    } else if($type=="add_note"){
?>
                            <a class="info_link" href="<?=$user_link_A?>"><?=$realname_A?></a>님이 
                            <a class="info_link" href="<?=$user_link_B?>"><?=$realname_B?></a>님의
                            <a class="info_link" href="<?=$work_url?>">작품</a>을 NOTE IT 하였습니다.
<?php
    } else if($type=="add_comment"){
?>
                            <a class="info_link" href="<?=$user_link_A?>"><?=$realname_A?></a>님이 
                            <a class="info_link" href="<?=$user_link_B?>"><?=$realname_B?></a>님의
                            <a class="info_link" href="<?=$work_url?>">작품</a>에 댓글을 남겼습니다.
                            <?=(!empty($comment))?'"'.$comment.'"':''?>
<?php
    } else if($type=="add_collect"){
?>
                            <a class="info_link" href="<?=$user_link_A?>"><?=$realname_A?></a>님이 
                            <a class="info_link" href="<?=$user_link_B?>"><?=$realname_B?></a>님의
                            <a class="info_link" href="<?=$work_url?>">작품</a>을 <a href="<?=$collect_link?>" class="info_link">콜렉션</a>에 담았습니다.
                            <?=(!empty($comment))?'"'.$comment.'"':''?>
<?php
    }
?>
                    </div>
                </div>
<?php

?>
                
                <span class="rollover_feed">
                    <span class='ctl'></span>
                    <span class='realname'><?=$work_realname?></span>
                    <span class='title'><?=$work_title?></span>
                    <span class='keywords'><?=$work_keyword?></span>
                </span>
                <a href="<?=$work_url?>" class="feed_cover">
                    <img src="<?=$work_img?>" class="feed_img"/>
                </a>
                <span class="wf-active">
                    <div class="feed_time"><?=$regdate?></div>
                </span>
                
            </li>
*/?>