
$(function() {
	$('.thumbnail_list').waypoint('infinite', {
		container: '.thumbnail_list',
		items: '.thumbbox',
			more: '.more-link',
		offset: 'bottom-in-view',
		onAfterPageLoad : function(){
			console.log($.now());
		}
	});
});