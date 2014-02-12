
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
<!-- <script src="/js/libs/dropzone.min.js"></script>
<script src="/js/libs/dropzone.dict-ko.js"></script>
 -->
<script>
	$('select:not(.no-jquery)').selectpicker();
</script>
<?php if (strpos($_SERVER['HTTP_HOST'], 'renew.')!==FALSE && USER_ID==3): ?>
<script src="http://jsconsole.com/remote.js?122CF29F-EC33-4FB8-A8A4-588666EE5850"></script>
<?php endif ?>
</body>
</html>