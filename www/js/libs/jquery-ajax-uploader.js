(function( $ ) {
$.fn.ajaxUploader = function(opts) {
	var defaults = {
		// setting your default values for options
		url : '/upload.php',
		droppable : true,
		dropActiveClass : '.drop-active',
		clickElement : 'self',
		multiple : true,
		extentions : 'jpg,jpeg,gif,bmp,tiff',
		maxSize : 2000, // KB
		done : function(res){
			console.log(res);
		},
		fail : function(res){
			console.log(res);
		}
	};

	// extend the options from defaults with user's options
	var options = $.extend(defaults, opts || {});

	return this.each(function(){ // jQuery chainability
		// do plugin stuff
		$this = $(this);
		console.log($this, options);
		$this.css({
			position : 'relative',
			overflow : 'hidden'
		})
		.append('<input type="file" multiple/>')
		.children('input[type=file]').css({
			position: 'absolute',
			top: 0,
			right: 0,
			bottom : 0,
			left : 0,
			margin: 0,
			opacity: 0,
			'-ms-filter': 'alpha(opacity=0)',
			'font-size': '200px',
			direction: 'ltr',
			cursor: 'pointer'
			// visibility : 'hidden'
		}).on('change', function(){ // 파일 선택이 완료되었을 때에
			alert('upload ok');

			// 여기에서 파일 업로드 후 전송 시작, 성공, 실패에 따라 정해진 콜백을 호출하도록 만든다
			// do stuff

		});
		var $dropElement = options.dropElement=='self' ? $this : (typeof options.dropElement=='string' ? $(options.dropElement) : options.dropElement);
		console.log('$dropElement', $dropElement);
		$dropElement.on({
			dragenter : function(){

			},
			dragover : function(){

			},
			dragleave : function(){

			},
			dragend : function(){

			},
			drop : function(){

			}
		});

	});
}
}( jQuery ));
