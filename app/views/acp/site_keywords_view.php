<?php 
$keyword = array(
    'name'  => 'keyword',
    'id'    => 'keyword',
    'value' => $default_keyword,
    'placeholder'   => "keyword",
    'style' =>  "width: 100%; height: 20em;"
);
$j_target_url = array(
    'name'        => 'j_target_url',
    'id'          => 'j_target_url',
    'value'       => '',
    'style'       => 'width:30%',
    'placeholder'   => "Enter Target URL",
);
$j_img_url = array(
    'name'        => 'j_img_url',
    'id'          => 'j_img_url',
    'value'       => '',
    'style'       => 'width:30%',
    'placeholder'   => "Enter Image URL",
);
$j_insert_button = array(
    'name' => 'j_insert',
    'id' => 'j_insert',
    'class' => 'btn',
    'value' => '삽입',
    'type' => 'j_insert',
    'content' => '삽입'
);

?>
    <h2>키워드</h2>
    <div class="info">
      <p>키워드를 추가 및 삭제할 수 있습니다.</p>
      <p>저장을 누를 때까지 반영하지 않습니다.</p>
    </div>

    <table class="table table-bordered">
      <caption></caption>
      <thead>
        <tr>
          <th>식별기호</th>
          <th>한글출력</th>
          <th>수정/삭제</th>
        </tr>
      </thead>
        <tr id="keyword-insert">
          <td><?=form_input()?></td>
          <td><?=form_input()?></td>
          <td>
            <a href=""><span class="btn btn-success">추가</span></a>
          </td>
        </tr>
      <tbody>
        <?php foreach($keyword_list as $key => $val): ?>
        <tr id="keyword-<?=$key?>">
          <td><?=$key?></td>
          <td><?=$val?></td>
          <td>
            <a href=""><span class="btn btn-primary">수정</span></a>
            <a href=""><span class="btn btn-danger">삭제</span></a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>


<?php echo form_open("acp/site/keyword/save", $form_attr); ?>
    <p>target url:<?php echo form_input($j_target_url);?>
     / img url:<?php echo form_input($j_img_url);?>
     <?php echo form_button($j_insert_button); ?></p>
    <p>for debug:</p>
    <p><?php echo form_textarea($keyword); ?></p>
    <?php if($save_result!='') {?><p class="info">결과: <?php echo $save_result; ?></p><?php }?>
    <button class="btn btn-large btn-primary" type="submit">전송</button>
<?php echo form_close(); ?>
    <script type="text/javascript">
        var bannerUtil = {
            make: function(target, image){
                var origData = JSON.parse($('#<?=$keyword['id']?>').val());
                var newData = [{ target: target, image: image }];
                newData = newData.concat(origData);
                
                $('#<?=$keyword['id']?>').val(JSON.stringify(newData).replace('[','[\n').replace(']','\n]').replace(/{/gi,'    {').replace(/},/gi,'},\n'));
                
            }
            
        }
        
        $(function(){
            $('#<?=$j_insert_button['id']?>').on('click', function(e){
                e.preventDefault(); // cancel default behavior
                var target = $('#<?=$j_target_url['id']?>').val();
                var image = $('#<?=$j_img_url['id']?>').val();
                bannerUtil.make(target, image);
            });
        });
    </script>