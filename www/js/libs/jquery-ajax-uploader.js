(function( $ ) {
$.fn.ajaxUploader = function(opts) {
	var defaults = {
		// setting your default values for options
		url : '/upload.php',
		droppable : true,
		dropActiveClass : '.drop-active',
		dropElement : 'self',
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
			postion : 'relative'
		}).append('<input type="file" multiple/>').children('input[type=file]').css({
			position: 'absolute',
			top: 0,
			right: 0,
			margin: 0,
			opacity: 0,
			'-ms-filter': 'alpha(opacity=0)',
			'font-size': '200px',
			direction: 'ltr',
			cursor: 'pointer'
		}).on('change', function(){ // 파일 선택이 완료되었을 때에
			alert('upload ok');
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
