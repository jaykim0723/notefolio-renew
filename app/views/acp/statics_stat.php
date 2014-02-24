<h2>현황</h2>
<?php echo $subtab?>
<?php
$year = array();
foreach(range(2012, date('Y')) as $v)
  $year[$v] = $v;

$month = array();
foreach(range(1, 12) as $v){
  if($v<10) $v = '0'.$v;
  $month[$v] = $v;
}
?>
<script type="text/javascript">
    $(function() {
      $('.from-year, .from-month, .to-year, .to-month').on('change', function(){
        drawChart('overview');
        drawChart('work');
      });
      var rank = ['upload-user','view-user'];
      for(var i in rank) {
        $('#'+rank[i]+'-rank-field').on('change', function(){
          var key = $(this).attr('id').replace('-rank-field', '');
          var val = $(this).val();
          drawRankData(key, val);
        });
        drawRankData(rank[i], $('#'+rank[i]+'-rank-field').val());
      }

      drawChart('overview');
      drawChart('work');
      drawChart('keyword');
      drawChart('stat');
    });  

    function drawChart(mode){

      switch(mode){
        case "overview":
          $.post('/acp/statics/stat_data/overview',
                {
                    date_from:$('.from-year').val()+'-'+$('.from-month').val(),
                    date_to:$('.to-year').val()+'-'+$('.to-month').val(),
                })
            .done(function(data){
              var json = eval('('+data+')');

              var countData = ['total-user', 'total-user-uploaded', 'total-works'];
              for (var i in countData){
                $('span.count', '#'+countData[i]).text(eval('json.'+countData[i].replace(/(\-[a-z])/g, function($1){return $1.toUpperCase().replace('-','');})));
              }
          }); 
        break;
        case "work":
          $.post('/acp/statics/stat_data/work',
                {
                    date_from:$('.from-year').val()+'-'+$('.from-month').val(),
                    date_to:$('.to-year').val()+'-'+$('.to-month').val(),
                })
            .done(function(data){
              var json = eval('('+data+')');

              var countData = ['total-view-count', 'total-note-count', 'total-comment-count'];
              for (var i in countData){
                $('span.count', '#'+countData[i]).text(eval('json.'+countData[i].replace(/(\-[a-z])/g, function($1){return $1.toUpperCase().replace('-','');})));
              }

              var chartName = ['workViewMonthCount','workNoteMonthCount','workCommentMonthCount'];
              for(var i in chartName){
                eval('chart.stat.'+chartName[i]+'(json.'+chartName[i]+');');
              }
          }); 
        break;
        case "keyword":
          $.post('/acp/statics/stat_data/keyword')
            .done(function(data){
              var json = eval('('+data+')');
              var chartName = ['workKeywordUsage','userKeywordUsage'];
              for(var i in chartName){
                eval('for(var j in json.'+chartName[i]+'){ var temp = Number(json.'+chartName[i]+'[j][1]); if(!isNaN(temp)) json.'+chartName[i]+'[j][1] = temp; }');
                eval('chart.stat.'+chartName[i]+'(json.'+chartName[i]+');');
              }
              drawRankData('work-keyword');
              drawRankData('user-keyword');
          });    
        break;
        case "stat":
          $.post('/acp/statics/stat_data/stat')
            .done(function(data){
              var json = eval('('+data+')');
              var chartName = ['firstUpload','secondUpload'];
              for(var i in chartName){
                eval('chart.stat.'+chartName[i]+'(json.'+chartName[i]+', json.'+chartName[i]+'Avg);');
              }
              var chartName = ['workPerUser','totalGenderAge'];
              for(var i in chartName){
                eval('chart.stat.'+chartName[i]+'(json.'+chartName[i]+');');
              }
          });    
        break;
        default:
        break;
      }   
    }

    function drawRankData(mode, val){
      $('table > tbody', '#'+mode+'-rank').empty().append('<tr><td colspan="5">Loading...<img src="/images/loading.gif" alt="loading..." /></td></tr>');
      switch(mode){
        case "upload-user":
          var countView = $('span.count', '#'+mode+'-rank').text('0');
          if(isNaN(val)){
            $(countView).text('NaN');
            $('table > tbody', '#'+mode+'-rank').empty().append('<tr><td colspan="5">Not Number...</td></tr>');
          }
          else {
            $.post('/acp/statics/rank_data/'+mode.replace('-','_'), {count:val})
              .done(function(data){
                var json = eval('('+data+')');
                var appendTo = $('table > tbody', '#'+mode+'-rank').empty();

                $(countView).text(json.count);
                for(i in json.data)
                $(
                  '<tr>'+
                    '<td>'+json.data[i].id+'</td>'+
                    '<td><a href="/acp/user/member/id/'+json.data[i].id+'">'+json.data[i].username+'</a></td>'+
                    '<td><a href="/acp/user/member/id/'+json.data[i].id+'">'+json.data[i].realname+'</a></td>'+
                    '<td>'+json.data[i].regdate+'</td>'+
                    '<td>'+json.data[i].count+'</td>'+
                  '</tr>'
                  ).appendTo(appendTo);
            });  
          }
        break;
        case "view-user":
          var countView = $('span.count', '#'+mode+'-rank').text('0');
          if(isNaN(val)){
            $(countView).text('NaN');
            $('table > tbody', '#'+mode+'-rank').empty().append('<tr><td colspan="5">Not Number...</td></tr>');
          }
          else {
            $.post('/acp/statics/rank_data/'+mode.replace('-','_'), {count:val})
              .done(function(data){
                var json = eval('('+data+')');
                var appendTo = $('table > tbody', '#'+mode+'-rank').empty();

                $(countView).text(json.count);
                for(i in json.data)
                $(
                  '<tr>'+
                    '<td>'+json.data[i].id+'</td>'+
                    '<td><a href="/gallery/'+json.data[i].id+'">'+json.data[i].title+'</a></td>'+
                    '<td><a href="/acp/user/member/id/'+json.data[i].user_id+'">'+json.data[i].realname+'</a></td>'+
                    '<td>'+json.data[i].regdate+'</td>'+
                    '<td>'+json.data[i].count+'</td>'+
                  '</tr>'
                  ).appendTo(appendTo);
            });  
          }
        break;
        case "work-keyword":
        case "user-keyword":
          $.post('/acp/statics/rank_data/'+mode.replace('-','_'))
            .done(function(data){
              var json = eval('('+data+')');
              var appendTo = $('table > tbody', '#'+mode+'-rank').empty();

              for(i in json.data)
              $(
                '<tr>'+
                  '<td>'+json.data[i].name+'</td>'+
                  '<td>'+json.data[i].count+'</td>'+
                  '<td>'+json.data[i].percent+'</td>'+
                '</tr>'
                ).appendTo(appendTo);
          });  
        break;
        default:
        break;
      }   
    }
