<script type="text/javascript">
	var workSpace = 'register';
                
    $(function() { // 약관 및 개인정보보호정책
        $('#open_term, #open_privacy').on('click', function(e){
            e.preventDefault();
            var page = $(this).attr('id').replace('open_', '');
            $('<div/>').dialog2({
                title: page.replace(page.charAt(0), page.charAt(0).toUpperCase()), 
                content: "/info/"+page, 
                id: "cont"
            });			
        });
    });
    
    var register = {
        nowStep: 0,
        nav: {
            goStep: function(step, is_force){
                if( typeof(is_force) == 'undefined') {
                    var is_force = false;
                }
                
                if(is_force != true && (step == 0 && register.nowStep > 0)){ //type select step
                    $.fn.dialog2.helpers.confirm("처음으로 돌아가시겠습니까? 입력하신 모든 내용은 사라집니다.", {
                        confirm : function(){
                            window.location.reload();
                        }
                    });
                } else if(is_force == true || register.check.step(register.nowStep)){
                    register.nav.reset();
                    
                    switch(step){
                        case 0:
                            $("#register-nav").css("display","none");
                            $("#auth_regitype").css("display","block");
                            break;
                        case 1:
                            $("#register-nav").css("display","block");
                            $("#auth_basic").css("display","block");
                            break;
                        case 2:
                            $("#register-nav").css("display","block");
                            $("#auth_keywords").css("display","block");
                            break;
                        case 3:
                            $("#register-nav").css("display","block");
                            $("#auth_recommend").css("display","block");
                            sRecommend();
                            break;
                    }
                    register.nav.setMenu(step);
                
                    register.nowStep = step;
                }
            },
            setMenu: function(step){ //-- menu set
                if(step>0){
                    $('#register-nav > #nav-step-'+step).children().each(function(){
                        var org_class = $(this).attr('class').split(/\s+/);
                        
                        for(var c in org_class){
                            $(this).addClass(org_class[c]+'_checked');
                        }
                    });
                }
            },
            reset: function(){
                //-- div hide
                $("#auth_regitype").css("display","none");
                $("#register-nav").css("display","none");
                $("#auth_basic").css("display","none");
                $("#auth_keywords").css("display","none");
                $("#auth_recommend").css("display","none");
                //-- end
                
                //-- menu reset
                $('#register-nav').children().children().each(function(){
                    var org_class = $(this).attr('class').split(/\s+/);
                    
                    for(var c in org_class){
                        if(org_class[c].search("_checked")!=-1)
                            $(this).removeClass(org_class[c]);
                    }
                });
                //-- end
            }
        },
        check: {
            step: function(step){
                var rtn = false;
                
                switch(step){
                    case 0:
                        if($('#email').val()==''){                                                                  // 이메일 비었음
                            msg.error('이메일을 입력해 주세요.'); 
                            $('#email').focus();                                                                    // 에러난 필드 포커스
                        } else if(!email_checked){                                                                  // 이메일 검증 오류
                            msg.error('이메일을 사용할 수 없거나 검증 중입니다.<br/>다시 한번 확인해 주세요.');
                            $('#email').focus();                                                                    // 에러난 필드 포커스
                        } else if($('input[name="password"]').val()==''){                                           // 비밀번호 비었음
                            msg.error('비밀번호를 입력해 주세요.');
                            $('input[name="password"]').focus();
                        } else if($('input[name="password"]').val().length<<?=$this->config->item("password_min_length","tank_auth")?>){                                           // 비밀번호 자릿수
                            msg.error('비밀번호를 <?=$this->config->item("password_min_length","tank_auth")?>자 이상 입력해 주세요.');
                            $('input[name="password"]').focus();                                                    // 에러난 필드 포커스
                        } else if($('input[name="confirm_password"]').val()==''){                                   // 비밀번호 확인 비었음
                            msg.error('비밀번호를 이곳에 다시 입력해 주세요.');
                            $('input[name="confirm_password"]').focus();                                            // 에러난 필드 포커스
                        } else if($('input[name="password"]').val()!=$('input[name="confirm_password"]').val()){    // 비밀번호 불일치
                            msg.error('비밀번호를 이곳에 똑같이 입력해 주세요.');
                            $('input[name="confirm_password"]').focus();                                            // 에러난 필드 포커스
                        } else {
                            rtn = true;
                        }
                        break;
                    case 1:
                        if($('input[name="realname"]').val()==''){                                                  // 이름 비었음
                            msg.error('이름을 입력해 주세요.');
                            $('input[name="realname"]').focus();                                                    // 에러난 필드 포커스
                        } else if($('#username').val()==''){                                                        // 개인url 비었음
                            msg.error('개인url을 입력해 주세요.'); 
                            $('#username').focus();                                                                 // 에러난 필드 포커스
                        } else if(!username_checked){                                                               // 개인url 검증 오류
                            msg.error('개인url을 사용할 수 없거나 검증 중입니다.<br/>다시 한번 확인해 주세요.');
                            $('#username').focus();                                                                 // 에러난 필드 포커스
                        } else if($('input[name="term"]').attr('checked')!="checked"){                              // 약관 동의 안함
                            msg.error('약관에 동의하여야만 가입할 수 있습니다.');
                            $('input[name="term"]').focus();                                                        // 에러난 필드 포커스
                        } else {
                            rtn = true;
                        }
                        break;
                    case 2:
                        if($('#work_categories_auth').children('.selected').length == 0){                           // 하나 이상 선택을 했는지 검사를 한다.
                            msg.error('키워드를 하나 이상 선택해 주세요.');
                        } else {
                            var selected = [];
                            $('#work_categories_auth').children('.selected').each(function(){                       // hidden에 값을 배정한다.
                                    selected.push($(this).data('key'));
                            });
                            $('#keywords').val(selected.join(','));
                            rtn = true;
                        }
                        break;
                    case 3:
                        $('#recommend').val(currentFollowing.join(','));
                        rtn = true;
                        break;
                    default:
                        rtn = true;
                        break;
                }
                
                return rtn;
            }
        },
        submit: function(){
            blockObj.block('register_form', '#register_form');
            for (var i=0;i<=3;i++){
                if(!(register.check.step(i))){
                    blockObj.unblock('register_form');
                    register.nav.goStep(i,true);
                    return;
                    break;
                }
            }
            $.post($('#register_form').attr('action'), $('#register_form').serialize())
                .done(register.afterSubmit);
        },
        afterSubmit: function(data){
            blockObj.unblock('register_form');
            /*
            $.fn.dialog2.helpers.confirm(data, {
                confirm : function(){
                    register.submit();
                }
            });
            */
            var response = eval('('+data+')');
            if(response.status=="error"){
                msg.error(response.errmsg);
                register.nav.goStep(response.goStep, true);
            }
            else if(response.status=="success"){
                window.location.href="/"+response.username;
            }
        }
    };
</script>