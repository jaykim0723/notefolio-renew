var msg = {
	open : function(msg, type){
		if(typeof type=='undefined')
			type = 'success';
		formFeedback('', type, msg);
	},
	close : function(){

	}
};

site.redirect = function(url, msg){
	if(typeof msg!=='undefined'){ // 이동된 이후에 출력할 성공 메시지가 있다면 지정
		localStorage.setItem('flashMsg', JSON.stringify({
			time : $.now(),
			url : url,
			msg : msg,
		}));
	}
	location.href = url;
};
site.scrollToBottom = function(obj){
	if(typeof obj!='undefined')
		var top = $(obj).offset().top - 40;
	else{
		var top = $(document).height();
	}
	var delay = 0.5 * Math.abs(top - document.body.scrollTop);
	return $('html,body').stop().animate({scrollTop: top}, delay);
};
site.checkFlashMsg = function(){
	if(!empty(localStorage.getItem('flashMsg'))){
		var t = JSON.parse(localStorage.getItem('flashMsg'));
		if(
			$.now() - parseInt(t.time,10) < 10000  // 메시지가 생성된 이후에 10초가 지나지 않았어야 하고
			&& 
			location.href.indexOf(t.url)>-1 // 목표 url을 포함하여야 한다
		){
			msg.open(t.msg);
		}
		localStorage.removeItem('flashMsg');
	}
};

site.alarm = {
	checkUnread : function(){
		$.getJSON('/feed/check_unread').done(function(d){
			console.log(d);
			if(d.status=='done'){
				$('.unread-alarm').text(d.alarm_unread)[d.alarm_unread>0?'show':'hide']();
				$('.unread-feed').text(d.feed_unread)[d.feed_unread>0?'show':'hide']();
				setTimeout(function(){
					site.alarm.checkUnread();
				}, 30000);
			}
		});
	},
	open : function(){
		if($('#alarm-popup').length > 0){
			this.close();
			return;
		}
		$('#alarm-wrapper').append([
			'<div id="alarm-popup">',
				'<div id="alarm-popup-unread"></div>',
				'<div id="alarm-popup-list"></div>',
			'</div>'
		].join('')).on({
			mouseenter : function(){
				$(document).off('click.alarm');
			},
			mouseleave : function(){
				$(document).one('click.alarm', function(){
					site.alarm.close();
				})
			}
		})
		.children('div').on({
			mouseenter : function(){
				site.scroll.lock();
			},
			mouseleave : function(){
				site.scroll.unlock();
			}
		})
		.height($(window).height()>550 ? 500 : $(window).height()-100)
		.children('#alarm-popup-list')
		.load('/alarm/listing/1');


		},
	close : function(){
		site.scroll.unlock(); // 혹시 몰라서 다시 한 번
		$('#alarm-popup').remove();
	}
};
site.scroll = {
	lock : function(){
		 var scrollPosition = [self.pageXOffset || document.documentElement.scrollLeft || document.body.scrollLeft, self.pageYOffset || document.documentElement.scrollTop  || document.body.scrollTop ];      
		 var html = jQuery('html');
		 html.data('scroll-position', scrollPosition);
		 html.data('previous-overflow', html.css('overflow'));
		 html.css('overflow', 'hidden');
		 window.scrollTo(scrollPosition[0], scrollPosition[1]);
	},
	unlock : function(){
		var html = jQuery('html');
		var scrollPosition = html.data('scroll-position');
		if(empty(scrollPosition)) return;
		html.css('overflow', html.data('previous-overflow'));
		window.scrollTo(scrollPosition[0], scrollPosition[1]);
	}
};
site.cache = {};
site.loadHTML = function(val){
	return site.cache[ val ]|| $.ajax(site.url + val, {		
    	success:function( resp ){
    		site.cache[ val ]= resp;
    	}
   	});
   	// use $.when(site.loadHTML('url')).then(function(resp){ });
};
site.requireLogin = function(){
	BootstrapDialog.confirm('회원만이 이용가능한 기능입니다. 지금 로그인 하시겠습니까?', function(result){
        if(result) {
            site.redirect(site.url+'auth/login?go_to='+location.href);
        }
    });
};
site.popWorkList = function(opts){
	var defaults = {
		title : 'Someone\'s gallery',
		username : '',
		work_id : '',
		sub : '',
		id_before : '',
		id : 'ajax-dialog-wrapper',
		done : function(dialog){
			console.log('site > popWorkList > done', dialog);
            dialog.close();
			site.scroll.unlock();
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
					site.scroll.unlock();
		        }
		    }
	    ]
	});
	dialog.realize();
	dialog.getModal().prop('id', options.id); // cssClass 버그로 인해서 이 꼼수로..
	dialog.open();
	site.scroll.lock();

	// call list
	$.when($.get('/profile/my_pop_recent_works/'+options.username+'/'+options.id_before, {}, function(d){return d;})).done(function(d){
		dialog.getModalBody().html(
			$('<div>').addClass('dialog-work-list-wrapper').html(
				$('<ul>')
				.addClass('work-list-ul')
				.addClass('dialog-work-list')
				.html(
					d
				)
			).append(
				$('<button class="btn btn-more btn-default btn-block">')
				.html('more...')
				.on('click', function(){
					var id_before = $('li:last', '#'+options.id).prop('id').replace('work-recent-', '');
					$.when($.get('/profile/my_pop_recent_works/'+options.username+'/'+id_before, {}, function(d){return d;})).done(function(d){
						if(d==''){
							$('button.btn-more', '#'+options.id).remove();
						}else{
							$(d).appendTo('#'+options.id+' ul');
						}
					});
				})
			)
		);
		return dialog;
	});
};
	
