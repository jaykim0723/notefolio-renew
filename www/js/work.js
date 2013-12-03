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
		setGround: function(target, trash){
			if(typeof(target)=='undefined'){
				var target = "#content-block-list";
			}
			if(typeof(trash)=='undefined'){
				var trash = ".trash-can";
			}
			$(target).sortable({
  				opacity: 0.6,
    			connectWith: trash,
    			handle: ".handle",
    			helper: 'clone',
				start: function(){
					$(target).droppable('option','disable', true);
				},
  				receive: function(event, ui) {

  				},
				update: function(){
					console.log('updated');
				}
			}).droppable({
				addClasses: false,
		    	drop: function( event, ui ) {
		    		var className =(""+$(ui.draggable).attr("class")+"").match(/block-(\w+)/);
					if(className){
						$(ui.draggable)
							.empty()
							.append(workUtil.content.createBlock(className[1]));
					}
					else if((""+$(ui.draggable).attr("class")+"").match(/remove/)){
						$(ui.draggable).remove();
					}
					else {
						//$(ui.draggable).remove();
					}
		    	},
		    	disable: true
		    });
		},
		setTrashBin: function(target){
			if(typeof(target)=='undefined'){
				var target = ".trash-can";
			}
			$(target).sortable().droppable({
        		accept: '.block-text, .block-image, .block-video',
			    hoverClass: "ui-state-hover",
		    	drop: function( event, ui ) {
		    		$(ui.draggable).fadeOut(100);
		    		$(ui.draggable).remove();
		    	}
		    });
		},
		setTool: function(target, container, sendTo){
			if(typeof(target)=='undefined'){
				var target = '.block-text, .block-image, .block-video';
			}
			if(typeof(container)=='undefined'){
				var container = "#work-content-blockadder";
			}
			if(typeof(sendTo)=='undefined'){
				var sendTo = "#content-block-list";
			}
			$(target, $(container)).draggable({
				connectToSortable: "#content-block-list",
				helper: "clone",
				start: function(){
					$(sendTo).droppable('option','enable',true);
				},
				stop: function(){
					$(sendTo).droppable('option','disable',true);
				}
			});
		},
		createBlock: function(type, target){
			if(typeof(type)=='undefined'){
				var type = "text";
			}

			var output = '';
			switch(type){
				case 'image':
					output = $('<img>').attr('src', '//www.notefolio.net/images/20121123/nf_logo.png');
				break;
				case 'video':
					output = $('<img>').attr('src', '//www.notefolio.net/images/20121123/nf_logo.png');
				break;
				case 'text':
				default:
					output = $('<p></p>').text('이곳을 눌러 내용을 입력하세요.');
				break;
			}

			return output;
		},
		removeBlock: function(type, target){
		},

	}
}