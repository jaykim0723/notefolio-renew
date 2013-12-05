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
				var trash = ".trashable";
			}
			$(target).sortable({
  				opacity: 0.6,
    			cursor: 'move',
    			connectWith: trash,
				start: function(event, ui){
					$(target).droppable('option','disable', true);
				},
		        placeholder: {
		            element: function(clone, ui) {
		            	var container = $('<li class="item-sorting"></li>')
		                		.css('outline', '#00ff00 5px dotted');

		                return $(container).append($('<div>'+clone[0].innerHTML+'</div>').css('opacity','0.5'));
		            },
		            update: function() {
		                return;
		            }
		        },
				stop: function(event, ui){
				},
  				receive: function(event, ui) {

  				},
				update: function(){
					console.log('updated');
				}
			}).droppable({
				addClasses: false,
				over: function(event, ui){
				},
				out: function(event, ui){
				},
		    	drop: function( event, ui ) {
					$(ui.draggable).css('outline', 'none');
		    		var className =(""+$(ui.draggable).attr("class")+"").match(/block-(\w+)/);
					if(className){
						$(ui.draggable)
							.empty()
							.append(workUtil.content.createBlock(className[1]));
					}
					else {
						//$(ui.draggable).remove();
					}
		    	},
		    	disable: true
		    }).disableSelection();
		},
		setTrashBin: function(target, sendTo){
			if(typeof(target)=='undefined'){
				var target = "#trash-bin";
			}
			if(typeof(sendTo)=='undefined'){
				var sendTo = "#content-block-list";
			}
			$(target).droppable({
				tolerance: 'pointer',
				over: function( event, ui ) {
					$(ui.draggable).css('outline', '#ff0000 5px dotted');
				},
				out: function( event, ui ) {
					$(ui.draggable).css('outline', 'none');
				},
		    	drop: function( event, ui ) {
					$(ui.draggable).css('outline', 'none');
		    		workUtil.content.removeBlock(ui.draggable);
		    	}
		    }).draggable({
				helper: "clone",
				start: function(event, ui){
					$(ui.helper).css('width', '5px').css('height', '5px');
					$(sendTo).droppable('option','disable',true);
					$(sendTo).sortable('option','disable',true);
					$('li', sendTo).droppable({
						tolerance: 'touch',
  						over: function( event, ui ) {
  							$(this).css('outline', '#ff0000 5px dotted');
  						},
  						out: function( event, ui ) {
  							$(this).css('outline', 'none');
  						},
				    	drop: function( event, ui ) {
				    		workUtil.content.removeBlock(this);
				    	}
				    });
				},
				stop: function(){
					$(sendTo).sortable('option','enable',true);
					$('li', sendTo).droppable('destroy');
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
			$(target, $(container))
				.on('click', function(event){
					//$(this).css('outline', '#0000ff 5px dotted');
		    		var className =(""+$(this).attr("class")+"").match(/block-(\w+)/);
					if(className){
						$('<li></li>')
							.attr('class','block-'+className[1])
							.append(workUtil.content.createBlock(className[1]))
							.appendTo(sendTo);
					}
					//setTimeout("$(this).css('outline', 'none');",500);
				})
				.draggable({
					connectToSortable: "#content-block-list",
					helper: "clone",
					start: function(event, ui){
						$(sendTo).droppable('option','enable',true);
					},
					stop: function(event, ui){
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
					output = $('<div></div>')
						.addClass('image-upload-box')
						.dropzone({
							url: "/upload/image",
							acceptedFiles: 'image/*',
							paramName: "file", 
							maxFilesize: 128, // MB
							fallback: function() {
								$('<div>Internet Explorer 9 이하는 끌어다 놓기를 지원하지 않습니다.</div>').appendTo(this);
							}
						});

					//output = $('<img>').attr('src', '//renew.notefolio.net/img/thumb6.jpg');
				break;
				case 'video':
					output = $('<img>').attr('src', '//renew.notefolio.net/img/thumb_wide6.jpg');
				break;
				case 'text':
				default:
					output = $('<p></p>').text('이곳을 눌러 내용을 입력하세요.');
				break;
			}

			return output;
		},
		removeBlock: function(target){
    		$(target).fadeOut(100);
    		$(target).remove();
		},

	}
}