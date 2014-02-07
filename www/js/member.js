var workUtil = {
	defaultValue : {
		image : '/img/thumb_wide4.jpg',
		video : '//www.youtube.com/embed/wnnOf05WKEs'
	},
	showCoverTip : function(){
		$('#cover-preview .col-md-12').show();
		$('#cover-preview .col-md-4').hide();
	},
	saveCover : function(upload_id, src){
		site.scroll.lock();
		memberUtil.popCrop({
			message : ['400x400 크기의 정사각형 썸네일을 지정해주세요.', '800x400의 직사각형 썸네일을 지정해주세요'],
			src : src,
			width : [400, 800],
			height: [400, 400],
			done : function(dialog){
				var crop1 = NFview.popCrop[0].tellSelect();
				var crop2 = NFview.popCrop[1].tellSelect();
				var tmp = {
					1 : {},
					2 : {}
				};
				tmp[crop1.w/crop1.h] = crop1;
				tmp[crop2.w/crop2.h] = crop2;

				// 이미지 src, crop 정보를 토대로 사진을 잘라내는 명령을 내린다.
				blockPage.block();
				$.post('/gallery/save_cover', {
					work_id : NFview.work_id,
					upload_id : upload_id,
					t2 : {
						x : tmp[1].x,
						y : tmp[1].y,
						w : tmp[1].w,
						h : tmp[1].h
					},
					t3 : {
						x : tmp[2].x,
						y : tmp[2].y,
						w : tmp[2].w,
						h : tmp[2].h
					}
				}, 'json').done(function(responseJSON){
					console.log('crop cover done > responseJSON', responseJSON);
					blockPage.unblock();
					if(responseJSON.status=='done'){

						$('[name=cover_upload_id]').val(upload_id);

						var $cover = $('#cover-preview');

						$cover.children('.col-md-12').hide();
						$cover.children('.col-md-4').show();

						$cover.find('.preview').each(function(index){
							$(this).attr('src', responseJSON.src[index]);
						});

						dialog.close();
						site.scroll.unlock();
					}
				});
			}
		});
	},
	checkValue : {
		title : function(){
			return $('#title').val()=='' ? 0 : 1;
		},
		contents : function(){
			var o = {
				image : 0,
				video : 0,
				text : 0,
				line : 0
			}
			$('#content-block-list > li').each(function(index){
	    		var type =(""+$(this).attr("class")+"").match(/block-(\w+)/)[0].replace('block-', '');
	    		switch(type){
	    			case 'image':
	    				if(empty($(this).children('img').data('id')))
	    					return true; // 더미 이미지는 제외한다.
	    			break;
	    			case 'video':
	    				if($(this).children('iframe').length > 0 && $(this).children('iframe').attr('src').indexOf(workUtil.defaultValue.video)!=-1)
	    					return true; // 기본 영상은 없는 걸로 친다
	    			break;
	    			case 'text':
	    				if($(this).children('textarea').val()=='')
	    					;//return true; // 내용이 없으면 빈놈으로 취급한다.
	    			break;
	    		}
	    		o[type]++;
			});
			return o;
		},
		keywords : function(){
			return empty($('#keywords').val()) ? 0 : $('#keywords').val().length;
		},
		tags : function(){
			var v = $('#tags').val();
			if(v.indexOf(',')!==-1)
				return v.split(',').length;
			else if(v!='')
				return 1;
			else
				return 0;
		},
		ccl : function(){
			return $('#ccl').val()=='' ? 0 : 1
		},
		cover : function(){
			return $('#cover-preview img:first').attr('src').indexOf('cover_default.png')!=-1 ? 0 : 1;
		}

	},
	discoverbility : function(){
		var total = 0;
		var value = {
			title : workUtil.checkValue.title(),
			contents : workUtil.checkValue.contents(),
			keywords : workUtil.checkValue.keywords(),
			tags : workUtil.checkValue.tags(),
			ccl : workUtil.checkValue.ccl(),
			cover : workUtil.checkValue.cover()
		};
		console.log(value.contents);
		if(value.contents.image == 1)
			total += 20;
		else if(value.contents.image == 2)
			total += 40;
		else if(value.contents.image > 2)
			total += 50;
		if(value.contents.video > 0)
			total += 20;
		if(value.contents.text > 0)
			total += 20;
		if(value.keywords>0)
			total += 20;
		if(value.tags == 1)
			total += 5;
		else if(value.tags > 1)
			total += 10;
		$('#work-discoverbility > span').stop().animate({
			width : total+'%'
		}, 1000);
		console.log('discoverbility', total, value);
		return value;
	},
	save : function(form, returnType){
		if(typeof(returnType)=='undefined'){
			var returnType = true;
		}
		var value = this.discoverbility();
		// 필수 입력값에 대해서만 간단하게 검사
		if(value.title==0){
			msg.open('제목을 입력하여 주십시오.', 'error', '#title', 'y');
			return;
		}
		if(value.contents.image + value.contents.video + value.contents.text == 0 ){
			msg.open('내용을 생성하여 주십시오.', 'error');
			return;
		}
		if(value.keywords == 0){
			msg.open('카테고리를 선택하여 주십시오.', 'error', $('#keywords').next(), 'y');
			return;
		}
		if(value.cover == 0){
			msg.open('커버를 지정하여 주십시오.', 'error', '#btn-upload-cover-wrapper', 'y');
			return;
		}
		
		var data = $(form).serialize();

		// keywords에 관련해서는 지우고 다시 작업을 진행한다.
		data = data.replace(/(&?keywords)=([A-Z7]{2})/g, '$1[]=$2');

		var contents = [];
		$('#content-block-list > li').each(function(index){
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
    				if(o.i=='')
    					return true; // 더미 이미지는 제외한다.
    			break;

    			case 'video':
    				o.c = $(this).children('iframe').attr('src').replace('?wmode=transparent', '');
    			break;

    			case 'text':
    				o.c = br2nl($(this).children('textarea').val());
    			break;

    			case 'line':
    			break;
    		}
    		contents.push(o);
		});
		data += '&contents='+JSON.stringify(contents);

		blockPage.block();
		$.ajax({
			url : $(form).attr('action'),
			data : data,
			type : 'post',
			dataType : 'json'
		}).done(function(d){
			blockPage.unblock();
			if(d.status=='done'){
				BootstrapDialog.confirm('전송이 성공되었습니다. 해당 작품으로 지금 이동하시겠습니까?', function(result){
		            if(result) {
		                site.redirect('/'+NFview.user.username+'/'+NFview.work_id);
		            }else{
		                ;
		            }
		        });
			}else
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
			if(NFview.contents.length>0){
				$.each(NFview.contents, function(k, block){
					$target = $('<li class="block-'+block.t+' block"></li>')
						.prepend($('<i class="spi spi-close2">close2</i>')).appendTo(sendTo);
					//$newBlock = workUtil.content.createBlock(className[1]);
					workUtil.content.applyBlock($target, block.t, block.c, block.i);
					$('#default-image').remove();
					workUtil.discoverbility();
				});
			}else{
				$('#content-block-list').append('<div id="default-image"><img src="/img/uploadcont1.png"/></div>');
			}
			$('#content-block-list').on('click', 'i.spi', function(){
				workUtil.content.removeBlock($(this).parents('li.block'));
			});

			// 키워드 셋팅하기
			$('#keywords').on('change', function(){
				var count = workUtil.checkValue.keywords();
				if(count>2){
					$(this).selectpicker('val', $(this).data('old').split(','));
				}else{
					$(this).data('old', count > 0 ? $(this).val().join(',') : '');
				}
	            workUtil.discoverbility();
			});

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
	            workUtil.discoverbility();
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
						var $img = dialog.getModalBody().find('.selected').children('img');
						if($img.length == 0){
							msg.open('이미지를 선택해주세요.', 'error');
							return;
						}
						var src = $img.attr('src');
						var upload_id = $img.data('id');
						if(typeof src=='undefined'){
							msg.open('이미지를 선택해주세요.', 'error');
							return;
						}
						dialog.close();
						workUtil.saveCover(upload_id, src);
					}
				});
				// call current images
				selectCover.getModal().addClass('dialog-work-list-wrapper');
				$list = $('<ul class="dialog-work-list"></ul>');
				$('#content-block-list .block-image').each(function(index){
					var $img = $(this).children('img');
					$list.append('<li><img src="'+$img.prop('src')+'" data-id="'+$img.data('id')+'"/></li>');
				});
				selectCover.getModalBody().html($list);
			});

			$('#content-multiple').ajaxUploader({
				url : '/upload/image',
				multiple : true,
				start : function(elem, id, fileName){
					$('#default-image').remove();
					$('#content-block-list').append($('<li class="block-image block" id="temp-'+id+'" style="min-height:100px;"><div class="ajax-loading-overlay"><img src="/img/ajax-loader.gif" class="ajax-loading"/></div><i class="spi spi-close2">close2</i></li>'));
				},
				cancel : function(elem, id, fileName){
					console.log(elem, id, fileName);
				},
				done : function(responseJSON, elem, id, fileName){
					var $o = $('#temp-'+id);
					$o.css('min-height', 'auto');
					$o.children('.ajax-loading-overlay').remove();
					$o.append('<img/><button class="btn btn-primary">Upload an image</button>');
					workUtil.content.createUploader($o).children('img:first').prop('src', responseJSON.src).data('id', responseJSON.upload_id);
					workUtil.discoverbility();
				},
				fail : function(responseJSON, elem, id, fileName){
					console.log(responseJSON, elem, id, fileName);
				}
			});

			// footer 버리기
			$('#footer').remove();

			workUtil.discoverbility();
			
		},

		setGround: function(target, trash){ // 각 블록별 소팅할 수 있도록 이벤트 바인딩
			if(typeof(target)=='undefined'){
				var target = "#content-block-list";
			}
			$(target).sortable({
  				opacity: 0.6,
    			cursor: 'move',
    			distance: 15,
				start: function(event, ui){
				},
				stop: function(event, ui){
				},
  				receive: function(event, ui) {
  				},
				update: function(event, ui){
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
				$(this).find('.block-video-overlay').show().find('textarea').val('<iframe src="'+$(this).find('iframe').attr('src').replace('?wmode=transparent', '')+'"></iframe>');
			}).on('change mouseleave', '.block-video', function(event){
				var videoSrc = $(this).find('iframe').attr('src').replace('?wmode=transparent', '');
				var textSrc = $(this).find('.block-video-overlay').hide().find('textarea').val();
				if(textSrc.indexOf(videoSrc)==-1){
					$(this).find('iframe').prop('src', $(textSrc).attr('src')+'?wmode=transparent');
					workUtil.discoverbility();
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
						className = className[1];
						$target = $('<li class="block-'+className+' block"></li>')
							.fadeTo(0, 0.01)
							.appendTo(sendTo)
							.prepend($('<i class="spi spi-close2">close2</i>'));

						$.when(workUtil.content.applyBlock($target, className)).done(function(){
							$target.fadeTo(150, 1);
						});
						
						$('#default-image').remove();
						workUtil.discoverbility();
					}
				})
				.draggable({
					connectToSortable: "#content-block-list",
					helper: 'clone',
					distance: 15,
					start: function(event, ui){
						$(sendTo).droppable({
							addClasses: false,
							over: function(event, ui){
							},
							out: function(event, ui){
							},
					    	drop: function( event, ui ) {
								$(ui.draggable).css('outline', 'none');
					    		var className = $(ui.draggable).attr("class").match(/block-(\w+)/);
								if(className){
									className = className[1];
									$target = $(ui.draggable).empty().prepend($('<i class="spi spi-close2">close2</i>'));
									//$newBlock = workUtil.content.createBlock(className[1]);
									workUtil.content.applyBlock($target, className);
									$('#default-image').remove();
									workUtil.discoverbility();
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
		createBlock : function(type, c, i, returnType){
			if(typeof(type)=='undefined'){
				var type = "text";
			}
			if(typeof(c)=='undefined'){
				var c = '';
			}
			if(typeof(i)=='undefined'){
				var i = '';
			}

			var output = '';
			switch(type){
				case 'line':
					output = $('<li class="block-line block"></li>');
				break;
				
				case 'image':
					if(c=='')
						c = workUtil.defaultValue.image;
					output = workUtil.content.createUploader($('<li class="block-image block"><img data-id="'+i+'" src="'+c+'"/><button class="btn btn-primary">Upload an image</button></li>'));
					//output = $('<img>').attr('src', '//renew.notefolio.net/img/thumb6.jpg');
				break;

				case 'video':
					if(c=='')
						c = workUtil.defaultValue.video;
					output = $('<li class="block-video block"><iframe src="'+c+'?wmode=transparent" frameborder="0" wmode="Opaque"></iframe><div class="block-video-overlay"><textarea></textarea></div><i class="spi spi-close2">close2</i></li>');
				break;

				case 'text':
				default:
					var textarea = $('<textarea placeholder="이곳을 눌러 내용을 입력하세요"></textarea>').val(nl2br(c));
					output = $('<li class="block-text block"><i class="spi spi-close2">close2</i></li>').append(textarea);
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
		applyBlock : function(target, type, c, i){
			if(typeof(type)=='undefined'){
				var type = "text";
			}
			if(typeof(c)=='undefined'){
				var c = '';
			}
			if(typeof(i)=='undefined'){
				var i = '';
			}

			$(target).addClass('block');

			switch(type){
				case 'line':
				break;
				
				case 'image':
					if(c=='')
						c = workUtil.defaultValue.image;
					var img = $('<img data-id="'+i+'" src="'+c+'"/><button class="btn btn-primary">Upload an image</button>').appendTo($(target));
					workUtil.content.createUploader($(target));
				break;

				case 'video':
					if(c=='')
						c = workUtil.defaultValue.video;
					var img = $('<iframe src="'+c+'?wmode=transparent" frameborder="0" wmode="Opaque"></iframe><div class="block-video-overlay"><textarea></textarea></div>').appendTo($(target));
				break;

				case 'text':
				default:
					var textarea = $('<textarea placeholder="이곳을 눌러 내용을 입력하세요"></textarea>').val(nl2br(c));
					$(target).append(textarea).find('textarea').wysihtml5();
				break;
			}
			return true;
		},
		removeBlock: function(target){
    		$(target).fadeOut(100, function(){
	    		$(this).remove();
	    		workUtil.discoverbility();
    		});
		},
		createUploader:function(elem, data){
			return $(elem).ajaxUploader({
				url : '/upload/image',
				multiple : false,
				start : function(elem, id, fileName){
					elem.append('<div class="ajax-loading-overlay"><img src="/img/ajax-loader.gif" class="ajax-loading"/></div>');
				},
				cancel : function(elem, id, fileName){
					console.log(elem, id, fileName);
				},
				done : function(responseJSON, elem, id, fileName){
					elem.children('.ajax-loading-overlay').remove();
					elem.children('img').prop('src', responseJSON.src).data('id', responseJSON.upload_id);
					workUtil.discoverbility();
				},
				fail : function(responseJSON, elem, id, fileName){
					console.log(responseJSON, elem, id, fileName);
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
						site.scroll.unlock();
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
				blockPage.block();
				$.post('/profile/change_face', {
					upload_id : upload_id,
					x : crop.x,
					y : crop.y,
					w : crop.w,
					h : crop.h
				}, 'json').done(function(responseJSON){
					console.log('crop profile face done > responseJSON', responseJSON);
					blockPage.unblock();
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
		blockPage.block();
		$.post('/profile/change_bg', {
			upload_id : upload_id
		}, 'json').done(function(responseJSON){
			blockPage.unblock();
			console.log('crop profile bg done > responseJSON', responseJSON);
			// 프로필 배경을 응답받은 주소로 갱신을 해준다.
			msg.open('적용이 완료되었습니다.');
			$('#profile-header').css('background-image', 'url('+responseJSON.src+')');
		});
	},
	changeUsername : function(){
		var dialog = new BootstrapDialog({
		    title: '사용자명 변경',
		    message: function(){
				var $message = $(
					'<div id="dialog-change-username">'+
						'<label>변경할 사용자명</label><br/>'+
						'<input type="text" class="form-control" value="'+$('#profile-info > h2').text()+'"/>'+
					'</div>'
					);
				return $message;
		    },
		    buttons: [
			    {
			        label: 'Change',
			        cssClass: 'btn-primary',
			        action: function(dialog){
			        	var value = $('#dialog-change-username').find('input').val();
			        	if(value.length < 3){
			        		msg.open('최소한 3글자 이상을 입력하셔야 합니다.');
			        		return false;
			        	}
			        	$.post('/profile/change_username', {
			        		username : $.trim(value)
			        	}, 'json').done(function(responseJSON){
			        		if(responseJSON.status=='done'){
				        		$('#profile-info > h2').text(value);
				        		dialog.close();
				        		BootstrapDialog.alert('이름 변경이 완료되었습니다. 정상적인 사용을 위해 새로고침을 하겠습니다.', function(){
						            blockPage.block();
						            site.redirect('/'+responseJSON.username);
						        });
			        		}else{
			        			msg.open(responseJSON.msg, 'error');
			        		}
			        	});
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
		dialog.getModal().prop('id', 'dialog-change-username'); // cssClass 버그로 인해서 이 꼼수로..
		dialog.open();
		setTimeout(function(){
			$('#dialog-change-username').find('input').focus();
		}, 500);
	},
	changeKeywords : function(){
		var dialog = new BootstrapDialog({
		    title: '카테고리 변경',
		    message: function(){
		    	var currentKeywords = $('#profile-keywords').data('value').match(/.{1,2}/g);
		    	if(currentKeywords==null)
		    		currentKeywords = [];
		    	var area = '';
		    	for(var i in NFview.keywordList){
		    		area += '<div class="checkbox"><label><input type="checkbox" name="keywords" value="'+i+'" '+($.inArray(i, currentKeywords)>-1 ? 'checked' : '')+'>'+NFview.keywordList[i]+'</label></div>';
		    	}
				var $message = $(
					'<div id="dialog-change-keywords">'+
						'<label>최대 3개를 선택하여 주세요.</label>'+
						area + 
					'</div>'
					);
				return $message;
		    },
		    buttons: [
			    {
			        label: 'Change',
			        cssClass: 'btn-primary',
			        action: function(dialog){
			        	var value = '';
			        	$('#dialog-change-keywords').find(':checkbox').each(function(){
			        		if($(this).is(':checked'))
			        			value += $(this).val();
			        	});
			        	// if(value.length < 3){
			        	// 	msg.open('최소한 3글자 이상을 입력하셔야 합니다.');
			        	// 	return false;
			        	// }
			        	$.post('/profile/change_keywords', {
			        		keywords : value
			        	}, 'json').done(function(responseJSON){
			        		if(responseJSON.status=='done'){
				        		$('#profile-keywords').data('value', value).html('&nbsp;'+responseJSON.keywords_string);
				        		dialog.close();
			        		}else{
			        			msg.open(responseJSON.msg, 'error');
			        		}
			        	});
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
		dialog.getModal().prop('id', 'dialog-change-keywords'); // cssClass 버그로 인해서 이 꼼수로..
		dialog.open();
	},
	changeSNS : function(){
		var dialog = new BootstrapDialog({
		    title: '소셜주소 변경',
		    message: function(){
		    	var area = '<label>해당사항이 있는 곳에 아이디를 지정해주세요.</label><br/><br/>';
				$.each([
					'website', 'facebook', 'twitter', 'pinterest', 'tumblr', 'vimeo'
				], function(k, v){
					var value = $('#profile-sns-link > a.'+v).data('value');
					if(empty(value))
						value = '';
					area += '<div class="form-group row">'+
						'<div class="col-md-2 centered"><i class="spi spi-'+v+'"></i></div>'+
						'<div class="col-md-10"><input type="text" class="form-control" name="'+v+'" value="'+value+'"/></div>'+
					'</div>';
				});
				var $message = $(
					'<div id="dialog-change-sns">'+
						area + 
					'</div>'
					);
				return $message;
		    },
		    buttons: [
			    {
			        label: 'Change',
			        cssClass: 'btn-primary',
			        action: function(dialog){
			        	var sns = {};
			        	$('#dialog-change-sns').find('input').each(function(){
			        		sns[$(this).attr('name')] = $(this).val();
			        	});
			        	// if(value.length < 3){
			        	// 	msg.open('최소한 3글자 이상을 입력하셔야 합니다.');
			        	// 	return false;
			        	// }
			        	$.post('/profile/change_sns', sns, 'json').done(function(responseJSON){
			        		if(responseJSON.status=='done'){
				        		$('#profile-sns-link').html('&nbsp;'+responseJSON.sns_string);
				        		dialog.close();
			        		}else{
			        			msg.open(responseJSON.msg, 'error');
			        		}
			        	});
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
		dialog.getModal().prop('id', 'dialog-change-sns'); // cssClass 버그로 인해서 이 꼼수로..
		dialog.open();
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
		$('#btn-delete-face, #btn-delete-bg').on('click', function(){
			var _actionTarget = this.id == 'btn-delete-bg' ? 'bg' : 'face';
			BootstrapDialog.confirm('정말 삭제하시겠습니까?', function(result){
				if(result){
					$.post('/profile/delete_'+_actionTarget, {}, 'json').done(function(responseJSON){
						if(_actionTarget=='face'){
							$('#profile-image').children('img').prop('src', '/img/default_profile_face.png');
						}else{
							$('#profile-header').css('background-image', '');
						}
					});
				}
			}, 'danger');
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


		$('#btn-change-username').on('click', function(){
			profileUtil.changeUsername();
		});
		$('#btn-change-keywords').on('click', function(){
			profileUtil.changeKeywords();
		});
		$('#btn-change-sns').on('click', function(){
			profileUtil.changeSNS();
		});


	},


	statistics : {
		setGround : function(){
			NFview.oldPeriod = 'latest1'; // 기본기간을 설정한다. 최근 1개월
			NFview.oldType = 'hit'; // 기본타입을 설정한다. 조회수
			profileUtil.statistics.clickEvent('period', NFview.oldPeriod); // period가 바뀌는 경우에는 전체를 업데이트하므로....
			$('#statistics-toolbars a').on('click', function(){
				var type = $(this).data('type');
				var value = $(this).data('value');
				if(type=='period' && value=='user'){
					var dialog = new BootstrapDialog({
					    message: function(dialogRef){
					    	var s = '<div class="row">' +
						    	'<div class="col-sm-6">'+
						    		'조회시작<br/>'+
						    		'<input type="date" value="'+NFview.sdate+'" name="sdate"/>'+
						    	'</div>'+
						    	'<div class="col-sm-6">'+
						    		'조회종료<br/>'+
						    		'<input type="date" value="'+NFview.edate+'" name="edate"/>'+
						    	'</div>'+
					    	'</div>';
					        var $message = $(s);
					        return $message;
					    },
					    buttons: [
						    {
						        label: 'Select',
						        cssClass: 'btn-primary',
						        action: function(dialog){
						        	var s = [];
						        	dialog.getModalBody().find('input').each(function(){
						        		s.push($(this).val());
						        	});
						        	profileUtil.statistics.clickEvent('period', s.join('~'));
						            dialog.close();
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
					dialog.open();
					dialog.getModal().prop('id', 'select-range-wrapper'); // cssClass 버그로 인해서 이 꼼수로..
					dialog.getModalBody().find('[type=date]').datepicker({
			        	format : 'yyyy-mm-dd'
			        }).on('changeDate', function(ev){
		        		dialog.getModalBody().find('[type=date]').datepicker('hide');
					});
				}else{
					profileUtil.statistics.clickEvent(type, value);
				}
			});
		},

		clickEvent : function(type, value){
			console.log('profileUtil > statistics > clickEvent ', type, value);
			if(type=='period'){
				// 기간이 달라지는 경우 아래의 두 가지에 대해서 조회를 한다.
				profileUtil.statistics.reLoadTopCount(value);
				profileUtil.statistics.reLoadTable(value);
				profileUtil.statistics.reLoadChart(NFview.oldType, value);
				NFview.oldPeriod = value;
			}else if(type=='type'){
				// type만 달라진 경우
				profileUtil.statistics.reLoadChart(value, NFview.oldPeriod);
				NFview.oldType = value;
			}
			// 표시하는 부분을 교체해준다.
			var $obj = $('#statistics-toolbars a').filter('[data-value="'+NFview.oldPeriod+'"]');
			var disp = '';
			if($obj.length > 0)
				disp = $obj.html();
			else
				disp = '임의('+NFview.oldPeriod+')';
			$('#statistics-period').html(disp);

			// 표시하는 부분을 교체해준다.
			$('#statistics-type').html($('#statistics-toolbars a').filter('[data-value='+NFview.oldType+']').html());
		},

		reLoadTopCount : function(period){
			$.get(site.url+'/profile/statistics_count', {
				period : period
			}, 'json').done(function(responseJSON){
				$('#statistics-total-hit').html(responseJSON.row.hit_cnt);
				$('#statistics-total-note').html(responseJSON.row.note_cnt);
				$('#statistics-total-collect').html(responseJSON.row.collect_cnt);
			});
		},

		reLoadChart : function(type, period){
			$.get(site.url+'/profile/statistics_chart', {
				type : type,
				period : period
			}, 'json').done(function(responseJSON){
				NFview.sdate = responseJSON.sdate;
				NFview.edate = responseJSON.edate;

				$.plot("#statistics-chart", [ {label:$('#statistics-toolbars a').filter('[data-value='+type+']').html(), data:responseJSON.rows, color:'#2ac5c6'} ], {
					series: {
						lines: {
							show: true
						}
					},
					xaxes: [ {
						mode: "time",
						timeformat: "%y-%m-%d"
					} ],
					grid: {
						hoverable: true
					}
				});
				$("<div id='tooltip'></div>").css({
					position: "absolute",
					display: "none",
					border: "1px solid #fdd",
					padding: "2px",
					"background-color": "#fee",
					opacity: 0.80
				}).appendTo("body");

				$("#statistics-chart").bind("plothover", function (event, pos, item) {
					if (item) {
						var x = item.datapoint[0].toFixed(2),
							y = item.datapoint[1].toFixed(2);
						var date = new Date(item.datapoint[0]);

						var year = date.getFullYear();
						var month = date.getMonth() + 1;
						var day = date.getDate();
						x = year +'-' + month + '-' + day;

						$("#tooltip").html(x + " = " + y)
							.css({top: item.pageY+5, left: item.pageX+5})
							.fadeIn(200);
					} else {
						$("#tooltip").hide();
					}
				});
				window.onresize = function(event) {
			        $.plot($("#statistics-chart"), [ {label:type, data:responseJSON.rows, color:'#2ac5c6'} ],{
					series: {
						lines: {
							show: true
						}
					},
					xaxes: [ {
						mode: "time",
						timeformat: "%y-%m-%d"
					} ],
					grid: {
						hoverable: true
					}
				});
			    }
			});	
		},

		reLoadTable : function(period){
			$.get(site.url+'/profile/statistics_datatable', {
				period : period
			}, 'json').done(function(responseJSON){
				var html = '<table class="table">'+
					'<thead>'+
						'<tr>'+
							'<th>작품번호</th>'+
							'<th>작품명</th>'+
							'<th>날짜</th>'+
							'<th>조회수</th>'+
							'<th>노트수</th>'+
							'<th>콜렉트수</th>'+
						'</tr>'+
					'</thead>'+
					'<tbody>';
				for(var i in responseJSON.rows){
					html += '<tr>' +
						'<td>'+ responseJSON.rows[i].work_id+'</td>' +
						'<td>'+ responseJSON.rows[i].title+'</td>' +
						'<td>'+ responseJSON.rows[i].regdate+'</td>' +
						'<td>'+ responseJSON.rows[i].hit_cnt+'</td>' +
						'<td>'+ responseJSON.rows[i].note_cnt+'</td>' +
						'<td>'+ responseJSON.rows[i].collect_cnt+'</td>' +
						'</tr>';
				}
				html += '</tbody>'+
					'</table>';
				$('#statistics-table-area').html(html).children('table').dataTable();
			});
		}
	},

	about : {
		setGround : function(){
			$('#btn-update-about').on('click', function(){
				profileUtil.about.update();
			});
			$('#btn-submit-about').on('click', function(){
				profileUtil.about.submit();
			})
			$('#btn-cancel-about').on('click', function(){
				profileUtil.about.cancel();
			})
			$('#about-upload').ajaxUploader({
				url : '/upload/image',
				multiple : true,
				droppable : true,
				start : function(){
					blockPage.block();
				},
				cancel : function(){
					blockPage.unblock();
				},
				done : function(responseJSON){
					blockPage.unblock();
					// change profile face
					profileUtil.about.createAttachmentBlock(responseJSON).insertBefore('#about-upload');
				},
				fail : function(){
					blockPage.unblock();
				}
			});
			$('#about-edit-area').on('click', 'i', function(){
				$(this).parent().remove();
			});
		},
		update : function(){
			$('#about-cont, #btn-update-about').hide();
			$('#footer').css('bottom', '-100px');

			// init exist attachments list
			// 오래 머물러 있을 수 있으므로 수정을 했을 때 최신 정보를 다시 불러온다.
			$.get('/profile/read_about', {}, 'json').done(function(responseJSON){
				$('#about-edit-area').show().children('textarea').val(responseJSON.row.contents).wysihtml5(); // 현재의 값을 배치하고 위지윅에디터 활성화
				$.each(responseJSON.row.attachments, function(k,v){
					profileUtil.about.createAttachmentBlock(v).insertBefore('#about-upload');
				});
			});

		},
		cancel : function(){
			location.reload();
		},
		submit : function(){
			var contents = $('#about-edit-area > textarea').val();
			var attachments = [];
			$('#about-attachments > .about-attachment-li').each(function(){
				attachments.push(this.id.replace('attach-', ''));
			});
			$('#about-edit-area > textarea').val();
			console.log('contents', contents);
			contents = br2nl(contents);
			$.post('/profile/update_about', {
				contents : contents,
				attachments : attachments
			}, 'json').done(function(responseJSON){
				if(responseJSON.status=='done'){
					profileUtil.about.cancel();
				}
			});
		},
		createAttachmentBlock : function(r){
			return $('<li class="about-attachment-li" id="attach-'+r.upload_id+'"><i class="spi spi-close3">close3</i><img src="'+r.src+'"/></li>');
		},
		removeAttachment : function(){

		}
	}
};