</script>
<div class="container-fluid">
  <div class="row-fluid">
    <div class="control">
      범위 설정:
      <?php 
        echo form_dropdown('from_year', $year, '2012', "class='from-year' style='width: 6em;'"); 
        echo form_dropdown('from_month', $month, '10', "class='from-month' style='width: 4em;'"); 
      ?>
      -
      <?php
        echo form_dropdown('to_year', $year, date('Y'), "class='to-year' style='width: 6em;'"); 
        echo form_dropdown('to_month', $month, date('m'), "class='to-month' style='width: 4em;'");
      ?>
    </div>
    <ul id="site-overview" class="unstyled">
      <li id="total-user">
        <p class="lead">총 회원 수: <span class="count">NaN</span>명</p>
      </li>
      <li id="total-user-uploaded">
        <p class="lead">총 작품 올린 회원 수: <span class="count">NaN</span>명</p>
      </li>
      <li id="total-works">
        <p class="lead">총 작품 수: <span class="count">NaN</span>개</p>
      </li>
      <li id="total-view-count">
        <p class="lead">총 조회 수: <span class="count">NaN</span>개</p>
        <div id="total-view-by-month-count">
          <h4>월별 총 조회 수, 평균 조회 수</h4>
        </div>
      </li>
      <li id="total-note-count">
        <p class="lead">총 노트 수: <span class="count">NaN</span>개</p>
        <div id="total-note-by-month-count">
          <h4>월별 총 노트 수, 평균 노트 수</h4>
        </div>
      </li>
      <li id="total-comment-count">
        <p class="lead">총 댓글 수: <span class="count">NaN</span>개</p>
        <div id="total-comment-by-month-count">
          <h4>월별 총 댓글 수, 평균 댓글 수</h4>
        </div>
      </li>
    </ul>
  </div>
  <div class="row-fluid">
    <div id="upload-user-rank">
      <p>
        <?php
          $numField = array(
              'name'  => 'upload_user_rank',
              'id'    => 'upload-user-rank-field',
              'placeholder'=> "User...",
              'value' => 50,
              'style' => 'width:4em;',
          );
          echo form_input($numField);
        ?> 작품 이상 업로드 한 회원 수: <span class="count"></span>명</p>
      <table class="table table-striped table-hover table-condensed">
        <thead>
          <tr>
            <th>ID</th>
            <th>사용자</th>
            <th>실명</th>
            <th>가입일</th>
            <th>작품수</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>
  <div class="row-fluid">
    <div id="view-user-rank">
      <p>
        <?php
          $numField = array(
              'name'  => 'view_user_rank',
              'id'    => 'view-user-rank-field',
              'placeholder'=> "User...",
              'value' => 2000,
              'style' => 'width:4em;',
          );
          echo form_input($numField);
        ?> 회 이상 조회된 작품 수: <span class="count"></span>개</p>
      <table class="table table-striped table-hover table-condensed">
        <thead>
          <tr>
            <th>ID</th>
            <th>작품명</th>
            <th>작성자</th>
            <th>게시일</th>
            <th>조회수</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>
  <div class="row-fluid">
    <h4>작품 키워드 활용 현황</h4>
    <div id="work-keyword-usage" class="span6">
      
    </div>
    <div id="work-keyword-rank" class="span5">
      <table class="table table-striped table-hover table-condensed">
        <thead>
          <tr>
            <th>키워드</th>
            <th>빈도수</th>
            <th>비율</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>
  <div class="row-fluid">
    <h4>사용자 키워드 활용 현황</h4>
    <div id="user-keyword-usage" class="span6">
      
    </div>
    <div id="user-keyword-rank" class="span5">
      <table class="table table-striped table-hover table-condensed">
        <thead>
          <tr>
            <th>키워드</th>
            <th>빈도수</th>
            <th>비율</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>
  <div class="row-fluid">
    <div id="first-upload">
      <h4>첫 작품 업로드까지 걸리는 기간</h4>
    </div>
  </div>
  <div class="row-fluid">
    <div id="second-upload">
      <h4>첫 작품 업로드 후 다음 업로드까지 걸리는 기간</h4>
    </div>
  </div>
  <div class="row-fluid">
    <div id="work-per-user">
      <h4>1인당 올리는 작품 수</h4>
    </div>
  </div>
  <div class="row-fluid">
    <div id="total-gender-age">
      <h4>토탈 성비 연령비</h4>
    </div>
  </div>
</div>