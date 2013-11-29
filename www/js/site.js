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
					$('.unreadAlarm').text(d.unread)[d.unread>0?'show':'hide']();
					setTimeout(function(){
						site.alarm.checkUnread();
					}, 30000);
				}
			});
		},
		open : function(){
			if($('#alarmPopUp').length > 0){
				this.close();
				return;
			}
			$('#alarmWrapper').append([
				'<div id="alarmPopUp">',
					'<div id="alarmPopUpUnread"></div>',
					'<div id="alarmPopUpList"></div>',
				'</div>'
			].join(''));
			$('#alarmPopUp').on({
				mouseenter : function(){
					site.scroll.lock();
				},
				mouseleave : function(){
					site.scroll.unlock();
				}
			}).children('#alarmPopUpList').load('/alarm/listing/1');
 		},
		close : function(){
			site.scroll.unlock(); // 혹시 몰라서 다시 한 번
			$('#alarmPopUp').remove();
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
			html.css('overflow', html.data('previous-overflow'));
			window.scrollTo(scrollPosition[0], scrollPosition[1]);
		}
	}
};
site.checkFlashMsg(); // 페이지가 전환된 이후에 메시지를 표시할 것이 있는지 검사








$(function() {
	$('.infinite_list').waypoint('infinite', {
		items: '.infinite-item',
		more: '.more-link',
		offset: 'bottom-in-view',
		onAfterPageLoad : function(){
			console.log($.now());
		}
	});

	$('#mobile-menu').mmenu({
    	dragOpen: {
			open:	false,
			pageNode:	null,
			threshold:	50,
			maxStartPos:	150
		}
    })

    $('#btnAlarm').on('click', function(){
    	site.alarm.open();
    });
});