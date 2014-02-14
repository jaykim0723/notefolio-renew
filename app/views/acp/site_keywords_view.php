<?php 
$keyword = array(
    'name'  => 'keyword',
    'id'    => 'keyword',
    'value' => $default_keyword,
    'placeholder'   => "keyword",
    'style' =>  "width: 100%; height: 5em;"
);

?><div class="main">
  <div class="main-inner">
    <div class="container">
      <div class="row">
        <div class="widget ">
          <div class="widget-header">
            <i class="icon-user"></i>
            <h3>키워드</h3>
            <div class="alert alert-info">
              <p>키워드를 추가 및 삭제할 수 있습니다.</p>
              <p>저장을 누를 때까지 반영하지 않습니다.</p>
            </div>
          </div>
          <!-- /widget-header -->
          <div class="widget-content">
            <div class="container">
              <p>전체: <?=$all_count?>개</p>
              <p>페이지: <?=$page?>/<?=$all_page?> 페이지</p>
            </div>
            <div class="container">
              <div class="table-responsive">
                <table class="table table-striped table-hover">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>아이디</th>
                      <th>실명</th>
                      <th>이메일</th>
                      <th>가입일</th>
                      <th>
                        <? for ($i=0;$i<11;$i++) { ?>
                        <i class="icon-wrench"></i>
                        <? } ?>
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <? foreach ($rows as $key=>$row) { ?>
                    <tr>
                      <td><a href="/acp/user/member/view/id/<?=$row->id?>"><?=$row->id?></a></td>
                      <td><a href="/acp/user/member/view/id/<?=$row->id?>"><?=$row->username?></a></td>
                      <td><a href="/acp/user/member/view/id/<?=$row->id?>"><?=$row->realname?></a></td>
                      <td><?=$row->email?></td>
                      <td><?=$row->created?></td>
                      <td>
                        <a href="/acp/user/member/view/id/<?=$row->id?>"><span class="btn btn-info">보기</span></a>
                        <a href="/acp/user/member/edit/id/<?=$row->id?>"><span class="btn">수정</span></a>
                        <a href="/acp/user/member/del/id/<?=$row->id?>"><span class="btn btn-danger">삭제</span></a>
                      </td>
                    </tr>
                    <? } ?>
                  </tbody>
                </table>
              </div>
              <!-- /table-responsive -->
              <?=get_paging($params=array('now_page'=>$page, 'last_page'=>$all_page, 'url'=> '/user/member/list'))?>
            </div>
          </div>
          <!-- /widget-content -->
        </div>
      </div>
      
      <!-- /row --> 
    </div>
    <!-- /container --> 
  </div>
  <!-- /main-inner --> 
</div>
<!-- /main -->

    <table class="table table-bordered" id="keyword-list">
      <caption></caption>
      <thead>
        <tr>
          <th>식별기호</th>
          <th>한글출력</th>
          <th>수정/삭제</th>
        </tr>
        <tr id="keyword-insert">
          <td><?=form_input(array(
            'id'=>'keyword-key', 
            'name'=>'keyword-key', 
            'placeholder'=>'key'))?></td>
          <td><?=form_input(array(
            'id'=>'keyword-val', 
            'name'=>'keyword-val', 
            'placeholder'=>'value'))?></td>
          <td>
            <a href="javascript:keywordUtil.insert()">
              <span class="btn btn-success">추가</span>
            </a>
          </td>
        </tr>
      </thead>
      <tbody>
        <?php foreach($keyword_list as $key => $val): ?>
        <tr id="keyword-<?=$key?>">
          <td><?=$key?></td>
          <td><?=$val?></td>
          <td>
            <a href="javascript:keywordUtil.update('<?=$key?>'); return;">
              <span class="btn btn-primary">수정</span>
            </a>
            <a href="javascript:keywordUtil.delete('<?=$key?>'); return;">
              <span class="btn btn-danger">삭제</span>
            </a>
            <input type="hidden" name="keyword" value='<?=json_encode(array($key=>$val))?>' />
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

<?php echo form_open("acp/site/keywords/save", $form_attr); ?>
    <p>for debug:</p>
    <p><?php echo form_textarea($keyword); ?></p>
    <?php if($save_result!='') {?><p class="info">결과: <?php echo $save_result; ?></p><?php }?>
    <button class="btn btn-large btn-primary" type="submit">전송</button>
<?php echo form_close(); ?>


      </div> <!-- /container -->
  </div> <!-- /main-inner -->
</div>