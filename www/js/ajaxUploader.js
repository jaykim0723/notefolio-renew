(function($, window, undefined){
$.fn.ajaxUploader = function(opts) {
	var defaults = {
		// setting your default values for options
		url : '/upload.php',
		droppable : true,
		dropActiveClass : '.drop-active',
		multiple : true,
		extentions : 'jpg,jpeg,gif,bmp,tiff',
		maxSize : 2000 // KB
	};

	// extend the options from defaults with user's options
	var options = $.extend(defaults, opts || {});

	return this.each(function(){ // jQuery chainability
		// do plugin stuff
	
	});
})(jQuery, window);