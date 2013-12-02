var Browser = {
  Version: function() {
    var version = 99; // we assume a sane browser
    if (navigator.appVersion.indexOf("MSIE") != -1)
      // bah, IE again, lets downgrade version number
      version = parseFloat(navigator.appVersion.split("MSIE")[1]);
    return version;
  }
}
var isIE    = Browser.Version() !== 99;
var isIEVer = Browser.Version();
var ltIE8   = isIE && Browser.Version() < 	8 	? 	true 	: 	false;
var lteIE8  = isIE && Browser.Version() <= 	8 	? 	true 	: 	false;
var lteIE9  = isIE && Browser.Version() <= 	9 	? 	true 	: 	false;
var lteIE10 = isIE && Browser.Version() <= 	10 	? 	true 	: 	false;
var isAndroid = navigator.userAgent.toLowerCase().indexOf('android')!=-1 ? true : false;
if(lteIE8){
	 document.createElement('header');
	 document.createElement('nav');
	 document.createElement('menu');
	 document.createElement('section');
	 document.createElement('article');
	 document.createElement('aside');
	 document.createElement('footer');	
}
var empty = function(v){
     if(typeof v=='undefined')
          return true;
     if(v==null)
          return true;
     if(typeof v == 'string'){
          if(v=='')
               return true;
     }else if(typeof v == 'object'){
          if(v.length == 0)
               return true;
     }
     return false;
}

var valsl = [
	{
		nameSpace : ''
	},
	{
		nameSpace : ''
	}
];
var mcl=1;
/* HTML에서 붙여넣을 것들
	<script>
		var isIE = false;
		var lteIE7 = false;
		var lteIE8 = false;
		var lteIE9 = false;
		var isMobile = $(window).width() < 720 ? true : false;
	</script>
	<!--[if IE]><script type="text/javascript">isIE = true;</script><![endif]-->	
	<!--[if lte IE 7]><script type='text/javascript'>lteIE7 = true;</script><![endif]-->
	<!--[if lte IE 8]><script type='text/javascript'>lteIE8 = true;</script><![endif]-->
	<!--[if lte IE 9]><script type='text/javascript'>lteIE9 = true;</script><![endif]-->
*/


/**
 *	대상객체의 w, h와 l, t를 반환함.
 * 	o : 대상객체, string이나 object 모두 가능, 비어있으면 현재 스크린을 기점으로 계산함.
**/
var objectWHLT = function(o){
	var r = {};
	if(o==''){
		o = $(window);
		// 화면의 중앙을 표시지점으로 찾음.
		r.l = o.scrollLeft();
		r.t = o.scrollTop();
		r.w = o.width();
		r.h = o.height();
	}else{
		// 대상 객체의 중앙지점을 찾음.
		if(typeof(o)=='string')
			o = $(''+o);
		else
			o = $(o);
		if(o.length ==0) return false;
		
		r.l = o.offset().left;
		r.t = o.offset().top;
		r.w = o.outerWidth();
		r.h = o.outerHeight();
	}
	o = null;
	return r;
};