site.checkFlashMsg(); // 페이지가 전환된 이후에 메시지를 표시할 것이 있는지 검사




/* live binding */
$(window).on('beforeunload', function(){
	localStorage.setItem('prevPage', JSON.stringify({
		top : $(window).scrollTop(),
		url : location.href
	}));
});
site.prevPage = empty(localStorage.getItem('prevPage')) ? {top:0, url:''} : JSON.parse(localStorage.getItem('prevPage'));
$(document).on('click', '.btn-follow', function(){

	if(site.user_id==0){
		return site.requireLogin();
	}
	var $o = $(this);
	var data = {
		user_id : $o.data('id'),
		follow : $o.hasClass('activated') ? 'n' : 'y'
	};
	$.post(site.url+'profile/follow_action', data, function(d){
		console.log($o, d);
		$o[(d.is_follow == 'y' ? 'add' : 'remove')+'Class']('activated').find('span').html(d.is_follow == 'y' ? 'Following' : 'Follow');
	}, 'json');
});


$(function() {
	$('body').tooltip({
	    selector: '[rel=tooltip]',
	    placement : 'bottom'
	});

	$('.infinite-list').waypoint('infinite', {
		items: '.infinite-item',
		more: '.more-link',
		offset: 'bottom-in-view',
		onAfterPageLoad : function(){
			if(typeof NFview.infiniteCallback!=='undefined'){
				NFview.infiniteCallback();
			}
		}
	});
	$('.sticky').waypoint('sticky', {
	  stuckClass: 'stuck',
	  handler : function(direction){
	  	if(!empty(NFview.area) && (NFview.area=='work-info' || NFview.area=='work-form')){
		  	var $o = $('#work-sidebar');
		  	if(direction=='up')
		  		$o.css({'position':'absolute', 'top':'20px'});
		  	else
		  		$o.css({'position':'fixed', 'top':'80px'});
	  	}
	  }
	});

	$('#mobile-menu').mmenu({
    	dragOpen: {
			open:	false,
			pageNode:	null,
			threshold:	50,
			maxStartPos:	150,
		}
    })

    $('#btn-alarm').on('click', function(){
    	site.alarm.open();
    });


    $(document).on('mouseenter', '.btn-nofol', function(){
    	$(this).find('.spi, .si').each(function(){
	    	$(this).prop('class', $(this).prop('class')+'_point');
    	});
    }).on('mouseleave', '.btn-nofol', function(){
    	$(this).find('.spi, .si').each(function(){
	    	$(this).prop('class', $(this).prop('class').replace('_point',''));
    	});
    }).on('click', '.dialog-work-list-wrapper li', function(){
		if($(this).hasClass('disabled')) return;
		$(this).parent().children('.selected').removeClass('selected');
		$(this).addClass('selected');
	});


});




