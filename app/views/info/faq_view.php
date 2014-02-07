<?php
/*


#### FAQ 등록하는 방법

아래의 질답 블럭을 복사하고, data-key="" 부문한 대표 변수명으로 중복되지 않게 지정하고, 그냥 #faq_list 안에 붙여넣으면 된다.

<h2 class='h4 question' data-key="">
</h2>
<div class='answer'>
</div>


#### 사이드바에 등록하는 방법
위의 h2 class에 sidebar를 추가하면 된다. 자동으로 사이드바에 추가된다. 아래처럼..
<h2 class='h4 question sidebar' data-key="register">
.. 이하 생략 ...

*/


?>
<section id="faq_container">
    <div class="container">
        <div class="row">
            <div id="content" class="col-md-9">

                <div id='cont_gallery' class='span10'>

                    <h1>FAQ</h1>

                    <input type='search' id='faq_search' placeholder="이곳에 검색어를 입력하세요." class='form-control'/>
                    <script>
                        $(function(){
                            $('#faq_search').keyup(function(){
                                var q = $(this).val().toLowerCase();
                                var list = $('#faq_list');
                                if(q.length == 0){
                                    list.children('.question').removeClass('opened').show();
                                    return false;
                                }
                                list.children().hide();
                                $.each(data, function(k,v){
                                    if(v.indexOf(q) > -1){
                                        list.children('.question[data-key='+k+']').removeClass('opened').show();
                                    }
                                });
                            });
                        });
                    </script>
    
    
                    <div id='faq_list'>

                        <h2 class='h4 question' data-key="notefolio">
                        노트폴리오는 무엇인가요? 
                        </h2>
                        <div class='answer'>
                        노트폴리오는 다양한 분야의 아티스트와 디자이너가 서로의 작품을 공개하고 함께 이야기하는 문화예술 커뮤니티입니다. 보다 자세한 내용은 <a href="/info/about_us">about us</a>를 참조해주세요!
                        </div>


                        <h2 class='h4 question sidebar' data-key="type">
                        노트폴리오의 메인 작품 선정 기준은 어떻게 되나요?
                        </h2>
                        <div class='answer'>
                        메인 페이지는 매주 월/목요일마다 바뀌게 되며, 이를 선정하는 데에는 여러 기준이 있습니다. 먼저 분야의 다양성을 고려합니다. 노트폴리오에는 파인아트, 디자인, 일러스트레이션, 영상, 사진 등 다양한 분야의 작품들이 올라오기 때문에, 각 분야의 형평성을 맞추기 위해 여러 노력을 하고 있습니다. 
                        <br/><br/>
                        또한 정성들인 작품 소개가 중요한 선정 요소가 됩니다. 작품 외에도 작품을 만드는 과정 이미지, 다른 각도에서 바라 본 작품 이미지, 작품 설명 및 의도를 담은 텍스트 등 업로드 과정 중 사람들이 자신의 작품을 조금 더 이해하기 쉽도록 배려해주시면 메인 선정에 큰 도움이 됩니다. 페이스북 페이지(facebook.com/notefolio)에도 동일한 선정 기준을 적용하고 있습니다. 마지막으로, 작품에 대한 조회/추천/댓글 등 사람들의 활발한 피드백 역시 선정에 영향을 끼칠 수 있습니다.
                        </div>

                        <h2 class='h4 question sidebar' data-key="keyword">
                        키워드가 무엇인가요?
                        </h2>
                        <div class='answer'>
                        노트폴리오는 보다 다양한 분야의 아티스트와 디자이너가 함께 이야기할 수 있는 공간이 되기 위해 별도의 분류를 두지 않습니다. 대신 각각의 분야를 대표하는 키워드를 나열하고, 그 중 최대 3가지를 자유롭게 선택하여 본인과 본인의 작품을 표현할 수 있도록 하였습니다. 모든 사람들은 키워드의 여러 조합을 통해 그에 해당하는 다양한 작품과 아티스트, 디자이너를 만나볼 수 있습니다.
                        </div>


                        <h2 class='h4 question sidebar' data-key="ccl">
                        내 작품의 저작권은 어떻게 보호되나요?
                        </h2>
                        <div class='answer'>
                        노트폴리오는 CCL(Creative commons License)을 통해 회원들의 창작물을 보호하고 있습니다.  CCL은 자기 창작물에 대한 저작권의 범위를 본인이 직접 설정할 수 있는 라이선스(License)입니다.
                        <br/><br/>
                        지적재산권, 온라인 법률, 웹 저술, 컴퓨터 과학의 전문가들이 함께 머리를 맞대고 만든 CC (Creative Commons)는 발효 이후 온라인 상 콘텐츠의 저작권을 보호하는데 큰 힘을 발휘했습니다. 지금은 CCL로 발전하여 전 세계 50여개 국에서 도입, 활용 중인 저작권 보호 규약입니다.
                        <br/><br/>
                        저희 노트폴리오는 CCL에 의거하여 다음과 같은 6가지 라이선스를 지원합니다. (출처 : <a href="http://cckorea.org/xe/?mid=ccl" target="_blank">http://cckorea.org/xe/?mid=ccl</a> )
                        <br/><br/>
                        <img src='/img/info/ccl_license.png'/>
                        </div>


                        <h2 class='h4 question' data-key="credits">
                        작품 업로드 시, Credits 메뉴는 무엇인가요?
                        </h2>
                        <div class='answer'>
                        본인의 창작물이 공동 프로젝트일 경우 작품 업로드 과정 중 Credits 메뉴를 통해 함께 작업한 사람들을 등재할 수 있습니다. 이 때 등재한 공동 작업자에게는 이메일을 통해 알림 메일을 드리고 있으며, 공동작업자가 노트폴리오 회원인 경우 직접 프로필로 이동이 가능합니다.
                        </div>


                        <h2 class='h4 question' data-key="follow">
                        Follow는 어디에 쓰이는 기능인가요?
                        </h2>
                        <div class='answer'>
                        본인의 마음에 드는 회원의 활동 및 최근 소식을 정기적으로 받아볼 수 있는 구독기능입니다. 구독한 회원에 대한 소식은 Feed에서 확인하실 수 있습니다.
                        </div>

                            

                        <h2 class='h4 question' data-key="upload">
                            영상을 함께 올리려면 어떻게 하나요?
                        </h2>
                        <div class='answer'>
                            노트폴리오는 현재 YouTube와 Vimeo에 업로드된 영상 재생을 지원합니다. 해당 사이트에서 공유 링크를 복사, 붙여 넣으면 노트폴리오 사이트 내에서도 정상 재생이 가능합니다. 자세한 방법은 다음과 같습니다.
                            
                            <h3>① Youtube</h3>
                            <div><img src="/img/info/faq/youtube1.png"/></div>
                            공유(share)버튼을 클릭합니다.
                            <br/><br/>
                            <div><img src="/img/info/faq/youtube2.png"/></div>
                            소스코드를 클릭하면 embed소스가 생성됩니다. 이것을 복사하여 노트폴리오의 동영상 소스 입력기에 붙여 넣으면 됩니다.
                            
                            <h3>② Vimeo</h3>
                            <div><img src="/img/info/faq/vimeo1.png"/></div>
                            동영상에 마우스 버튼을 오버하여 share버튼을 클릭합니다.
                            <br/><br/>
                            <div><img src="/img/info/faq/vimeo2.png"/></div>
                            생성된 embed소스를 복사하여 노트폴리오의 동영상 소스 입력기에 붙여 넣으면 됩니다.    
                        </div>


                        <h2 class='h4 question sidebar' data-key="creators">
                            사이트 하단에 링크되어 있는 페이스북 그룹 note. creators 는 무엇인가요?
                        </h2>
                        <div class='answer'>
                            크리에이티브 네트워크 notefolio.net을 더 나은 공간으로 만드는 사람들의 모임입니다.
                           <br/><br/>
                            웹사이트 상 '공지사항'이나 '문의 접수' 등 딱딱한 느낌을 주는 메뉴로 만나기보다는 서로 이름과 얼굴을 마주 보며 좀 더 진실한 이야기를 나누고자 개설되었습니다. 노트폴리오닷넷에 가입해주신 회원분들께 개발 진행 상황 및 문제점, 향후 운영 방향들을 솔직하게 말씀드리고, 회원분들의 문의사항, 의견, 제안들을 정성껏 경청하는 것이 목적입니다. 
                           <br/> 
                            그룹은 현재 비공개로 운영 중이며, 노트폴리오에 속한 팀원 모두가 회원분들과 직접 함께 이야기하고 있습니다.
                        </div>

                        <h2 class='h4 question sidebar' data-key="discoverbility">
                            Discoverability는 무엇인가요?
                        </h2>
                        <div class='answer'>
                            Discoverability는 업로드한 포트폴리오의 내용 및 설명이 얼마나 구체적인지를 시각적으로 보여주는 그래프로, 
                            작품 카테고리, 작품 이미지(또는 동영상) 업로드 수, 작품 관련 텍스트, 태그 등이 영향을 끼칩니다.
                            노트폴리오는 작품의 장르가 다양하여 별도의 부연 설명 없이는 간혹 작가의 작품관이나 의도를 바로 이해하기 어려운 경우가 있습니다.
                            때문에 회원분들이 서로의 작품을 보다 깊게 이해하고 감상하실 수 있도록 Discoverability 기능을 구현하였습니다. 
                            그래프가 높으면 작품이 메인 페이지에 등재될 확률이 높아지고, 검색 결과에도 먼저 나타나게 되니 이 점 참고해주세요!
                        </div>



                                    
                        </div> <!-- end of faq_list -->
                        <script>
                            var data = {};
                            var s = '';
                            $('#faq_list > *').each(function(){
                                s+= $.trim($(this).html().toLowerCase())+'|';
                                if($(this).hasClass('answer')){
                                    data[$(this).prev().data('key')] = s;
                                    s = '';
                                }
                            });
                            $('.question').click(function(){
                                $(this).toggleClass('opened');
                                $(this).next().slideToggle('fast');
                            });
                            var focusTo = function(o){
                                $('#faq_search').val('');
                                var list = $('#faq_list');
                                list.children('.question').removeClass('opened').show();
                                list.children('.answer').hide();
                                $.fn.anchorAnimate(o, 200);
                                o.next().slideDown('fast');
                                return false;           
                            };
                           $(function(){
                                if(location.hash!=''){
                                    // hash가 들어왔다면 해당 질답으로 바로 이동하도록..
                                    focusTo($('#faq_list').children('h2[data-key='+location.hash.replace('#','')+']'));
                                }else{
                                    $('#faq_search').focus();                                    
                                }
                            });                            
                        </script>
                    </div> <!-- end of cont_gallery -->
                </div>
    
                <div class="col-md-3">
                <?php
                    $this->load->view('info/sidebar_view');
                ?>
            </div>
        </div>
    </div>
    <br/><br/><br/>
</section>





