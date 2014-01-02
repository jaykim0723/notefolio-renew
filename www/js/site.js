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
			console.log($.now());
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
	
	open : function(t){
		var $work = $(t).parents('.work-wrapper');
		if($work.data('comment_opened')=='y'){ // 현재 코멘트창이 열려있다면 닫아준다(같은 버은으로 토글)
			this.close($work);
			return;
		}
		if($work.data('comment_loaded')=='y'){ // 이미 한 번 열린 놈이라면 그냥 단순히 보여주기만 한다.
			$('.comment-wrapper', $work).show();
			return;
		}
		$work.data('comment_loaded', 'y').data('comment_opened', 'y'); // 다음의 코멘트열기 버튼에 대응하기 위하여 값을 지정해준다.

		$('.comment-wrapper', $work).show();
		var work_id = $work.data('id');
		// call list and insert into wrapper
		$.when(commentUtil.getList(work_id, '')).then(function(d){ // 리스트를 불러와서 '이전보기' 버튼 뒤에 배치하기
			$('.comment-prev', $work)[$(d).find('.comment-block').length<10?'hide':'show']().after(d);
		});

	},
	prev : function(t){
		var $work = $(t).parents('.work-wrapper');	
		var work_id = $work.data('id');
		// get latest comment_id
		var idBefore = $('.comment-block:first', $work).data('id'); // 가장 마지막에 불러들인 코멘트의 번호를 가지고 와서 작업
		$.when(this.getList(work_id, idBefore)).then(function(d){
			$('.comment-prev', $work)[$(d).find('.comment-block').length<10?'hide':'show']().after(d);
		});
	},
	getList : function(work_id, idBefore){
		// get id_before
		$.get(site.url+'comment/get_list/'+work_id,  {
			id_before : idBefore
		}, function(d){
			return d;
		});
	},


	getComment : function(){
		$.get(site.url+'comment/get_info/'+work_id+'/'+comment_id, {

		}).done(function(d){

		}).fail(function(d){

		});

	},
	update : function(o){
		
	},
	reply : function(o){
		// 표준 form을 떼어다가 들어갈 위치에 삽입을 해준다.
		var $wrapper = $(t).parents('.comment-wrapper');
		// $wrapper

	},
	delete : function(o){

	},
	submitComment : function(f){
		event.preventDefault();
		event.stopPropagation();

		$f = $(f);
		var params = $f.serialize();

		var $work = $f.parents('.work-wrapper');	
		var work_id = $work.data('id');

		blockObj.block('comment-form-'+work_id, $f);
		$.post(site.url+'comment/post/'+work_id, params, function(d){
			console.log(d);
		}, 'json');
	},


	close : function($work){
		$('.comment-wrapper', $work).hide(); // 추후에 다시 열릴 것을 감안하여 숨겨만 준다.
	}

};