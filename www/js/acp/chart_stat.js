/*
 * chart library js for Notefolio ACP
 * stat chart luncher
 * 
 * (c) 2013 Notefolio/ Developed by Yoon, Seongsu.
 */

// if you want to use, load chart.js first.

chart.stat =  {
  workViewMonthCount: function(data){
    var opt = {
      title: '월별 총 조회 수, 평균 조회 수',
      hAxis: {title: '월', titleTextStyle: {color: '#333333'}, showTextEvery: 2},
      vAxis: {title: '조회수만 1/100'},
      data:data,
      chartArea: {left:60, width: "70%", height: "70%"},
      height:300,
      target:"#total-view-by-month-count",
      type:"LineChart"
    };

    chart.remove(opt.target);
    chart.draw(opt);
  },
  workNoteMonthCount: function(data){
    var opt = {
      title: '월별 총 추천 수, 평균 추천 수',
      hAxis: {title: '월', titleTextStyle: {color: '#333333'}, showTextEvery: 2},
      data:data,
      chartArea: {left:60, width: "70%", height: "70%"},
      height:300,
      target:"#total-note-by-month-count",
      type:"LineChart"
    };

    chart.remove(opt.target);
    chart.draw(opt);
  },
  workCommentMonthCount: function(data){
    var opt = {
      title: '월별 총 댓글 수, 평균 댓글 수',
      hAxis: {title: '월', titleTextStyle: {color: '#333333'}, showTextEvery: 2},
      data:data,
      chartArea: {left:60, width: "70%", height: "70%"},
      height:300,
      target:"#total-comment-by-month-count",
      type:"LineChart"
    };

    chart.remove(opt.target);
    chart.draw(opt);
  },

  //--

  workKeywordUsage: function(data){
    var opt = {
      title: '작품 키워드 활용 현황',
      data:data,
      chartArea:{left:0,top:20,width:"100%",height:"500"},
      sliceVisibilityThreshold:0,
      target:"#work-keyword-usage",
      type:"PieChart"
    };

    chart.remove(opt.target);
    chart.draw(opt);
  },
  userKeywordUsage: function(data){
    var opt = {
      title: '사용자 키워드 활용 현황',
      data:data,
      chartArea:{left:0,top:20,width:"100%",height:"500"},
      sliceVisibilityThreshold:0,
      target:"#user-keyword-usage",
      type:"PieChart"
    };

    chart.remove(opt.target);
    chart.draw(opt);
  },

  //--

  firstUpload: function(data, avg){
    var opt = {
      title: '첫 작품 업로드까지 걸리는 기간',
      hAxis: {title: '기간', titleTextStyle: {color: '#333333'}, showTextEvery: 1},
      data:data,
      target:"#first-upload",
      type:"ColumnChart"
    };

    $('.avg-text', opt.target).remove();
    chart.remove(opt.target);
    chart.draw(opt);
    $('<div></div>').addClass('.avg').html('평균 <span class="count">'+avg+'</span> 일').appendTo(opt.target);
  },
  secondUpload: function(data, avg){
    var opt = {
      title: '첫 작품 업로드 후 다음 업로드까지 걸리는 기간',
      hAxis: {title: '기간', titleTextStyle: {color: '#333333'}, showTextEvery: 1},
      data:data,
      target:"#second-upload",
      type:"ColumnChart"
    };

    $('.avg-text', opt.target).remove();
    chart.remove(opt.target);
    chart.draw(opt);
    $('<div></div>').addClass('.avg').html('평균 <span class="count">'+avg+'</span> 일').appendTo(opt.target);
  },
  workPerUser: function(data){
    var opt = {
      title: '1인당 올리는 작품 수',
      hAxis: {title: '작품 수', titleTextStyle: {color: '#333333'}, showTextEvery: 1},
      data:data,
      target:"#work-per-user",
      type:"ColumnChart"
    };

    chart.remove(opt.target);
    chart.draw(opt);
  },
  totalGenderAge: function(data){
    var opt = {
      title: '토탈 성비 연령비',
      hAxis: {title: '연령', titleTextStyle: {color: '#333333'}},
      vAxis: {title: '비율(%)'},
      data:data,
      target:"#total-gender-age",
      type:"ColumnChart"
    };

    chart.remove(opt.target);
    chart.draw(opt);
  }
};