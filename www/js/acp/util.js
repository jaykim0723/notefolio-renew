  var keywordUtil = {
        insert: function(){
            var key = $('#keyword-key').val();
            var val = $('#keyword-val').val();
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

            var json = '{"'+ key +'":"'+ val +'"}';

            var node = $('<tr></tr>').html('<td>'+ key +'</td>'+'<td>'+ val +'</td>'+'<td>'+'<a href="javascript:keywordUtil.update('+ key +')">'+'<span class="btn btn-primary">수정</span>'+'</a>'+'<a href="">'+'<span class="btn btn-danger">삭제</span>'+'</a>'+'<input type="hidden" name="keyword[]" value="'+ json +'" />'+'</td>');
        }
      }
      
  
  $(function(){
  }); 