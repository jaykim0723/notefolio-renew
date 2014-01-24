<?php 
$keyword = array(
    'name'  => 'keyword',
    'id'    => 'keyword',
    'value' => $default_keyword,
    'placeholder'   => "keyword",
    'style' =>  "width: 100%; height: 5em;"
);

?>
<div class="main">
  <div class="main-inner">
      <div class="container">


    <h2>키워드</h2>
    <div class="info">
      <p>키워드를 추가 및 삭제할 수 있습니다.</p>
      <p>저장을 누를 때까지 반영하지 않습니다.</p>
    </div>

    <table class="table table-bordered" id="keyword-list">
      <caption></caption>
      <thead>
        <tr>
          <th>식별기호</th>
          <th>한글출력</th>
          <th>수정/삭제</th>
        </tr>
      </thead>
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
      <tbody>
        <?php foreach($keyword_list as $key => $val): ?>
        <tr id="keyword-<?=$key?>">
          <td><?=$key?></td>
          <td><?=$val?></td>
          <td>
            <a href="javascript:keywordUtil.update('<?=$key?>')">
              <span class="btn btn-primary">수정</span>
            </a>
            <a href="">
              <span class="btn btn-danger">삭제</span>
            </a>
            <?=form_hidden(array('keyword[]'=>json_encode(array($key=>$val))))?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

<?php echo form_open("acp/site/keyword/save", $form_attr); ?>
    <p>for debug:</p>
    <p><?php echo form_textarea($keyword); ?></p>
    <?php if($save_result!='') {?><p class="info">결과: <?php echo $save_result; ?></p><?php }?>
    <button class="btn btn-large btn-primary" type="submit">전송</button>
<?php echo form_close(); ?>

    <script type="text/javascript" src="/js/libs/json2.js"></script>
    <script type="text/javascript" src="/js/acp/util.js"></script>
    <script type="text/javascript">       
    </script>




  
      </div> <!-- /container -->
  </div> <!-- /main-inner -->
</div>