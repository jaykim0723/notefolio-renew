        <div id="auth_personal">
            <div class="row-fluid">
                <div class="span3 control-group">
                    <label class="auth_name">이름</label>
                </div>

                <div class="span10 control-group auth_left">
                    <input type="text" class="auth_input" id="realname" name="realname" value="<?=$realname?>" minlength="3" maxlength="20">
                    <span class="auth_tooltip" title="실명을 권장합니다.">[?]</span>
                </div>
            </div>

            <div class="row-fluid">
                <div class="span3 control-group">
                    <label class="auth_name">성별</label>
                </div>

                <div class="span10 control-group auth_gender auth_left">
                    <label class="notefolio-radio inline<?if($gender!="male"||$gender=='f'||empty($gender)){?> checked<?}?>">
                        <input type="radio" name="gender" value="f"<?if($gender!="male"||$gender=='f'||empty($gender)){?> checked="checked"<?}?>>여
                    </label>
                    <label class="notefolio-radio inline<?if($gender=="male"||$gender=='m'){?> checked<?}?>">
                        <input type="radio" name="gender" value="m"<?if($gender=="male"||$gender=='m'){?> checked="checked"<?}?>>남
                    </label>
                </div>
            </div>

            <div class="row-fluid">
                <div class="span3 control-group">
                    <label class="auth_name">생년월일</label>
                </div>

                <div class="span10 control-group auth_left">
                    <div id='birth_field'>
                        <select name='year' class='span3 auth_birth'>
                            <?php
                            for($i=date('Y');$i>=1900;$i--){
                            ?>
                            <option value="<?=$i?>"<?if($i==$birth[0]){?> selected<?}?>><?=$i?>년</option>
                            <?php
                            }
                            ?>
                        </select>
                        <select name='month' class='span3 auth_birth'>
                            <?php
                            for($i=1;$i<=12;$i++){
                            ?>
                            <option value="<?=$i?>"<?if($i==$birth[1]){?> selected<?}?>><?=$i?>월</option>
                            <?php
                            }
                            ?>
                        </select>
                        <select name='day' class='span3 auth_birth'>
                            <?php
                            for($i=1;$i<=31;$i++){
                            ?>
                            <option value="<?=$i?>"<?if($i==$birth[2]){?> selected<?}?>><?=$i?>일</option>
                            <?php
                            }
                            ?>
                        </select> <span class="auth_tooltip" title="생년월일은 더 나은 서비스 제공을 위해서만 이용되며, 외부로 노출되지 않습니다.">[?]</span>
                    </div>
                </div>
            </div>

            <div class="row-fluid">
                <div class="span3 control-group">
                    <label class="auth_name">개인url</label>
                </div>

                <div class="span10 control-group auth_left">
                    <span class="url_hint auth_url">notefolio.net/</span>
                    <input type="text" id="username" name="username" class="auth_input" value="<?=$username?>" minlength="3" maxlength="20"> 
                    <span class="auth_tooltip" title= "고유 프로필 주소를 지원합니다('@','#','.' 등 특수문자는 이용하실 수 없습니다.).">[?]</span>
                    <span class="check" id="checker"></span> 
                </div>
            </div>

            <div class="row-fluid">
                <div class="span3 control-group">
                    <label class="auth_name">메일링</label>
                </div>

                <div class="span9 control-group auth_check auth_left" >
                    <label class="notefolio-checkbox inline"><input type="checkbox" name="mailing" value="1">노트폴리오의 최신 소식 및 작가/작품 소개를 메일로 받겠습니다. <br></label> 
                    
                </div>
            </div>

            <div class="row-fluid"></div>
            
            <script>
<?php
if(!isset($fb_num_id)){
?>
                $('#auth_basic :radio[name=gender]').filter('input[value=f]').attr('checked', true).trigger('change');
<?            
}
?>
                
                var timeout='';
                var username_checked=false;
                var check_reg = function(v){
                        return /^[a-zA-Z0-9\_\-]+$/.test(v); // 정규식 검사하기. 
                };
                var autoCheck = function(){
                        var o = $('#username');
                        $.get('/auth/check_username_available', { username : o.val() }, function(d){
                                o = o.data('last', o.val()).next().next();
                                if(d=='y'){
                                    o.html('<img src="/images/signup/check.png">').removeClass('unavailable');
                                    username_checked=true;
                                } else {
                                    o.html('<img src="/images/signup/x.png">').addClass('unavailable');
                                    username_checked=false;
                                }
                        });
                };
                
                $(function() {
                    $(".auth_tooltip").tooltip({placement : 'right'});
                    $('#username').on('change keyup input focusout',function(){
                            // 키 입력마다 검사하기
                            var v = $.trim($(this).val());
                            if($(this).data('last') == v) return false;
                            var o = $('#checker').html('').removeClass('unavailable');
                            if(!check_reg(v)){
                                    if(v!='')
                                            $(this).focus();
                                    return false;
                            }
                            o.html('<img src="/images/block.gif"/>');
                            clearTimeout(timeout);
                            timeout = setTimeout(function(){
                                    autoCheck();
                            },800);
                    });
                });
                 
			</script>
        </div>