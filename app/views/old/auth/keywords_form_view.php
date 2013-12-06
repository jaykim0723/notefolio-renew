        <div id="auth_keyword">
            <div class="row-fluid">
                <div class="span3 control-group keyword_hint">
                    가장 관심있는 분야를<br>
                    선택해주세요.

                    <div class="keyword_hint_sub">
                        최대 3개까지<br>
                        선택이 가능합니다.
                    </div>
                </div>

                <div class="span12 control-group" id="work_categories_auth">
                <?	$this->load->config('notefolio');
                    foreach($this->config->item('categories') as $k=>$v): ?>
                    <div class='cate_option_auth' data-key="<?php echo $k?>"><?php echo $v?></div>
                <?php endforeach; ?>
                </div><br style="clear:left;">
            </div>
            <input type='hidden' id='keywords' name='categories' value="<?php echo $categories?>"/>
            
            <script>
                // keywords에 관한 폼 검증
                
                $(function(){
                    // binding
                    $('#work_categories_auth > div').on('click', function(){
                        if($(this).hasClass('selected')==false && $('#work_categories_auth > .selected').length == 3){
                            msg.error('최대 3개까지만 선택 가능합니다.');
                            return false;
                        }
                        $(this).toggleClass('selected');
                    });
                    // set values
                    $('#work_categories_auth').children('div[data-key=<?php echo @implode("],div[data-key=", $categories)?>]').addClass('selected');
                });
            </script>
        </div>