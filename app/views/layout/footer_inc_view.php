<script src="/js/libs/bootstrap.min.js"></script>
<script src="/js/libs/bootstrap-dialog.js"></script>
<script src="/js/libs/bootstrap-select.js"></script>
<script src="/js/libs/jquery-ui-view-1.10.4.custom.min.js"></script>
<script src="/js/libs/fileuploader.js"></script>
<script src="/js/libs/jquery-ajax-uploader.js"></script>
<script src="/js/libs/waypoints.js"></script>
<script src="/js/libs/waypoints-infinite.js"></script>
<script src="/js/libs/waypoints-sticky.js"></script>
<script src="/js/libs/jquery.history.js"></script>
<script src="/js/libs/jquery.hammer.min.js"></script>
<script src="/js/libs/jquery.mmenu.min.all.js"></script>
<script src="/js/libs/jquery.history.js"></script>
<script src="/js/libs/jquery.placeholder.js"></script>
<script>
	$(function() {
		 $('input, textarea').placeholder();
		});
</script>
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 999769175;
var google_conversion_label = "6jBVCLnY9QMQ14jd3AM";
var google_custom_params = window.google_tag_params;
var google_remarketing_only = true;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<script>
	$('select:not(.no-jquery)').selectpicker();
</script>

<?php if($this->input->get('rotate-my-eye')!==FALSE){  //joke www  ?>
<style type="text/css">
	.container {
		transform: rotate(180deg);
		-ms-transform: rotate(180deg);
		-o-transform: rotate(180deg);
		-webkit-transform: rotate(180deg);
	}
</style>
<?php }?>

</body>
</html>