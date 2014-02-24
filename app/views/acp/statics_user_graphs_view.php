<h2>회원 동향 분석</h2>
<?php echo $subtab?>
<?php
$hidden_field = array(
    'mode'  => isset($mode)?$mode:'user',
);

$date_from = array(
    'name'  => 'date_from',
    'id'    => 'date-from',
    'placeholder'=> "Date From...",
    'value' => isset($date_from)?$date_from:date('Y-m-d', strtotime("-1 week")),
    'data-date-format' => "yy-mm-dd",
);

$date_to = array(
    'name'  => 'date_to',
    'id'    => 'date-to',
    'placeholder'=> "Date To...",
    'value' => isset($date_to)  ?$date_to:date('Y-m-d'),
    'data-date-format' => "yy-mm-dd",
);
?>
<script type="text/javascript">
    $(function() {
        $( "#date-from" )
          .datepicker({dateFormat: 'yy-mm-dd'})
          .on('change', function(){
            drawChart();
          });   
        $( "#date-to"   )
          .datepicker({dateFormat: 'yy-mm-dd'})
          .on('change', function(){
            drawChart();
          });

        drawChart();
    });  

    function drawChart(){
      $.post('/acp/statics/research_data/user',
             {date_from:$('#date-from').val(),date_to:$('#date-to').val()})
        .done(function(data){
          var json = eval('('+data+')');
          chart.user.userJoin(json.userJoin);
          chart.user.userJoinWithFacebook(json.userJoinWithFacebook);
          chart.user.userJustUploadAtJoin(json.userJustUploadAtJoin);
          chart.user.uploadTermGraph(json.uploadTermGraph);
          chart.user.joinGender(json.joinGender);
          chart.user.percentageAge(json.percentageAge);
          chart.user.percentageGenderAge(json.percentageGenderAge);
          chart.user.userActive(json.userActive);
          chart.user.userLastLogin(json.userLastLogin);
      });    
    }
</script>
<script type="text/javascript">
  $(function(){
  });
</script>
<div class="container-fluid">
  <div class="row-fluid">
<?php echo form_open("acp/statics/research/user", $form_attr); ?>
<?php echo form_hidden($hidden_field); ?>
    <span class="txt-head">기간</span>
    <?php echo form_input($date_from);?>
    <span class="txt-period">-</span>
    <?php echo form_input($date_to);?>
<?php echo form_close(); ?>
  </div>
  <div class="row-fluid">
    <div id="user-join" class="span6">
      <h4>날짜별 회원가입수</h4>
    </div>
    <div id="user-join-with-facebook" class="span6">
      <h4>날짜별 페이스북 연동율</h4>
    </div>
  </div>
  <div class="row-fluid">
    <div id="user-just-upload-at-join" class="span6">
      <h4>가입즉시 업로드 비율</h4>
    </div>
    <div id="upload-term-graph" class="span6">
      <h4>최근 일주일, 한달, 토탈 업로드 한 사람 수</h4>
    </div>
  </div>
  <div class="row-fluid">
    <div id="join-gender" class="span6">
      <h4>가입성비</h4>
    </div>
    <div id="percentage-age" class="span6">
      <h4>나이분포</h4>
    </div>
  </div>
  <div class="row-fluid">
    <div id="percentage-gender-age">
      <h4>연령별 남, 여 비율</h4>
    </div>
  </div>
  <div class="row-fluid">
    <div id="user-active">
      <h4>최근 한 달간 로그인 수</h4>
    </div>
  </div>
  <div class="row-fluid">
    <div id="user-last-login">
      <h4>마지막 로그인 현황</h4>
    </div>
  </div>
</div>