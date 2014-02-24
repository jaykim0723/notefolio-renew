<?php 

?>
<div class="main">
  <div class="main-inner">
      <div class="container">
        <div class="widget ">
          <div class="widget-header">
            <i class="icon-picture"></i>
            <h3>작품 목록</h3>
          </div>
          <!-- /widget-header -->
          <div class="widget-content">
            <div class="container">
              <p>전체: <?=$all_count?>개</p>
              <p>페이지: <?=$page?>/<?=$all_page?> 페이지</p>
            </div>
            <div class="table-responsive">

              <h2>작품 동향 분석</h2>
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
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript" src="/js/acp/chart.js"></script>
<script type="text/javascript" src="/js/acp/chart_work.js"></script>
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
      $.post('/acp/statics/research_data/work',
             {date_from:$('#date-from').val(),date_to:$('#date-to').val()})
        .done(function(data){
          var json = eval('('+data+')');
          chart.work.workViewCount(json.workViewCount);
          chart.work.workNoteCount(json.workNoteCount);
          chart.work.workCommentCount(json.workCommentCount);
          chart.work.workUploadUserWork(json.workUploadUserWork);
      });    
    }
</script>
<script type="text/javascript">
  $(function(){
  });
</script>
<div class="container-fluid">
  <div class="row-fluid">
<?php echo form_open("acp/statics/research/work", $form_attr); ?>
<?php echo form_hidden($hidden_field); ?>
    <span class="txt-head">기간</span>
    <?php echo form_input($date_from);?>
    <span class="txt-period">-</span>
    <?php echo form_input($date_to);?>
<?php echo form_close(); ?>
  </div>
  <div class="row-fluid">
    <div id="work-view-count" class="span6">
      <h4>날짜별 조회수</h4>
    </div>
    <div id="work-note-count" class="span6">
      <h4>날짜별 노트수</h4>
    </div>
  </div>
  <div class="row-fluid">
    <div id="work-comment-count">
      <h4>날짜별 댓글수</h4>
    </div>
  </div>
  <div class="row-fluid">
    <div id="work-upload-user-work">
      <h4>날짜별 작품 올린 사람 수, 작품 올라온 수</h4>
    </div>
  </div>
</div>
          </div>
        </div>
      </div> <!-- /container -->
  </div> <!-- /main-inner -->
</div>