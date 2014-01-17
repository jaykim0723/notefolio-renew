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
		setGround: function(target, trash){ // 각 블록별 소팅할 수 있도록 이벤트 바인딩
			if(typeof(target)=='undefined'){
				var target = "#content-block-list";
			}
			$(target).sortable({
  				opacity: 0.6,
    			cursor: 'move',
				start: function(event, ui){
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
			});
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
				var target = '.block-text, .block-image, .block-video, .block-line';
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
						workUtil.content.createBlock(className[1]).appendTo(sendTo);
					}
					//setTimeout("$(this).css('outline', 'none');",500);
				})
				.draggable({
					connectToSortable: "#content-block-list",
					helper: "clone",
					start: function(event, ui){
						$(sendTo).droppable({
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
								}else{
									//$(ui.draggable).remove();
								}
					    	},
					    	disable: true
					    });
					},
					stop: function(event, ui){
						$(sendTo).droppable('destroy');
					}
				});
		},
		createBlock: function(type, data, returnType){
			if(typeof(type)=='undefined'){
				var type = "text";
			}

			var output = '';
			switch(type){
				case 'line':
					output = $('<li class="block-line"></li>');
				break;
				
				case 'image':
					console.log('data : ', data);
					output = workUtil.content.createUploader(
								$('<li class="block-image"><img src="/img/thumb_wide4.jpg"/><button class="btn btn-primary">Upload an image</button></li>'), data	);

					//output = $('<img>').attr('src', '//renew.notefolio.net/img/thumb6.jpg');
				break;

				case 'video':
					output = $('<li class="block-video"><iframe src="//www.youtube.com/embed/wnnOf05WKEs?wmode=opaque"></iframe><div class="block-video-overlay"><textarea></textarea></div></li>');
				break;

				case 'text':
				default:
					var textarea = $('<textarea placeholder="이곳을 눌러 내용을 입력하세요"></textarea>').wysihtml5();
					output = $('<li class="block-text"></li>').append(textarea);
				break;
			}
			if(typeof(data)!='undefined'){
				$(output).append($(data));
			}
			// if(typeof(returnType)!='undefined' 
			// && returnType=="list-block"){
			// 	output =  $('<li></li>')
			// 				.attr('class','block-'+type)
			// 				.append(output);
			// }
			return output;
		},
		removeBlock: function(target){
    		$(target).fadeOut(100, function(){
	    		$(this).remove();
    		});
		},
		createUploader:function(elem, data){
			return $(elem).ajaxUploader({
				url : '/upload/work',
				multiple : false,
				start : function(){

				},
				cancel : function(){
					
				},
				done : function(){

				},
				fail : function(){

				}
			});

		},
		createOldUploader: function(url){
    		return $("<div></div>");
		},

	},



















	'delete' : function(o){
		var url = $(o).attr('href');
		BootstrapDialog.confirm('정말 삭제하시겠습니까?', function(result){
			if(result){
				site.redirect(url);
			}
		}, 'danger');
	}
}