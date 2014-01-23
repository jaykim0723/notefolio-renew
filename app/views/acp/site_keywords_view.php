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
<?php echo form_open("acp/site/keyword/save", $form_attr); ?>
    <h2>메인배너</h2>
    <div class="info"><p>메인에 띄울 배너를 설정할 수 있습니다. </p></div>
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

    <table class="table table-bordered">
      <caption></caption>
      <thead>
        <tr>
          <th>key</th>
          <th>val</th>
          <th>수정/삭제</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($keyword_list as $key => $val): ?>
        <tr>
          <td><?=$key?></td>
          <td><?=$val?></td>
          <td><?=$key?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>