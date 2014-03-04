/*
 * chart library js for Notefolio ACP
 * work chart luncher
 * 
 * (c) 2013 Notefolio/ Developed by Yoon, Seongsu.
 */

// if you want to use, load chart.js first.

chart.work =  {
  workViewCount: function(data){
    var opt = {
      title: '날짜별 조회수',
      hAxis: {title: '날짜', titleTextStyle: {color: '#333333'}, showTextEvery: parseInt(data.length/7)},
      vAxis: {title: '모든 작품만 1/10'},
      height: 800,
      data:data,
      target:"#work-view-count",
      type:"LineChart"
    };

    chart.remove(opt.target);
    chart.draw(opt);
  },
  workNoteCount: function(data){
    var opt = {
      title: '날짜별 추천수',
      hAxis: {title: '날짜', titleTextStyle: {color: '#333333'}, showTextEvery: parseInt(data.length/7)},
      vAxis: {title: '모든 작품만 1/10'},
      height: 800,
      data:data,
      target:"#work-note-count",
      type:"LineChart"
    };

    chart.remove(opt.target);
    chart.draw(opt);
  },
  workCommentCount: function(data){
    var opt = {
      title: '날짜별 댓글수',
      hAxis: {title: '날짜', titleTextStyle: {color: '#333333'}, showTextEvery: parseInt(data.length/7)},
      vAxis: {title: '모든 작품만 1/10'},
      height: 800,
      data:data,
      target:"#work-comment-count",
      type:"LineChart"
    };

    chart.remove(opt.target);
    chart.draw(opt);
  },
  workUploadUserWork: function(data){
    var opt = {
      title: '날짜별 작품 올린 사람 수, 작품 올라온 수',
      hAxis: {title: '날짜', titleTextStyle: {color: '#333333'}, showTextEvery: parseInt(data.length/7)},
      data:data,
      target:"#work-upload-user-work",
      type:"ColumnChart"
    };

    chart.remove(opt.target);
    chart.draw(opt);
  }
};