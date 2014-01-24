var workUtil = {
	saveCover : function(upload_id, src){
		memberUtil.popCrop({
			message : ['400x400 크기의 정사각형 썸네일을 지정해주세요.', '800x400의 직사각형 썸네일을 지정해주세요'],
			src : src,
			width : [400, 800],
			height: [400, 400],
			done : function(dialog){
				var crop1 = NFview.popCrop[0].tellSelect();
				var crop2 = NFview.popCrop[1].tellSelect();
				// 이미지 src, crop 정보를 토대로 사진을 잘라내는 명령을 내린다.
				$.post('/gallery/save_cover', {
					upload_id : upload_id,
					t2 : {
						x : crop1.x,
						y : crop1.y,
						w : crop1.w,
						h : crop1.h
					},
					t3 : {
						x : crop2.x,
						y : crop2.y,
						w : crop2.w,
						h : crop2.h
					}
				}, 'json').done(function(responseJSON){
					console.log('crop cover done > responseJSON', responseJSON);
					
					$('[name=cover_upload_id]').val(upload_id);

					$('#cover-preview .well').each(function(index){
						$(this).html('<img src="'.responseJSON.src[index]+"'/>");
					});

					dialog.close();
					site.scroll.unlock();
				});
			}
		});
	},
	save : function(form, returnType){
		if(typeof(returnType)=='undefined'){
			var returnType = true;
		}
		
		console.log($('#keywords').selectpicker('val'));

		var data = $(form).serialize();

		// keywords에 관련해서는 지우고 다시 작업을 진행한다.
		data = data.replace('')		

		var contents = [];
		$('#content-block-list li').each(function(index){
			var o = {
				t : '', 
				i : '', 
				c : ''
			};
    		var type =(""+$(this).attr("class")+"").match(/block-(\w+)/)[0].replace('block-', '');
    		o.t = type;
    		switch(type){
    			case 'image':
    				o.c = $(this).children('img').attr('src');
    				o.i = $(this).children('img').data('id');
    			break;

    			case 'video':
    				o.c = $(this).children('iframe').attr('src');
    			break;

    			case 'text':
    				o.c = $(this).children('textarea').val();
    			break;

    			case 'line':
    			break;
    		}
    		contents.push(o);
		});
		data += '&contents='+array2json(contents);
		console.log(data);


		blockPage.block();
		$.ajax({
			url : $(form).attr('action'),
			data : data,
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
		restoreContents : function(){ // create든 update든 NFView의 값을 가지고 폼들에 반영을 해준다.
			// 제목 셋팅해주기
			$('#title').val(NFview.title);


			// 내용 블럭 셋팅하기
			var sendTo = $('#content-block-list');
			$.each(NFview.contents, function(k, block){
				workUtil.content.createBlock(block.t, block.c).appendTo(sendTo);
			});


			// 키워드 셋팅하기
			$('#keywords').selectpicker('val', NFview.keywords);


			// 태그 셋팅하기
			var tagsObj = $('#tags');
			tagsObj.tagsinput({
				maxTags : 50,
				confirmKeys: [13, 9, 188]
			})
			$.each(NFview.tags, function(k,v){
				tagsObj.tagsinput('add', v);
			});
	        $('.bootstrap-tagsinput input').on('blur', function(){
	            if($(this).val()!=''){
	                tagsObj.tagsinput('add', $(this).val());
	                $(this).val('');
	            }
	        });

        
			// CCL 셋팅하기
			$('#ccl').selectpicker('val', NFview.ccl);


			// 커버 셋팅하기
			$('#btn-upload-cover').ajaxUploader({
				url : '/upload/image',
				multiple : false,
				droppable : false,
				start : function(){
					blockPage.block();
				},
				cancel : function(){
					blockPage.unblock();
				},
				done : function(responseJSON){
					blockPage.unblock();
					// change profile face
					workUtil.saveCover(responseJSON.upload_id, responseJSON.src);
				},
				fail : function(){
					blockPage.unblock();
				}
			});
			$('#btn-select-cover').on('click', function(){
				var selectCover = memberUtil.popLoading({
					title : '작품내용 중 선택하기',
					done : function(dialog){
						var src = dialog.getModalBody().find('.selected').children('img').attr('src');
						if(typeof src=='undefined'){
							msg.open('이미지를 선택해주세요.', 'error');
							return;
						}
						alert('selected : '+src);
						// dialog.close();
					}
				});
				// call current images
				selectCover.getModal().addClass('dialog-work-list-wrapper');
				$list = $('<ul></ul>');
				$('#content-block-list .block-image').each(function(index){
					$list.append('<li><img src="'+$(this).children('img').prop('src')+'"/></li>');
				});
				selectCover.getModalBody().html($list);
			});

			// footer 버리기
			$('#footer').remove();
			
		},
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
			$(sendTo).on('mouseenter', '.block-video', function(event){
				$(this).find('.block-video-overlay').show().find('textarea').val($(this).find('iframe').prop('src').replace('?wmode=transparent', ''));
			}).on('mouseleave', '.block-video', function(event){
				var videoSrc = $(this).find('iframe').prop('src').replace('?wmode=transparent', '');
				var textSrc = $(this).find('.block-video-overlay').hide().find('textarea').val();
				if(videoSrc!=textSrc){
					$(this).find('iframe').prop('src', textSrc+'?wmode=transparent');
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
		    		if($('html').is(':animated')) return;
		    		var className =(""+$(this).attr("class")+"").match(/block-(\w+)/);
					if(className){
						$newBlock = workUtil.content.createBlock(className[1]).fadeTo(0, 0.01);
						$newBlock.appendTo(sendTo);
						$.when(site.scrollToBottom()).done(function(){
							$newBlock.fadeTo(300, 1);
						});
					}
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
		createBlock: function(type, c, returnType){
			if(typeof(type)=='undefined'){
				var type = "text";
			}
			if(typeof(c)=='undefined'){
				var c = '';
			}

			var output = '';
			switch(type){
				case 'line':
					output = $('<li class="block-line"></li>');
				break;
				
				case 'image':
					if(c=='')
						c = '/img/thumb_wide4.jpg';
					output = workUtil.content.createUploader($('<li class="block-image"><img src="'+c+'"/><button class="btn btn-primary">Upload an image</button></li>'));

					//output = $('<img>').attr('src', '//renew.notefolio.net/img/thumb6.jpg');
				break;

				case 'video':
					if(c=='')
						c = '//www.youtube.com/embed/wnnOf05WKEs';
					output = $('<li class="block-video"><iframe src="'+c+'?wmode=transparent" frameborder="0" wmode="Opaque"></iframe><div class="block-video-overlay"><textarea></textarea></div></li>');
				break;

				case 'text':
				default:
					var textarea = $('<textarea placeholder="이곳을 눌러 내용을 입력하세요"></textarea>').val(c).wysihtml5();
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
				url : '/upload/image',
				multiple : false,
				start : function(){

				},
				cancel : function(){
					
				},
				done : function(){
					// upload id와 src만 반환
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
};
















var memberUtil = {
	popCrop : function(opts){
		var defaults = {
			title : 'Crop',
			message : ['',''],
			width : [400, 800],
			height : [400, 400],
			src : '/img/dummy_big.jpg',
			done : function(dialog){
				console.log('commonUtil > popCrop > done', dialog, NFview.popCrop);
				NFview.popCrop = null;
	            dialog.close();
			}
		};

		// extend the options from defaults with user's options
		var options = $.extend(defaults, opts || {});
		if(typeof options.message!='object')
			options.message = [options.message];
		if(typeof options.width!='object')
			options.width = [options.width];
		if(typeof options.height!='object')
			options.height = [options.height];


		NFview.popCrop = [];
		var dialog = new BootstrapDialog({
		    title: options.title,
		    message: function(){
				var $message = $('<div id="crop-wrapper"></div>');
				for(var i in options.message){
					var $imageWrapper = $(
						'<div class="crop-image-wrapper">'+
							(options.message[i]!='' ? (i==1 ? '<br/>' : '')+ '<h2>'+options.message[i]+'</h2>' : '' ) +
							'<img class="crop-image" src="'+options.src+'"/>' +
						'</div>'
					);
					$imageWrapper.children('img').Jcrop({
						aspectRatio : (options.width[i] / options.height[i]),
						setSelect : [ 0, 0, options.width[i], options.height[i]]
					}, function(){
						NFview.popCrop.push(this); // 나중에 값을 계산하기 위함
					});
					$imageWrapper.appendTo($message);
				}
				return $message;
		    },
		    data: {
                'done': options.done
            },
		    buttons: [
			    {
			        label: 'Crop',
			        cssClass: 'btn-primary',
			        action: function(dialog){
			        	typeof dialog.getData('done') === 'function' && dialog.getData('done')(dialog);
			        }
			    },{
			        label: 'Cancel',
			        cssClass: 'btn-default',
			        action: function(dialog){
						NFview.popCrop = null;
			            dialog.close();
			        }
			    }
		    ]
		});
		dialog.realize();
		dialog.getModal().prop('id', 'crop-wrapper'); // cssClass 버그로 인해서 이 꼼수로..
		dialog.open();
		return dialog;
	},


	popLoading : function(opts){
		var defaults = {
			title : 'Loading',
			id : 'ajax-dialog-wrapper',
			done : function(dialog){
				console.log('commonUtil > popLoading > done', dialog);
	            dialog.close();
			}
		};
		// extend the options from defaults with user's options
		var options = $.extend(defaults, opts || {});

		var dialog = new BootstrapDialog({
		    title: options.title,
		    message: '<div class="loading"><img src="/img/loading.gif"/></div>',
		    data: {
                'done': options.done
            },
		    buttons: [
			    {
			        label: 'Select',
			        cssClass: 'btn-primary',
			        action: function(dialog){
			        	typeof dialog.getData('done') === 'function' && dialog.getData('done')(dialog);
			        }
			    },{
			        label: 'Cancel',
			        cssClass: 'btn-default',
			        action: function(dialog){
			            dialog.close();
			        }
			    }
		    ]
		});
		dialog.realize();
		dialog.getModal().prop('id', options.id); // cssClass 버그로 인해서 이 꼼수로..
		dialog.open();
		return dialog;
	}
};

var profileUtil = {
	changeFace : function(upload_id, src){
		// change profile face
		memberUtil.popCrop({
			message : '프로필 사진으로 쓸 영역을 지정해주세요.',
			src : src,
			width : 400,
			height: 400,
			done : function(dialog){
				var crop = NFview.popCrop[0].tellSelect();
				// 이미지 src, crop 정보를 토대로 사진을 잘라내는 명령을 내린다.
				$.post('/profile/change_face', {
					upload_id : upload_id,
					x : crop.x,
					y : crop.y,
					w : crop.w,
					h : crop.h
				}, 'json').done(function(responseJSON){
					console.log('crop profile face done > responseJSON', responseJSON);
					// 프로필 이미지를 응답받은 주소로 갱신을 해준다.
					msg.open('적용이 완료되었습니다.');
					$('#profile-image').children('img').prop('src', responseJSON.src);
					dialog.close();
					site.scroll.unlock();
				});
			}
		});
	},

	changeBg : function(upload_id, src){
		// change profile bg
		$.post('/profile/change_bg', {
			upload_id : upload_id
		}, 'json').done(function(responseJSON){
			console.log('crop profile bg done > responseJSON', responseJSON);
			// 프로필 배경을 응답받은 주소로 갱신을 해준다.
			msg.open('적용이 완료되었습니다.');
			$('#profile-header').css('background-image', responseJSON.src);
		});
	},

	setGround :  function(){
		$('#btn-upload-face').ajaxUploader({
			url : '/upload/image',
			multiple : false,
			droppable : false,
			start : function(){
				blockPage.block();
			},
			cancel : function(){
				blockPage.unblock();
			},
			done : function(responseJSON){
				blockPage.unblock();
				profileUtil.changeFace(responseJSON.upload_id, responseJSON.src);
			},
			fail : function(){
				blockPage.unblock();
			}
		});
		$('#btn-upload-bg').ajaxUploader({
			url : '/upload/image',
			multiple : false,
			droppable : false,
			start : function(){
				blockPage.block();
			},
			cancel : function(){
				blockPage.unblock();
			},
			done : function(responseJSON){
				blockPage.unblock();
				profileUtil.changeBg(responseJSON.upload_id, responseJSON.src);
			},
			fail : function(){
				blockPage.unblock();
			}
		});
		$('#btn-delete-face').on('click', function(){

		});
		$('#btn-delete-bg').on('click', function(){

		});
		$('#btn-select-face, #btn-select-bg').on('click', function(){
			var _actionTarget = this.id == 'btn-select-bg' ? 'bg' : 'face';
			site.popWorkList({
				username :site.segment[0],
				done : function(dialog){
					console.log('profile headialoger done', dialog);
					var work_id = dialog.getModalBody().find('.selected').prop('id').replace('work-recent-', '');
					console.log('work_id : ', work_id);
					dialog.close();
					site.scroll.unlock();

					$.get(site.url+'/upload/get_upload_id_by_work_id/'+work_id, {}, function(responseJSON){
						// work_id에 대한 이미지 원본을 가지고 와서 크롭창을 띄워준다.
						if(_actionTarget=='face'){
							profileUtil.changeFace(responseJSON.upload_id, responseJSON.src);
						}else{
							profileUtil.changeBg(responseJSON.upload_id, responseJSON.src);
						}
					}, 'json');
				}
			});
		});
		$('#btn-change-color').on('click', function(){
			var dialog = new BootstrapDialog({
			    title: '프로필 배경색 변경',
			    message: function(){
			    	return $('<input type="text" id="input-change-color" style="display:none;">');
			    },
			    data : {
			    	done : function(dialog){
			    		var color = $('.sp-input').val();
			    		$.post('/profile/change_color', {color: color}, function(responseJSON){
			    			$('#profile-inner-wrapper').css('background-color', responseJSON.color);
				            dialog.close();
							site.scroll.unlock();
			    			msg.open('배경색 변경 완료');
			    		}, 'json');
			    	}
			    },
			    onshow: function(dialogRef){
			    	setTimeout(function(){
						$('#input-change-color').spectrum({
							flat: true,
							color: $('#profile-inner-wrapper').css('background-color'),
							showInitial: true,
							showInput: true,
							showAlpha : true
						});
					}, 500);
	            },
			    buttons: [
				    {
				        label: 'Change',
				        cssClass: 'btn-primary',
				        action: function(dialog){
				        	typeof dialog.getData('done') === 'function' && dialog.getData('done')(dialog);
				        }
				    },{
				        label: 'Cancel',
				        cssClass: 'btn-default',
				        action: function(dialog){
				            dialog.close();
							site.scroll.unlock();
				        }
				    }
			    ]
			});
			dialog.realize();
			dialog.getModal().prop('id', 'dialog-change-color'); // cssClass 버그로 인해서 이 꼼수로..
			dialog.open();
			site.scroll.lock();
		});
	}



};










$(function(){
	$(document).on('click', '.dialog-work-list-wrapper li', function(){
		$(this).parent().children('.selected').removeClass('selected');
		$(this).addClass('selected');
	})
});