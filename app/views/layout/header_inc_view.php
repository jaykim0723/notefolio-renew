<!DOCTYPE html>
<html class="<?php echo $this->uri->segment(1); ?>">
<head>
	<meta charset="utf-8">
	<title><?php echo $title ?></title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge"> 
	<meta name="viewport" content="user-scalable=yes, initial-scale=1.0, maximum-scale=3.0, minimum-scale=1.0, width=device-width" />
	<meta name="keywords" content="<?php echo $keywords ?>"/>
	<meta name="description" content="<?php echo $description ?>"/>
	<meta property="og:title" content="<?php echo $title ?>"/>
	<meta property="og:type" content="article"/>
	<meta property="og:url" content="<?php echo $url ?>"/>
	<meta property="og:image" content="<?php echo $image ?>"/>
	<meta property="og:site_name" content="<?php echo $site_name ?>"/>
	<meta property="og:description" content="<?php echo $description ?>"/>  

	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
	  <script src="/js/libs/html5shiv.js"></script>
	  <script src="/js/libs/respond.min.js"></script>
	<![endif]-->

	<link href="/css/normalize.css" rel="stylesheet"/>
	<link href="/css/flick/jquery-ui-1.10.3.custom.min.css" rel="stylesheet"/>
	<link href="/css/bootstrap.min.css" rel="stylesheet"/>
	<link href="/css/bootstrap-dialog.css" rel="stylesheet"/>
	<link href="/css/bootstrap-select.css" rel="stylesheet"/>
	<link href="/css/util.css" rel="stylesheet"/>
	<link href="/css/mobile.css" rel="stylesheet"/>
	<link href="/css/mmenu/jquery.mmenu.nf.css" rel="stylesheet" /><!-- jQuery.mmenu -->
	<link href="/css/web.css" rel="stylesheet" media="screen and (min-width: 992px)"/>
	<link href="https://s3.amazonaws.com/css_sprites/543/10543/ebc4aa8095.css" rel="stylesheet"/>

	<script src="/js/libs/jquery-1.10.2.min.js"></script>
	<script src="/js/libs/jquery-ui-1.10.3.custom.min.js"></script>
	<script src="/js/util.js"></script>
	<script src="/js/site.js"></script>
	<script>
		var common_assets = '';
		site.user_id = <?php echo USER_ID ?>;
		site.username = '<?php echo $this->session->userdata('username'); ?>';
		site.url = '<?php echo site_url() ?>';
		site.segment = ['<?php echo implode("','", $this->uri->segment_array()); ?>'];
	</script>

</head>

<body>