var commentUtil = {
	// 코멘트 작업은 대부분 무한스크롤 내의 특정 블럭에서 이루어지므로, 
	// 코멘트에 관련된 모든 작업은 해당 work-wrapper 블럭의 dom 요소에 저장해두는 방식을 취한다.
	
	formHTML : null,

	open : function($work){
		console.log('site.js > commentUtil > open', $work);

		// var $work = $('#work-'+work_id);
		var work_id = $work.data('id');
		if($work.data('comment_opened')=='y'){ // 현재 코멘트창이 열려있다면 닫아준다(같은 버은으로 토글)
			console.log('이미 열려있다.');
			this.close($work);
			return;
		}
		$work.data('comment_opened', 'y'); // 다음의 코멘트열기 버튼에 대응하기 위하여 값을 지정해준다.

		if($work.data('comment_loaded')=='y'){ // 이미 한 번 열린 놈이라면 그냥 단순히 보여주기만 한다.
			console.log('이미 로딩되어 있다.');
			$('.comment-wrapper', $work).show();
			return;
		}
		$work.data('comment_loaded', 'y');

		// 활용이 많이 되므로 백업을 해둔다.
		if(this.formHTML == null){
			commentUtil.formHTML = $('form', $work).clone();
		}
		

		$('.comment-wrapper', $work).show();
		// call list and insert into wrapper
		$.when(commentUtil.readList(work_id, '')).then(function(responseHTML){ // 리스트를 불러와서 '이전보기' 버튼 뒤에 배치하기
			var $o = $('<div>'+responseHTML+'</div>');
			if($o.find('.comment-block').length<11)
				var mode = 'hide';
			else{
				var mode = 'show';
				$o.children('.comment-block:last').remove();
			}
			console.log($o);
			$('.btn-comment-prev', $work)[mode]().after($o.html());
		});

	},

	prev : function(o){
		console.log('site.js > commentUtil > prev', o);

		var $work = $(o).parents('.work-wrapper');	
		var work_id = $work.data('id');
		// get latest comment_id
		var idBefore = $('.comment-block:first', $work).data('id'); // 가장 마지막에 불러들인 코멘트의 번호를 가지고 와서 작업
		$.when(this.readList(work_id, idBefore)).then(function(responseHTML){
			var $o = $('<div>'+responseHTML+'</div>');
			if($o.find('.comment-block').length<11)
				var mode = 'hide';
			else{
				var mode = 'show';
				$o.children('.comment-block:last').remove();
			}
			console.log($o);
			$('.btn-comment-prev', $work)[mode]().after($o.html());
		});
	},

	readList : function(work_id, idBefore){
		console.log('site.js > commentUtil > readList', work_id, idBefore);

		// get id_before
		return $.ajax({
			type : 'get',
			url : site.url+'comment/read_list/'+work_id,
			data : {
				id_before : idBefore
			}
		});
	},


	// readComment : function(){
	// 	$.get(site.url+'comment/get_info/'+work_id+'/'+comment_id, {

	// 	}).done(function(d){

	// 	}).fail(function(d){

	// 	});

	// },
	update : function(o){
		console.log('site.js > commentUtil > update', o);
		// 기존의 폼을 가지고 와서
		var $commentInner = $(o).closest('.comment-inner');
		var $commentBlock = $commentInner.closest('.comment-block');
		var content = $.trim($commentBlock.find('.comment-textarea').html());
		contents = br2nl(contents);
		var $work = $commentBlock.parents('.work-wrapper');	
		var work_id = $work.data('id');
		// console.log($commentBlock, work_id);

		var $f = commentUtil.formHTML.clone();
		$f.find('textarea').val(content);
		$f.data('mode','update');
		$f.removeClass('create');
		$f.data('comment_id', $commentBlock.data('id'));
		$f.insertAfter($commentInner);
		$commentInner.hide().next().find('textarea').focus();
	},

	reply : function(o){
		console.log('site.js > commentUtil > reply', o);

		var $commentBlock = $(o).closest('.comment-block');
		var $commentReplies = $('.comment-replies', $commentBlock);
		var $work = $commentBlock.parents('.work-wrapper');	
		var work_id = $work.data('id');
		
		// 이미 form이 존재하는지 검사하기
		var exist = false;
		$commentReplies.find('form').each(function(){
			if($(this).data('mode')=='reply'){
				exist = true;
				return;
			}
		});
		if(exist) return; // reply form이 이미 존재하는 상태라면 추가로 열지 않기

		var $f = commentUtil.formHTML.clone();
		$f.find('textarea').val('');
		$f.data('mode','reply');
		$f.removeClass('create');
		$f.data('parent_id', $commentBlock.data('id'));
		$f.appendTo($commentReplies);
		$commentReplies.find('textarea:last').focus();
	},

	'delete' : function(o){
		console.log('site.js > commentUtil > delete', o);
		var $work = $(o).parents('.work-wrapper');	
		var work_id = $work.data('id');
		var params = {
			'mode' : 'delete',
			'comment_id' : $(o).parents('.comment-block').data('id')
		};
		BootstrapDialog.confirm('Are you sure?', function(result){
			if(result){
				$.post(site.url+'comment/post/'+work_id, params, function(responseHTML){
					$('#comment-'+params.comment_id).remove();
				});			
			}
		}, 'danger');
		return false;
	},

	cancel : function(o){
		console.log('site.js > commentUtil > cancel', o);

		var $f = $(o).closest('form.comment-block');
		switch($f.data('mode')){
			case 'update':
				$f.prev().show();
				$f.remove();
			break;
			
			case 'reply':
				$f.remove();
			break;
		}
	},

	submitComment : function(f){
		event.preventDefault();
		event.stopPropagation();

		var $f = $(f);
		var params = {
			mode : $f.data('mode'),
			content : $('textarea[name=content]', $f).val(),
			parent_id : $f.data('parent_id'),
			comment_id : $f.data('comment_id')
		}
		console.log(params);
		var $work = $f.parents('.work-wrapper');	
		var work_id = $work.data('id');

		blockObj.block('comment-form-'+work_id, $f);
		$.post(site.url+'comment/post/'+work_id, params, function(responseHTML){
			// 모드에 따라 대처하기
			switch(params.mode){
				case 'create':
					$f.before(responseHTML);
					$('textarea', $f).val('');
				break;

				case 'update':
					var $o = $(responseHTML);
					$o.children('.comment-replies').remove();
					$f.prev().replaceWith($o.html());
					$f.remove();
				break;

				case 'reply':
					$f.before(responseHTML);
					$f.remove();
				break;
			}
		});
	},


	close : function($work){
		console.log('site.js > commentUtil > close', $work);
		if($work.data('comment_opened')=='n') return;
		$work.data('comment_opened', 'n');
		$('.comment-wrapper', $work).hide(); // 추후에 다시 열릴 것을 감안하여 숨겨만 준다.
	}

};
var noteUtil = {
	open : function(o){
		console.log('site.js > noteUtil > open', o);

		var $work = $(o).parents('.work-wrapper');
		var work_id = $work.data('id');


		$btnNote = $('.btn-note', $work);
		if($work.data('noted')=='y'){
			// 이미 좋아요를 누른 상태라면 취소한다.
			this.cancel($work);
			return;
		}
		$work.data('noted', 'y');
		if($work.data('collected')=='n'){ // 취소는?
			$btnNote.next().css('visibility','visible');
		}

		$.post(site.url+'gallery/note', {
			work_id : work_id,
			note : 'y'
		}, function(responseJSON){
			if(responseJSON.status=='done'){
				msg.open('노트되었습니다.');
				$btnNote.addClass('noted');
			}else
				msg.open(responseJSON.message);
		}, 'json');
	},

	cancel : function($work){
		console.log('site.js > noteUtil > cancel', $work);

		$work.data('noted', 'n');
		var work_id = $work.data('id');
		$btnNote = $('.btn-note', $work);
		$btnNote.next().css('visibility','hidden');

		$.post(site.url+'gallery/note', {
			work_id : work_id,
			note : 'n'
		}, function(responseJSON){
			if(responseJSON.status=='done'){
				msg.open('노트가 취소되었습니다.');
				$btnNote.removeClass('noted');
			}else
				msg.open(responseJSON.message);
		}, 'json');

	},

	close : function($work){
		console.log('site.js > noteUtil > close', $work);

		if($work.data('noted')=='n') return;
		$('.note-wrapper', $work).hide(); // 추후에 다시 열릴 것을 감안하여 숨겨만 준다.
		$work.data('noted', 'n');
	}
};

