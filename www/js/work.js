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
		    	}
		    });
		},
		setDraggable: function(target, container, sendTo){
			if(typeof(target)=='undefined'){
				var target = '.block-text, .block-image, .block-video';
			}
			if(typeof(container)=='undefined'){
				var container = "#work-content-blockadder";
			}
			if(typeof(sendTo)=='undefined'){
				var sendTo = "#work-content-blockadder";
			}
			$(target, $(container)).draggable({
				connectToSortable: "#content-block-list",
				helper: "clone",
				drop: function( event, ui ){
		    		var classNames = $(ui.draggable).attr("class").split(' ');
		    		console.log(classNames[0]);
					for(var i in classNames){
						var m =(""+classNames[i]+"").match(/block-(\w+)/);
						if(m){
							$(ui.draggable)
								.attr('class', classNames[i])
								.empty()
								.append(workUtil.content.createBlock(m[1]));
							break;
						}
						else {
							$(ui.draggable).remove();
						}
					}
				}
			});
		},
		createBlock: function(type, position){
			if(typeof(type)=='undefined'){
				var type = "text";
			}

			var output = '';
			switch(type){
				case 'image':
					output = $('<img>').attr('src', '//www.notefolio.net/images/20121123/nf_logo.png');
				break;
				case 'video':
				default:
					output = $('<img>').attr('src', '//www.notefolio.net/images/20121123/nf_logo.png');
				break;
				case 'text':
				default:
					output = $('<p></p>').text('이곳을 눌러 내용을 입력하세요.');
				break;
			}

			return output;
		},
		removeBlock: function(type, position){
		},

	}
}