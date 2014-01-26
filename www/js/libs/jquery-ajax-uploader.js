(function( $ ) {
$.fn.ajaxUploader = function(opts) {
	var defaults = {
		// setting your default values for options
		url : '/upload',
		debug : true,
		droppable : true,
		dropActiveClass : '.drop-active',
		clickElement : 'self',
		multiple : true,
		allowedExtensions : ['jpg','jpeg','gif','bmp','tiff','png'],
		sizeLimit : 10000, // KB
		start : function(elem, id, fileName){
			console.log(elem, id, fileName);
		},
		cancel : function(elem, id, fileName){
			console.log(elem, id, fileName);
		},
		done : function(responseJSON, elem, id, fileName){
			console.log('default', responseJSON, elem, id, fileName);
		},
		fail : function(responseJSON, elem, id, fileName){
			console.log(responseJSON, elem, id, fileName);
		}
	};

	// extend the options from defaults with user's options
	var options = $.extend(defaults, opts || {});
	console.log('options >done', options.done);

	// console.log('ajaxUploader', options, this);
	$(this).addClass('uploader-wrapper').append('<div class="uploader-area"></div>');


	// initializing ajax uplaodify
	$(this).uploader = new qq.FileUploader({
	//new qq.FileUploader({
		// pass the dom node (ex. $(selector)[0] for jQuery users)
		element: $(this).children('.uploader-area')[0],
		// path to server-side upload script
		action: options.url,
		multiple: options.multiple,
		debug : options.debug,
		sizeLimit : options.sizeLimit*1024,
		allowedExtensions : options.allowedExtensions,
		onComplete: function(id, fileName, responseJSON){
			var elem = $(this.element).closest('.uploader-wrapper');
		    if(responseJSON.status=='done')
		    	options.done(responseJSON, elem, id, fileName);
		    else
		    	options.fail(responseJSON, elem, id, fileName);
		},              
		onSubmit: function(id, fileName){
			var elem = $(this.element).closest('.uploader-wrapper');
		    options.start(elem, id, fileName);
		},
		onCancel: function(id, fileName){
			var elem = $(this.element).closest('.uploader-wrapper');
		    options.cancel(elem, id, fileName);
		},         
	});	

	return $(this);

}
}( jQuery ));
