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

    $(document).on('mouseenter', '.hover-enabled', function(){
    	$o = $(this).find('.spi');
    	$o.attr('class', $o.attr('class')+'_hover');
    }).on('mouseleave', '.hover-enabled', function(){
    	$o = $(this).find('.spi');
    	$o.attr('class', $o.attr('class').replace('_hover',''));
    });

});