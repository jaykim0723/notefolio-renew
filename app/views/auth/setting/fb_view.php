<?php $ci = get_instance(); ?>
<fieldset id='auth_facebook' <?php echo $this->uri->segment(2) == 'setting' ? 'style="margin-bottom:30px;"' : ''?>>
    <legend>페이스북 연동</legend>
<?php
 if ($fb_num_id&&$fb_num_id>0){
?>    
    <div class="row-fluid">
        <div class="span12">
            <div class="fb_connect">
                <div class="fb_label">
                    <label>Facebook과 연동되어 있습니다.</label>
                    <?php /* ?><p class="small">Number ID: <?=$fb_num_id?></p><?php */ ?>
                    <input type='hidden' id='fb_num_id' name='fb_num_id' value='<?=$fb_num_id?>' />
                </div>
                <div class="fb_button">
                    <a class="pure-button btn-fbconnect pure-button-selected">Connected</a>
                </div>
            </div>
            <div class="fb_connect">
                <div class="fb_label">
                    <div class='fb_check'>새로운 작품을 업로드 했을 때 Facebook에 알립니다.</div>
                </div>
                <div class="fb_button">
                    <label class='notefolio-checkbox inline'>
                        <input type='checkbox' id='fb_post_work' name='fb_post_work' value='Y'<?=($fb_post_work=='Y')?' checked="checked"':''?>/>&nbsp;
                    </label>
                </div>
            </div>
            <div class="fb_connect">
                <div class="fb_label">
                    <div class='fb_check'>작품에 댓글을 달았을 때 Facebook에 알립니다.</div>
                </div>
                <div class="fb_button">
                    <label class='notefolio-checkbox inline'>
                    <input type='checkbox' id='fb_post_comment' name='fb_post_comment' value='Y'<?=($fb_post_comment=='Y')?' checked="checked"':''?>/>&nbsp;
                    </label>
                </div>
            </div>
            <div class="fb_connect">
                <div class="fb_label">
                    <div class='fb_check'>작품을 NOTE IT 했을 때 Facebook에 알립니다.</div>
                </div>
                <div class="fb_button">
                    <label class='notefolio-checkbox inline'>
                    <input type='checkbox' id='fb_post_note' name='fb_post_note' value='Y'<?=($fb_post_note=='Y')?' checked="checked"':''?>/>&nbsp;
                    </label>
                </div>
            </div>                      
        </div>
    </div>
    
    <script>
        $(function(){
            $('.btn-fbconnect').on('click', function() {
                var fb_diag = window.open('<?=$ci->config->item('base_url')?>fbauth/unlink','fb_diag','width=600,height=300,scrollbars=yes,resizable=no');
                //fb_diag.focus();
                //msg.error('에러가 발생하였습니다.');
<?php /* ?>
                var post_data = {action:"unlink"};

                $.post('/auth/fb/set_action', post_data, function(d){
                    if(d=='TRUE')
                        window.location.reload();
                    else
                        msg.error('에러가 발생하였습니다.');
                });
<?php */ ?>
            });
            
            $('.fb_button').each(function(){                                            // 각 체크박스 버튼마다
                if($('input',this).attr("checked")=="checked"){                         // 체크되었다면
                    $('label.notefolio-checkbox',this).addClass("checked");             // 체크해 주고
                }
            });
            
            $('.fb_button').off('click', 'label.notefolio-checkbox').on('click', 'label.notefolio-checkbox', function(){ // 체크 트리거
                $(this).toggleClass("checked");
                if($('input',$(this).parent()).attr("checked")!="checked"&&$(this).hasClass("checked")){ //클릭시 체크연동
                    $('input',$(this).parent()).attr("checked", "checked");
                } else {
                    $('input',$(this).parent()).removeAttr("checked");
                }
                
            });
        });
    </script>
<?php
 } else {
?>      
    <div class="row-fluid">
        <div class="span12">
            <div class="fb_connect">
                <div class="fb_label">
                    <label>Facebook과 연동되어있지 않습니다.</label>
                </div>
                <div class="fb_button">
                    <a class="btn btn-default btn-fbconnect">Connect</a>
                </div>
            </div>                          
        </div>
    </div>  
    
    <script>
        $(function(){
            $('.btn-fbconnect').on('click', function() {
                var fb_diag = window.open('<?=$ci->config->item('base_url')?>fbauth/link','fb_diag','width=600,height=300,scrollbars=yes,resizable=no');
                fb_diag.focus();
                //msg.error('에러가 발생하였습니다.');
            });
        });
    </script>
<?php
 }
?>
</fieldset>