// r에 기반하여 렌더링할 좌표값 생성하기(w와 h도 같이 반환)
var objectRenderWHTL = function(r, w, h){
	var rlt = {};

	var win = objectWHLT('');

	if(r==''){ // 중앙정렬임.
		rlt.w = w;
		rlt.h = h;
		rlt.l = (win.w - w)/2;
		rlt.t = (win.h - h)/2 + win.t - 20;
		r = w = h = null;
	}else{
		r.w = w;
		r.h = h;
		rlt.t = r.t + r.h/2 - h/2 - 30;
		rlt.l = r.l + r.w/2 - w/2;
		w = h = r = null;
	}
	// 창이 스크린 밖으로 나가는 것을 조절함.
	if (rlt.l + rlt.w + 20 > win.l + win.w) rlt.l = win.l + win.w - rlt.w - 20;
	if (rlt.l < win.l+10) rlt.l = win.l + 10;
	if (rlt.t+rlt.h+20 > win.t + win.h) rlt.t = win.t + win.h - rlt.h - 20;	
	if (rlt.t < win.t+20) rlt.t = win.t+20;
	
	win = null;
	return rlt;
};
/**
 *	대상 객체를 특정 객체로 이동 시킨다.
 * 	type : o1과 o2의 이동방향. 'out' 이면 o1->o2로 가면서 사라짐. 'in'이면 o1이 o2로부터 생성되면서 나타남.
 *	o1 : 현재의 객체, string이나 object모두 가능. 비어있으면 현재의 modal창을 대상으로 한다.
 *	o2 : 에니메이션 되면서 사라질 객체
 *	callback : 애니메이션 완료 후 실행될 콜백함수, 문자열화된 함수
 *	doFocus : callback과 상관없이 에니메이션 후 선택을 할 것인지 여부. 'y'인 경우에는 실행. 없으면 ''
**/
var objectTransition = function(type, o1, o2, callback, doFocus, r1, r2){

	var objType = 'modal';
	if(typeof(o1)=='string' && o1==''){ 
		// 아무것도 지정되어 있지 않다면 현재의 modal을 대상으로 함.
		// 열릴때의 에니메이션이나 닫힐때의 에니메이션 둘 다 할 수가 있음.
		o1 = $('div#modal_frame_'+mcl);
		o1.children('div.modal_contents').hide(); // 깔끔한 에니메이션 위해서 안의 내용을 비워둔다.
		if(type=='out'){
			$('#modal_overlay_'+mcl).hide();
		}
	}else{
		objType = 'formFeedback';
		// modal 이외의 다른 객체 에니메이션인데 현재까지는 피드백 밖에 없음.
		if(typeof(o1)=='string')
			o1 = $(''+o1);
		else
			o1 = $(o1);
		o1.children('img').css('visibility','hidden');
	}
	if(typeof(o2)=='string')
		o2 = $(''+o2);
	else
		o2 = $(o2);
		
	if(typeof(r1)!='object')
		var r1 = objectWHLT(o1);
	if(typeof(r2)!='object')
		var r2 = objectWHLT(o2);
	if(r1 == false || r2 == false){
		o1.fadeOut(function(){
			$(this).remove();
		});
	}
	
	var toOpacity = 0;
	if(type=='in'){
		o1.width(r2.w).height(r2.h).css({'left':r2.l, 'top':r2.t, 'opacity' : 0});
		toOpacity = 1;
		r2 = r1;
	}
	if($(window).width() < 767)
		speedOut = 0;
	else{
		if(objType=='formFeedback'){
			var speedOut = 70;
		}else{
			if(type=='in'){
				var speedOut = lteIE9?0:170;
			}else{ // out
				var speedOut = 250;
			}
		}
	}

	o1.animate({
		"opacity" : toOpacity,
		"height" : r2.h,
		"width" : r2.w,
		"left" : r2.l,
		"top" : r2.t
	}, speedOut, function(){
		if(type=='in'){
			// 숨겨두었던 내용을 재표시한다.
			if(objType == 'modal')
				$(this).show().children('div.modal_contents').show();
		}else{
			$(this).remove(); // o1을 사라지게 한다.
			if(typeof(doFocus)!='undefined' && doFocus=='y')
				o2.focus().select();
		}
		if(typeof(callback)!='undefined' && callback!=''){
			if(typeof callback=='string')
				eval(callback);
			else
				callback();
		}
		r1 = r2 = o1 = o2 = callback = doFocus = toOpacity = objType = type = speedOut = null;
	});
};

