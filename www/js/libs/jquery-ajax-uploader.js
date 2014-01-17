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
		maxSize : 10000, // KB
		start : function(id, fileName){
			console.log(id, fileName);
		},
		cancel : function(){
			console.log(id, fileName);
		},
		done : function(id, fileName, responseJSON){
			console.log(id, fileName, responseJSON);
		},
		fail : function(id, fileName, responseJSON){
			console.log(id, fileName, responseJSON);
		}
	};

	// extend the options from defaults with user's options
	var options = $.extend(defaults, opts || {});

	// console.log('ajaxUploader', options, this);
	$(this).addClass('uploader-wrapper').append('<div class="uploader-area"></div>');


	// initializing ajax uplaodify
	// var uploader = new qq.FileUploader({
	new qq.FileUploader({
		// pass the dom node (ex. $(selector)[0] for jQuery users)
		element: $(this).children('.uploader-area')[0],
		// path to server-side upload script
		action: options.url,
		debug : true,
		onComplete: function(id, fileName, responseJSON){
		    if(typeof(responseJSON.success)=='undefined')
		    	options.fail(id, fileName, responseJSON);
		    else
		    	options.done(id, fileName, responseJSON);
		},              
		onSubmit: function(id, fileName){
		    options.start(id, fileName);
		},
		onCancel: function(id, fileName){
		    options.cancel(id, fileName);
		},         
	});	

	return $(this);

}
}( jQuery ));
