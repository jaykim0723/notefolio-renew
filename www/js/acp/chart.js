/*
 * chart library js for Notefolio ACP
 * 
 * (c) 2013 Notefolio/ Developed by Yoon, Seongsu.
 */

// if you want to use, load google jsapi first.
google.load("visualization", "1", {packages:["corechart"]});

var chart =  {
  /*
   * @brief draw chart
   * @param array option ({title:string,hAxis:Json,data:arrayJson,type:string,target:string})
   */
  draw: function(option){
    if(typeof(option)=="undefined"){
      console.log('Option is not defined.');
      return;
    }

    var data = google.visualization.arrayToDataTable(option.data);
    var opt = {
      title: option.title,
      hAxis: option.hAxis,
      vAxis: option.vAxis,
      chartArea: option.chartArea,
      width: option.width,
      height: option.height,
      sliceVisibilityThreshold: option.sliceVisibilityThreshold
    };
    var chart = eval('new google.visualization.'+option.type+'($("<div></div>").addClass("graph").appendTo("'+option.target+'")[0])');
    chart.draw(data, opt);
  },
  /*
   * @brief remove chart
   * @param string target ($('#id'))
   */
  remove: function(target){
    $('.graph', target).remove();
  }
};