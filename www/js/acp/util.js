  var keywordUtil = {
    getRowHtml: function(key,val){
      var json = JSON.stringify(JSON.parse('{"'+ key +'":"'+ val +'"}'));

      var html = '<td>'+ key +'</td>';
      html    += '<td>'+ val +'</td>';
      html    += '<td>';
      html    +=  '<a href="javascript:keywordUtil.update('+ key +'); return;">';
      html    +=   '<span class="btn btn-primary">수정</span>';
      html    +=  '</a> ';
      html    +=  '<a href="javascript:keywordUtil.delete('+ key +'); return;">';
      html    +=   '<span class="btn btn-danger">삭제</span>';
      html    +=  '</a> ';
      html    +=  '<input type="hidden" name="keyword" value='+"'"+ json +"' />";
      html    += '</td>';

      return html;
    },
    insert: function(){
      var key = $('#keyword-key').val().toUpperCase();
      var val = $('#keyword-val').val();

      $('#keyword-key').val('');
      $('#keyword-val').val('');
      if(key==''){
        alert('식별기호를 입력해주세요.');
        $('#keyword-key').val();

        return;
      }
      if(val==''){
        alert('한글출력을 입력해주세요.');
        $('#keyword-val').val();

        return;
      }

      keywordUtil.proc.insert(key, val);

    },
    delete: function(key){
      keywordUtil.proc.delete(key);
    },
    proc: {
      insert: function(key, val){

        var html = keywordUtil.getRowHtml(key, val);
        $('<tr id="keyword-'+key+'"></tr>')
          .html(html)
          .prependTo($('tbody', '#keyword-list'));

        keywordUtil.proc.refreshForm();
      },
      delete: function(key){
        $('tr#keyword-'+key).remove();

        keywordUtil.proc.refreshForm();
      },
      refreshForm: function(){
        var obj = new Object();
        var text = $('input[name=keyword]').each(function(){
          var json = JSON.parse($(this).val());
          for (i in json){
            eval('obj.'+i+'="'+json[i]+'"');
          }
        });

        $('#keyword').text(JSON.stringify(obj));
      },
    }

  }

function url_go_to(key, val, pop){
    var url_args = window.location.href.split('/');
    var new_args = [];
    for (var i=0; i<url_args.length; i++){
        if(url_args[i]==key){
            i++;
            continue;
        }
        else{
            new_args.push(url_args[i]);
        }
    }
    if(typeof(pop)=='undefined' || pop == false){
        new_args.push(key);
        new_args.push(val);
    }

    window.location.href = new_args.join('/');
}
  
$(function(){
  $('.staffpoint-btn').on('click.staffpoint', function(e){
    var $root = $(this).closest('tr');
    e.preventDefault();
    var staffpoint = $('input[name="staffpoint"]', $root).val();
    var work_id = $root.data('id');
    $.post('/acp/work/works/proc/staffpoint', 
          {mode: 'staffpoint', work_id: work_id, staffpoint: staffpoint}, 
          function(data, textStatus, xhr) {
            var response = $.parseJSON(data);
            if(response.status=='done'){
              alert('반영 완료');
            }else{
              alert('오류');
            }
          });
  });

  $('#search_q').keydown(function (e){
      if(e.keyCode == 13){
          $('#search_btn').trigger('click');
      }
  });
}); 