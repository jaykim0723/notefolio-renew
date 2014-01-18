var adminMenu = {
	menuItems : [
		{
			'btn' : 'btn-add-to-creators',
			'label' : '핫작가등록',
			'func' : 'addToHotCreators',
			'inPage' : ['work-info', 'profile'] // 'all'
		}
	],
	initBottom : function(){
		if(typeof NFview=='undefined') return;
		if(typeof NFview.area=='undefined') return;
		console.log('NFview.area', NFview.area);

		$('#footer .admin-none').remove();
		$('#footer-menu').prop('class', 'col-md-6').next().show();

		var menuArea = $('#footer-gap');
		$.each(this.menuItems, function(k,o){
			if($.inArray(NFview.area, o.inPage) || $.inArray('all', o.inPage))
				menuArea.append('<a href="javascript:adminMenu.'+o.func+'();" id="'+o.btn+'">'+o.label+'</a>');
		});


	},

	addToHotCreators : function(){
		$.post('/admin/add_to_hot_creators', {
			username : site.segment[0]
		}).done(function(d){
			alert(d);
		});
	}
};