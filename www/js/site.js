var site = {
	redirect : function(url, msg){
		if(typeof msg!=='undefined'){ // 이동된 이후에 출력할 성공 메시지가 있다면 지정
			localStorage.setItem('flashMsg', JSON.stringify({
				time : $.now(),
				url : url,
				msg : msg,
			}));
		}
		location.href = url;
	},
	checkFlashMsg : function(){
		if(!empty(localStorage.getItem('flashMsg'))){
			var t = JSON.parse(localStorage.getItem('flashMsg'));
			if(
				$.now() - parseInt(t.time,10) < 10000  // 메시지가 생성된 이후에 10초가 지나지 않았어야 하고
				&& 
				location.href.indexOf(t.url)>-1 // 목표 url을 포함하여야 한다
			){
				formFeedback('', 'success', t.msg);
			}
			localStorage.removeItem('flashMsg');
		}
	},
	alarm : {
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
	},
	scroll : {
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
	},
	cache : {},
	loadHTML : function(val){
		return site.cache[ val ]|| $.ajax(site.url + val, {		
        	success:function( resp ){
        		site.cache[ val ]= resp;
        	}
       	});
       	// use $.when(site.loadHTML('url')).then(function(resp){ });
	}
};
site.checkFlashMsg(); // 페이지가 전환된 이후에 메시지를 표시할 것이 있는지 검사




$(window).on('beforeunload', function(){
	localStorage.setItem('prevPage', JSON.stringify({
		top : $(window).scrollTop(),
		url : location.href
	}));
});
site.prevPage = empty(localStorage.getItem('prevPage')) ? {top:0, url:''} : JSON.parse(localStorage.getItem('prevPage'));


$(function() {
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
		  		$o.css({'position':'fixed', 'top':'70px'});
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


    $(document).on('mouseenter', '.btn-hover', function(){
    	$(this).find('.spi, .si').each(function(){
	    	$(this).attr('class', $(this).attr('class')+'_hover');
    	});
    }).on('mouseleave', '.btn-hover', function(){
    	$(this).find('.spi, .si').each(function(){
	    	$(this).attr('class', $(this).attr('class').replace('_hover',''));
    	});
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
			$('.btn-comment-prev', $work)[mode]().after($o);
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
			$('.btn-comment-prev', $work)[mode]().after($o);
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
		var $work = $commentBlock.parents('.work-wrapper');	
		var work_id = $work.data('id');
		// console.log($commentBlock, work_id);

		var $f = commentUtil.formHTML.clone();
		$f.find('textarea').val(content);
		$f.data('mode','update');
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
		$f.data('parent_id', $commentBlock.data('id'));
		$f.appendTo($commentReplies);
		$commentReplies.find('textarea:last').focus();
	},

	delete : function(o){
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

		$work.data('note_opened', 'y');

		$btnNote = $('.btn-note', $work);
		if($btnNote.hasClass('noted')){
			// 이미 좋아요를 누른 상태라면 취소한다.
			this.cancel($work);
			return;
		}

		$btnNote.tooltip('show');

		$.post(site.url+'gallery/note', {
			work_id : work_id,
			note : 'y'
		}, function(responseJSON){
			if(responseJSON.status=='done'){
				formFeedback('', 'success', '노트되었습니다.');
				$btnNote.addClass('noted');
			}else
				formFeedback('', 'error', responseJSON.message);
		}, 'json');
	},

	cancel : function($work){
		console.log('site.js > noteUtil > cancel', $work);

		var work_id = $work.data('id');
		$btnNote = $('.btn-note', $work);

		$.post(site.url+'gallery/note', {
			work_id : work_id,
			note : 'n'
		}, function(responseJSON){
			if(responseJSON.status=='done'){
				formFeedback('', 'success', '노트가 취소되었습니다.');
				$btnNote.removeClass('noted');
			}else
				formFeedback('', 'error', responseJSON.message);
		}, 'json');

	},

	close : function($work){
		console.log('site.js > noteUtil > close', $work);

		if($work.data('note_opened')=='n') return;
		$('.note-wrapper', $work).hide(); // 추후에 다시 열릴 것을 감안하여 숨겨만 준다.
		$work.data('note_opened', 'n');
	}
};

var snsUtil = {
	newPop : function(url, w, h){
		encodeURI(url.split("#").join("%23"));
		var top = ($(window).height() - h) / 2;	
        var left = ($(window).width() - w) / 2;		
		var options = 'toolbar=no, menubar=no, location=no, scrollbar=yes, status=no, width='+w+', height='+h+', top=' + top + ', left=' + left;
		window.open(encodeURI(url), '', options);
	},

	getInfo : function(o){
		var $work = $(o).parents('.work-wrapper');
		var workInfo = {};
		workInfo.id = $work.data('id');
		workInfo.url = $('.work-url', $work).text();
		workInfo.title = $('.work-title', $work).text();
		workInfo.cover = '/data/covers/'+workInfo.cover+'-t2.jpg';
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

	pinterest : function(){
		console.log('site.js > snsUtil > pinterest');

	},

	tumblr : function(){
		console.log('site.js > snsUtil > tumblr');

	},

	path : function(){
		console.log('site.js > snsUtil > path');
		
	}
};

var collectUtil = {
	add : function(){

	},
	delete : function(){
		
	}
};