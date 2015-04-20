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
//			console.log(elem, id, fileName);
		},
		cancel : function(elem, id, fileName){
//			console.log(elem, id, fileName);
		},
		done : function(responseJSON, elem, id, fileName){
//			console.log('default', responseJSON, elem, id, fileName);
		},
		fail : function(responseJSON, elem, id, fileName){
//			console.log(responseJSON, elem, id, fileName);
		}
	};

	// extend the options from defaults with user's options
	var options = $.extend(defaults, opts || {});
//	console.log('options >done', options.done);

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
        // messages                
        messages: {
            typeError: "{file} 은 업로드할 수 없는 파일입니다. 업로드 가능한 파일 포맷은 {extensions} 입니다.",
            sizeError: "{file} 이 너무 큽니다. 업로드 가능한 이미지 사이즈는 최대 {sizeLimit} 입니다.",
            minSizeError: "{file} 이 너무 작습니다. 업로드 가능한 이미지 사이즈는 최소 {minSizeLimit} 입니다.",
            emptyError: "{file} 이 비어 있습니다. 이 파일을 제외하고 업로드해 주세요.",
            onLeave: "파일을 업로드하고 있습니다. 이 창을 벗어나면 업로드가 중지됩니다."            
        },       
	});	

	return $(this);

}
}( jQuery ));
