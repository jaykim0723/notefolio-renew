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

      var html = keywordUtil.getRowHtml(key, val);
      $('<tr id="keyword-'+key+'"></tr>')
        .html(html)
        .prependTo($('tbody', '#keyword-list'));

      keywordUtil.refreshForm();
    },
    delete: function(key){
      $('tr#keyword-'+key).remove();
    },
    refreshForm: function(){
      var text = $('input[name=keyword').map(function(){
        return $(this).val();
      })
      .get()
      .join(',');

      $('#keyword').text('{'+text+'}');
    }

  }
      
  
  $(function(){
  }); 