var snsUtil = {
	newPop : function(url, w, h){
		url = url.split("#").join("%23");
		var top = ($(window).height() - h) / 2;	
        var left = ($(window).width() - w) / 2;		
		var options = 'toolbar=no, menubar=no, location=no, scrollbar=yes, status=no, width='+w+', height='+h+', top=' + top + ', left=' + left;
		window.open(url, '', options);
	},

	getInfo : function(o){
		var $work = $(o).parents('.work-wrapper');
		var workInfo = {};
		workInfo.id = $work.data('id');
		workInfo.url = encodeURIComponent($('.work-url', $work).text());
		workInfo.title = encodeURIComponent($('.work-title', $work).text());
		workInfo.cover = '/data/covers/'+workInfo.id+'_t2.jpg?_='+$work.data('moddate');
		workInfo.summary = $.trim($('.work-contents', $work).text().substr(0,100));
		console.log('workInfo', workInfo);
		return workInfo;
	},

	twitter : function(o){
		console.log('site.js > snsUtil > twitter');
		var workInfo = this.getInfo(o);
		this.newPop('https://twitter.com/intent/tweet?original_referer='+workInfo.url+'&text='+workInfo.summary+'&url='+location.href, 620, 310);
	},

	facebook : function(o){
		console.log('site.js > snsUtil > facebook');
		var workInfo = this.getInfo(o);
		this.newPop('http://www.facebook.com/sharer.php?s=100&p[url]=' + workInfo.url + '&p[images][0]=' + workInfo.cover + '&p[title]=' + workInfo.title + '&p[summary]=' + workInfo.summary, 510, 368);
	},

	kakaotalk : function(o){
		console.log('site.js > snsUtil > kakaotalk');

		// 정책적으로 추가가 필요하다면 추가할 수 있음.
		// https://github.com/kakao/kakaolink-web
	},

	pinterest : function(o){
		console.log('site.js > snsUtil > pinterest');
		var workInfo = this.getInfo(o);
		this.newPop('http://pinterest.com/pin/create/button/?url='+workInfo.url+'&media='+workInfo.cover+'&description='+workInfo.summary, 510, 368);
	},

	tumblr : function(o){
		console.log('site.js > snsUtil > tumblr');
		var workInfo = this.getInfo(o);
		this.newPop('http://www.tumblr.com/share?s=&t='+workInfo.title+'&u='+workInfo.url+'&v=3', 510, 368);
	},

	path : function(o){
		console.log('site.js > snsUtil > path');
		
	}
};

