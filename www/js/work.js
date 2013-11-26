var work = {
	save: function(){
		blockPage.block();
		$.ajax({
			url : $(this).attr('action'),
			data : $(this).serialize(),
			type : 'post',
			dataType : 'json'
		}).done(function(d){
			blockPage.unblock();
			if(d.status=='done')
				site.redirect('/gallery/'+work.work_id, '등록이 성공하였습니다');
			else
				formFeedback('', 'error', '전송에 실패하였습니다.');
		}).fail(function(e){
			blockPage.unblock();
			formFeedback('', 'error', '전송에 실패하였습니다.');
		});
	}
}