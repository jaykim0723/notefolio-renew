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
			alert('alarm');
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