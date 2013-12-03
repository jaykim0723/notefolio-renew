var workUtil = {
	save: function(form, returnType){
		if(typeof(returnType)=='undefined'){
			var returnType = true;
		}
		blockPage.block();
		$.ajax({
			url : $(form).attr('action'),
			data : $(form).serialize(),
			type : 'post',
			dataType : 'json'
		}).done(function(d){
			blockPage.unblock();
			if(d.status=='done')
				(returnType)?
				site.redirect('/gallery/'+NFview.work_id, '등록이 성공하였습니다'):
				formFeedback('', 'success', '전송에 성공하였습니다.');
			else
				formFeedback('', 'error', '전송에 실패하였습니다.');
		}).fail(function(e){
			blockPage.unblock();
			formFeedback('', 'error', '전송에 실패하였습니다.');
		});
	},
	content: {
		setSortable: function(target){
			if(typeof(target)=='undefined'){
				var target = "#content-block-list";
			}
			$(target).sortable({
  				receive: function(event, ui) {

  				},
				update: function(){
					console.log('updated');
				}
			});
		},
		setDroppable: function(target){
			if(typeof(target)=='undefined'){
				var target = "#content-block-list";
			}
			$(target).droppable({
		      drop: function( event, ui ) {
		        console.log($(this).attr("class"));
		      }
		    });
		},
		setSortable: function(target, container){
			if(typeof(target)=='undefined'){
				var target = "#work-content-blockadder li";
			}
			$(target).draggable({ revert: "valid" });
		},
		createBlock: function(type, position){
			if(typeof(type)=='undefined'){
				var type = "text";
			}
			switch(type){
				case 'image':
					
				break;
				case 'block':
				default:
					
				break;
				case 'text':
				default:
					
				break;
			}
		},
		removeBlock: function(type, position){
		},

	}
}