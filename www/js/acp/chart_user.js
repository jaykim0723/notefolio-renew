/*
 * chart library js for Notefolio ACP
 * user chart luncher
 * 
 * (c) 2013 Notefolio/ Developed by Yoon, Seongsu.
 */

// if you want to use, load chart.js first.

chart.user =  {
  userJoin: function(data){
    var opt = {
      title: '날짜별 회원가입수',
      hAxis: {title: '날짜', titleTextStyle: {color: '#333333'}, showTextEvery: parseInt(data.length/7)},
      data:data,
      target:"#user-join",
      type:"ColumnChart"
    };
    
    chart.remove(opt.target);
    chart.draw(opt);
  },
  userJoinWithFacebook: function(data){
    var opt = {
      title: '날짜별 페이스북 연동율',
      hAxis: {title: '날짜', titleTextStyle: {color: '#333333'}, showTextEvery: parseInt(data.length/7)},
      data:data,
      target:"#user-join-with-facebook",
      type:"LineChart"
    };

    chart.remove(opt.target);
    chart.draw(opt);
  },
  userJustUploadAtJoin: function(data){
    var opt = {
      title: '가입즉시 업로드 비율',
      hAxis: {title: '날짜', titleTextStyle: {color: '#333333'}, showTextEvery: parseInt(data.length/7)},
      vAxis: {viewWindowMode: "explicit", viewWindow:{ min: 0 }},
      data:data,
      target:"#user-just-upload-at-join",
      type:"ColumnChart"
    };

    chart.remove(opt.target);
    chart.draw(opt);
  },
  uploadTermGraph: function(data){
    var opt = {
      title: '최근 일주일, 한달, 토탈 업로드 한 사람 수',
      hAxis: {title: '기준일', titleTextStyle: {color: '#333333'}, showTextEvery: parseInt(data.length/7)},
      data:data,
      target:"#upload-term-graph",
      type:"LineChart"
    };

    chart.remove(opt.target);
    chart.draw(opt);
  },
  joinGender: function(data){
    var opt = {
      title: '가입성비',
      hAxis: {title: '기준일', titleTextStyle: {color: '#333333'}, showTextEvery: parseInt(data.length/7)},
      data:data,
      target:"#join-gender",
      type:"LineChart"
    };

    chart.remove(opt.target);
    chart.draw(opt);
  },
  percentageAge: function(data){
    var opt = {
      title: '나이분포',
      hAxis: {title: '연령', titleTextStyle: {color: '#333333'}, showTextEvery: 1},
      data:data,
      target:"#percentage-age",
      type:"ColumnChart"
    };

    chart.remove(opt.target);
    chart.draw(opt);
  },
  percentageGenderAge: function(data){
    var opt = {
      title: '연령별 남, 여 비율',
      hAxis: {title: '날짜', titleTextStyle: {color: '#333333'}, showTextEvery: 1},
      data:data,
      target:"#percentage-gender-age",
      type:"ColumnChart"
    };

    chart.remove(opt.target);
    chart.draw(opt);
  },
  userActive: function(data){
    var opt = {
      title: '최근 한 달간 로그인 수',
      hAxis: {title: '날짜', titleTextStyle: {color: '#333333'}, showTextEvery: 1},
      data:data,
      target:"#user-active",
      type:"ColumnChart"
    };

    chart.remove(opt.target);
    chart.draw(opt);
  },
  userLastLogin: function(data){
    var opt = {
      title: '마지막 로그인 현황',
      hAxis: {title: '날짜', titleTextStyle: {color: '#333333'}, showTextEvery: 1},
      data:data,
      target:"#user-last-login",
      type:"ColumnChart"
    };

    chart.remove(opt.target);
    chart.draw(opt);
  }
};