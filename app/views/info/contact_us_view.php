<section id="contact_us_container">
    <div class="container">
        <div class="row">
            <div id="content" class="col-md-9 contact-content">

                <h1>Contact Us</h1>

                <p class="lead labeltext">노트폴리오는 격의없는 커뮤니케이션을 지향합니다.</p>
                <p class="labeltext2">
                    제안, 요청, 신고, 문의사항 등이 있다면 언제든 이야기해주세요.<br/>
                        빠른 시일 내에 답변드리겠습니다.<br/>
                    
                </p>
                <br/>
                        
                <?php
                    $this->load->helper('form');
                
                    echo form_open('info/contact_us', array('role'=>'form', 'method'=>'post', 'id'=>'contact_form', 'class'=>'form-horizontal well'));   

                    if(isset($success)): // normal
                ?>
                    <div class='alert alert-success contact-success'>

                        <h2><i class="spi spi-check">check</i><br><br>감사합니다.</h2>
                        
                        정상적으로 접수되었습니다.<br/>
                        확인 후 최대한 빠른 시일 안에 연락 드리겠습니다.
                        <br/><br/>
                    </div>  
                <?php   
                    else:
                    echo validation_errors(); 

                ?>
                    <div class="form-group contact-select">
                        <label for="" class="col-sm-3 control-label">종류</label>
                        <div class="col-sm-9">
                            <select name='type' class="selectpicker">
                                <option value="제안">제안</option>
                                <option value="요청">요청</option>
                                <option value="신고">신고</option>
                                <option value="문의">문의</option>
                                <option value="기타">기타</option>
                            </select>
                            <script>
                                $(function(){
                                    $('#contact_form select').val(['<?php echo $type?>']).focus();
                                });
                            </script>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">이름*</label>
                        <div class="col-sm-9">
                            <input type='text' name='name' value="<?php echo $name?>" required class="form-control"/>        
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">이메일*</label>
                        <div class="col-sm-9">
                            <input type='email' name='email' value="<?php echo $email?>" required class="form-control"/>
                        </div>    
                    </div>
                    
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">연락처</label>
                        <div class="col-sm-9">
                            <input type='text' name='tel' value="<?php echo $tel?>" class="form-control"/>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">내용*</label>
                        <div class="col-sm-9">
                            <textarea class='form-control' style="height:150px;" name='contents' required><?php echo $contents?></textarea>        
                        </div>
                    </div>
                    
                    <div class='form-group'>
                        <div class="col-sm-offset-3 col-sm-9 contact-sumbit">
                            <button type='submit' name='submit' value='1' class='btn btn-pointgreen btn-lg'><i class="spi spi-email_white">email_white</i>Submit</button>
                        </div>
                    </div>
                <?php 
                    endif;
                    echo form_close(); 
                ?>
            </div>
    
            <div class="col-md-3">
                <?php
                    $this->load->view('info/sidebar_view');
                ?>
            </div>
        </div>
    </div>
</section>
