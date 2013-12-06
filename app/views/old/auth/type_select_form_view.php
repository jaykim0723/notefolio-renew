
        <br>
        <br>

<?php
if(isset($fb_num_id)){
?>
        <img src=
        "/images/signup/facebook_signup.png" width="288" height="34" rel="tooltip" data-original-title=
        "페이스북 사용자인 경우 더욱 간소하게 가입할 수 있습니다.">
        
        <p style="padding: 40px 30px 30px 30px;"></p>
        
        <div class="alert alert-info">
            <p>지금 Facebook 계정으로 가입하고 있습니다.</p>
            <p>입력된 정보는 Facebook에서 제공한 정보를 이용하고 있습니다.</p>
        </div>
        <div id="prefered-choice-data" style="display:none;">
            <input type="hidden" name="email" data-last="" id="email" class="legend_email" value="<?=$email?>" placeholder="이메일 주소를 입력해주세요">
            <input type="hidden" name="password" class="legend_pw" value="<?=$password?>" placeholder="비밀번호를 입력해주세요" minlength="3" maxlength="50">
            <input type="hidden" name="confirm_password" class="legend_pw" value="<?=$password?>" placeholder="비밀번호를 다시 입력해주세요" minlength="3" maxlength="50">
        </div>
<?            
} else {
?>  
        <a href="#" id="register_with_facebook"><img src=
        "/images/signup/facebook_signup.png" width="288" height="34" rel="tooltip" data-original-title=
        "페이스북 사용자인 경우 더욱 간소하게 가입할 수 있습니다."></a>

        <p style="padding: 40px 30px 30px 30px;"><img src="/images/signup/or.png"></p>

        <div id="register_with_email" class="regi_email">
            이메일로 가입하기
        </div>

        <div class="row-fluid">
            <div class="row-fluid">
                <div class="control-group">
                    <input type="text" name="email" data-last="" id="email" class="legend_email auth_tooltip" value="<?=$email?>" placeholder="이메일 주소를 입력해주세요" title="이메일 주소를 입력해주세요."> 
                    <div class="auth_checkbox">
                    	<span class="check" id="email_checker" rel="tooltip"></span>
                    </div>
                </div>
            </div>
            <div class="row-fluid">
                <div class="control-group">
                    <input type="password" name="password" class="legend_pw auth_tooltip" value="<?=$password?>" placeholder=
                    "비밀번호를 입력해주세요" minlength="3" maxlength="50" title="비밀번호를 입력해주세요.">
                </div>

                <div class="control-group">
                    <input type="password" name="confirm_password" class="legend_pw auth_tooltip" value="<?=$password?>" placeholder=
                    "비밀번호를 다시 입력해주세요" minlength="3" maxlength="50" title="비밀번호를 다시 입력해주세요.">
                </div>
            </div>
        </div>
<?php
}
?>        
        <script>
            var timeout = '';
            var email_checked = false;
            
            var check_reg_email = function(v){
                    return /^[_a-zA-Z0-9\-\.\+]+@[\.\_a-zA-Z0-9\-\+]+\.[a-zA-Z]+$/.test(v); // 정규식 검사하기.      
            };
            
            var autoCheckEmail = function(){
                    var o = $('#email');
                    $.get('/auth/check_email_available', { email : o.val() }, function(d){
                            o = o.data('last', o.val()).next();
                            if(d=='y'){
                                o.html('<img src="/images/signup/check.png">').removeClass('unavailable');
                                email_checked = true;
                            }else{
                                o.html('<img src="/images/signup/x.png">').addClass('unavailable');
                                email_checked = false;
                            }
                    });
            };
            
            $(function() {
                $('#register_with_facebook').on('click',function(e){
                    e.preventDefault();
                    var fb_diag = window.open('<?=$this->config->item('base_url')?>auth/fb/link/for-register','fb_diag','width=600,height=300,scrollbars=yes,resizable=no');
                    fb_diag.focus();
                    //$.fn.dialog2.helpers.alert("현재 준비중입니다.");
                });
                
                $('#email').on('change keyup input focusout',function(){
                        // 키 입력마다 검사하기
                        var v = $.trim($(this).val());
                        if($(this).data('last') == v) return false;
                        var o = $('#email_checker').html('').removeClass('unavailable');
                        if(!check_reg_email(v)){
                                if(v!='')
                                        $(this).focus();
                                return false;
                        }
                        o.html('<img src="/images/block.gif"/>');
                        clearTimeout(timeout);
                        timeout = setTimeout(function(){
                                autoCheckEmail();
                        },800);
                });  
            });
            
             $(function() {
                    $(".auth_tooltip").tooltip({placement : 'right'});
                });
<?php
if(isset($fb_num_id)){
?>
            $(function() {
                autoCheckEmail();
                register.nav.goStep(1,true);
            });
<?            
}
?>            
        </script>