/**
 *	-------  완료 , 실패, 알림 등 modal.alert 말고 임시적이고 간단하게 쓰이는 경우에 사용한다. -------
 *	f : 피드백창을 표시할 기준 객체, 폼이 아니어도 된다. string이나 object 둘다 허용한다. '#form' or $('#form). 없으면 ''.
 *	type : 아이콘을 표시할 타입. success, error, info 등으로 구분된다. 없으면 ok로 인식한다.
 *	msg : 창에 표시할 메시지, 없으면 아예 표시하지 않으면 type 아이콘만 표시한다. 없으면 ''.
 *	tObj : formFeedback표시 후에 에니메이션으로 이동할 대상 객체, string이나 object둘다 허용.'#form' or $('#form). 없으면 ''.
 *	doFocus : 해당 객체로 이동한 후에 focus를 실행할 것인지 여부. 없으면 ''.
 *	callback : formFeedback 표시 완료후에 실행할 것. 없으면 제외하거나 ''.
**/
var interval = '';
/* 메시지 창에 관한 것. */
var formFeedback = function(f, type, msg, tObj, doFocus, callback){

	if(formFeedback.tmp != null) return false;
	
	// object의 w, h, l, t를 구한다. formFeedback을 엘리먼트의 중앙지점을 기반으로 계산하여 표시를 한다.
	
	var background = 'green';
	if(type.indexOf('-')>0){ // 분리기호가 들어갔다면 뒤에는 배경색 지정이다.
		var type_background = type.split('-')[1];
		type = type.split('-')[0];
	}
	if(typeof(type)=='undefined'){
		var type = 'success';
	}
	switch(type){
		case 'error': background = '#a70000'; break;
		case 'info' : background = '#b07014'; break;
	}
	if(typeof type_background!='undefined')
		background = type_background;

	
	var whtl = {};
	whtl.w = msg!=''?300:180;
	whtl.h = msg!=''?120:80;
	// formFeedback의 최종 포지션을 구한다.
	whtl = objectRenderWHTL('', whtl.w, whtl.h);
	
	// html에 formFeedback을 추가한다.
	if($('#modal_formFeedback').length > 0) $('#modal_formFeedback').remove();
	$('body').append("<div id='modal_formFeedback' onclick='$(this).remove();' style='width:"+whtl.w+"px;left:"+whtl.l+"px;top:"+whtl.t+"px;background-color:"+background+";'><div class='icon48 "+type+"'></div><div style='padding:0 10px 15px 10px;'>"+msg+"</div></div>");
	f = type = background = whtl = null;
	
	formFeedback.tmp = {
		'formFeedback_target' : tObj,
		'doFocus' : doFocus,
		'callback' : callback
	};
	interval = setTimeout(function(){
		if(!empty(formFeedback.tmp.formFeedback_target)){ // 대상 객체가 있다면 에니메이션으로 하면서 callback을 같이 넘김.
			objectTransition('out', 'div#modal_formFeedback', formFeedback.tmp.formFeedback_target, formFeedback.tmp.callback, formFeedback.tmp.doFocus);
		}else{
			// 표시 후 이동할 객체가 없다면 서서히 사라지기.
			$('#modal_formFeedback').fadeOut('fast', function(){
				$(this).remove();
			});
		}
		formFeedback.tmp = null;
	}, (msg=='' ? 700 : 1400) );
	tObj = msg = doFocus = callback = null;
};



var blockObj = {
	'block' : function(nick, tar, color){
		if(typeof(color)=='undefined')
			color = '';
		if(typeof(tar)=='string'){
			tar = $(''+(tar=='' ? '#modal_frame_'+mcl+' > div.modal_contents' : tar));
		}else if(typeof(tar)=='object')
			tar = $(tar);
		if(tar.length==0) return;
		var t_position = (tar.css('position')=='fixed'?'fixed':'absolute');
		if(t_position=='absolute')
			var top = tar.offset().top;
		else
			var top = tar.offset().top - getTop();
		$('#block_div').append("<div class='block' rel='b"+mcl+"' id='block_"+nick+"' style='position:"+t_position+";background:"+color+";"+(color!=''?"border:0;z-index:"+mcl+"51":"z-index:"+mcl+"40")+";top:"+top+"px;left:"+tar.offset().left+"px;width:"+tar.outerWidth()+"px;height:"+(tar.outerHeight()+1)+"px;'><img src='"+common_assets+"/img/ajax-loader.gif' style='margin-top:"+(tar.outerHeight()/2-10)+"px;'/></div>");
	
	},
	'unblock' : function(nick){
		$('div#block_'+nick).remove();
	}
};