var collectUtil = {
	add : function(o){
		console.log('site.js > collectUtil > add');
		var $work = $(o).parents('.work-wrapper');
		var work_id = $work.data('id');
		var $btnCollect = $('.add-collection', $work);
		$.post(site.url+'gallery/collect', {
			work_id : work_id,
			collect : 'y'
		}, function(responseJSON){
			if(responseJSON.status=='done'){
				msg.open('콜렉션에 추가되었습니다.');
				$work.data('collected', 'y');
				$btnCollect.addClass('collected');
			}else
				msg.open(responseJSON.message);
		}, 'json');
	},
	hide : function(o){
		console.log('site.js > collectUtil > hide');

		var $work = $(o).parents('.work-wrapper');
		$('.add-collection', $work).css('visibility','hidden');
	},
	cancel : function(o){
		console.log('site.js > collectUtil > cancel');

		var $work = $(o).parents('.work-wrapper');
		var work_id = $work.data('id');
		var $btnCollect = $('.add-collection', $work);
		$.post(site.url+'gallery/collect', {
			work_id : work_id,
			collect : 'n'
		}, function(responseJSON){
			if(responseJSON.status=='done'){
				msg.open('콜렉트에서 제외되었습니다.');
				$work.data('collected', 'n');
				$btnCollect.removeClass('collected');
			}else
				msg.open(responseJSON.message);
		}, 'json');
	}
};



