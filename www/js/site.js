var site = {
	redirect : function(url){
		location.href = url;
	}
};
$(function() {
	$('.infinite_list').waypoint('infinite', {
		container: '.infinite_list',
		items: '.infinite-item',
			more: '.more-link',
		offset: 'bottom-in-view',
		onAfterPageLoad : function(){
			console.log($.now());
		}
	});
});