var blockPage = {
	headerHeight : 0, // site에서 지정
	opacity : 0.5, // site에서 지정
	text : 'Loading',
	block : function(){
		if(ltIE8) return;
		if($('#pageBlock').length) return;
		$('body').css('cursor', 'wait');
		$('<div id="pageBlock" style="position:fixed; left:0; top:'+this.headerHeight+'px; background:#fff; width:100%; height:100%; z-index:1040;"></div>').appendTo('body').delay(1000).css('opacity', this.opacity);
		$('<div id="pageBlockBar" style="display:none; text-align:center; width:100%; position:fixed; left:0; top:45%; z-index:1041;font-size:15px;color:gray;"><div><span style="background:white;">'+this.text+'</span></div><div id="pageBlockChar" style="font-size:30px;font-weight:bold;letter-spacing:3px;">.</div></div>').appendTo('body').fadeIn('slow');
		this.interval = setInterval(function(){
			var o = $('#pageBlockChar');
			var t = o.html();
			if(o.html().length > 20)
				o.html('.');
			else
				o.html(t+'.');
		}, 200);
		$(window).bind('pageshow', function() {	
			blockPage.unblock();
		});	
	},
	interval : null,
	unblock : function(){
		if(ltIE8) return;
		$('body').css('cursor', 'default');
		clearInterval(this.interval);
		this.interval = null;
		$('#pageBlock, #pageBlockBar').remove();
	}
};

/* 
	정상적이지 않은 값이 들어오면 해당 인풋창을 강조해준다 
	errors는 이와 같은 형식 -> {"email":"The email field is required.","tel":"The tel field is required."}
*/

var formValidation = function(o, errors){
	if(typeof o=='string')
		o = $(o);
	var keys = [];
	for(var i in errors)
		keys.push(i);
	o.find('*[name='+keys.join('],*[name=')+']').addClass('form_error');
};


function delete_object_by_key(k, obj){
	var new_obj = {};
	for(var i in obj){
		if(i != k){
			new_obj[i] = obj[i]
		}
	}
	return new_obj;
}
function delete_array_by_key(k, arr){
	var new_arr = [];
	for(var i in arr){
		if(i != k){
			new_arr.push(arr[i]);
		}
	}
	return new_arr;
}
function delete_array_by_value(v, arr){
	var index = -1;
	for(var i in arr){
		if(v == arr[i]){
			index = i;
			break;
		}
	}
	if(index != -1){
		arr.splice(index,1);
	}
	return arr;
}


function jumpObj(tar, callback){
	if(typeof(tar)=='string')
		tar = $(''+tar);
	else if(typeof(tar)=='object')
		tar = $(tar);
	tar.css('position','relative').animate({ top : '-8px'}, 250).animate({ top : '0px'}, 120).animate({ top : '-5px'}, 120).animate({ top : '-0px'}, 90).animate({ top : '-3px'}, 60).animate({ top : '0px'}, 30);
	tar = null;
};



// made by gumoisland.com
jQuery.fn.extend({
 anchorTar : '',
 anchorLink: function() {
	this.bind('click', function(e){
		e.preventDefault();
		var t= $(this).attr('href').split('#');
		if(typeof t[1]=='undefined' || $('#'+t[1]).length == 0){
			location.href = $(this).attr('href');
			return;
		}
		$.fn.anchorTar = $('#'+t[1]);
		var top = $.fn.anchorTar.offset().top - 50 - blockPage.headerHeight;
		var delay = 0.2 * Math.abs(top - document.body.scrollTop);
	 	$('html,body').animate({
	 		scrollTop: top
	 	}, delay, function(){
			$.fn.anchorTar.focus();
		});
	});
 }
});