var workInfoUtil = {
	setGround : function(){
		$('#work-info-wrapper').on('submit', 'form.comment-block', function(){
			commentUtil.submitComment(this);
		}).on('click', '.btn-open-comment', function(){
			commentUtil.open(this);
		}).on('click', '.btn-delete-comment', function(){
			commentUtil['delete'](this);
		}).on('click', '.btn-update-comment', function(){
			commentUtil.update(this);
		}).on('click', '.btn-reply-comment', function(){
			commentUtil.reply(this);
		}).on('click', '.btn-cancel-comment', function(){
			commentUtil.cancel(this);
		}).on('click', '.btn-comment-prev', function(){
			commentUtil.prev(this);
		}).on('click', '.btn-note', function(){
			noteUtil.open(this);
		}).on('click', '.btn-add-collect', function(){
			collectUtil.open(this);
		}).on('click', '.btn-cancel-collect', function(){
			collectUtil.close(this);
		});
	},
	getRecentList : function(work_id){
		console.log('site.js > workInfoUtil > getRecentList', work_id);

		// 지금 막 불러온 것이 사이드바에서 마지막인지 확인을 해보고,
		// 마지막이라면 리스트 불러와서 추가해주기
		var $workRecentList = $('#work-recent-list');
		var idBefore = null;
		var isFirst = false;
		if($workRecentList.children('li:last').length > 0){
			idBefore = $workRecentList.children('li:last').attr('id').replace('work-recent-','');
		}else{ // 아직 불려진게 없다면
			idBefore = parseInt(work_id) + 1; // 현재 열린것 이전부터 들여오기
			isFirst = true;
		}
		$('#work-'+work_id).waypoint(function() {
			workInfoUtil.selectRecentList(this.id.replace('work-',''));
		});	

		if(isFirst || idBefore==work_id){
			$.get(site.url+'profile/my_recent_works/'+NFview.username+'/'+idBefore, {
			}).done(function(responseHTML){
				$workRecentList.append(responseHTML);
				// 현재 불려진 놈을 선택하기
				// $workRecentList.children('#work-recent-'+work_id).addClass('selected');
				// $workRecentList.scrollTo($workRecentList.children('#work-recent-'+work_id));
			});
		}else{
			// 현재 불려진 놈을 선택하기
			// $workRecentList.children('#work-recent-'+work_id).addClass('selected');
			// $workRecentList.scrollTo($workRecentList.children('#work-recent-'+work_id));
		}
	},
	selectRecentList : function(work_id){
		var $workRecentList = $('#work-recent-list');
		if($workRecentList.children('li').length==0) return;
		$workRecentList.children('.selected').removeClass('selected');
		$workRecentList.children('#work-recent-'+work_id).addClass('selected');
		$workRecentList.scrollTo($workRecentList.children('#work-recent-'+work_id));
	},
	initRecentList : function(){
		var top = $('#work-recent-works').offset().top - $(window).scrollTop();
		top -= $('#work-recent-works > h2').outerHeight();
		top += 14; // ? 왜 안맞는지는 모르겠지만 일단 이렇게..
		console.log('top', top); 
		$('#work-recent-list').on({
			mouseenter : function(){
				site.scroll.lock();
			},
			mouseleave : function(){
				site.scroll.unlock();
			}
		}).css('top', top);
	}
};