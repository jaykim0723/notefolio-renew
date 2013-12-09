<!DOCTYPE html>
<html lang="ko" class="<?php echo $this->uri->segment(1); ?>">
<head>
	<meta charset="utf-8">
	<title><?php echo $title ?></title>

	<meta http-equiv="X-UA-Compatible" content="IE=10">
	<meta name="viewport" content="user-scalable=yes, initial-scale=1.0, maximum-scale=3.0, minimum-scale=1.0, width=device-width" />
	<meta name="keywords" content="<?php echo $keywords ?>"/>
	<meta name="description" content="<?php echo $description ?>"/>
	<meta property="og:title" content="<?php echo $title ?>"/>
	<meta property="og:type" content="article"/>
	<meta property="og:url" content="<?php echo $url ?>"/>
	<meta property="og:image" content="<?php echo $image ?>"/>
	<meta property="og:site_name" content="<?php echo $site_name ?>"/>
	<meta property="og:description" content="<?php echo $description ?>"/>  


	<link href="/css/bootstrap.min.css" rel="stylesheet"/>
	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
	  <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
	  <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
	<![endif]-->
	<link href="/css/bootstrap-dialog.css" rel="stylesheet"/>
	<link href="/css/bootstrap-select.css" rel="stylesheet"/>
	<link href="/css/flick/jquery-ui-1.10.3.custom.min.css" rel="stylesheet"/>
	<link href="/css/util.css" rel="stylesheet"/>
	<link href="/css/mmenu/jquery.mmenu.nf.css" rel="stylesheet" /><!-- jQuery.mmenu -->
	<link href="/css/mobile.css" rel="stylesheet"/>
	<link href="/css/web.css" rel="stylesheet" media="screen and (min-width: 992px)"/>
	<!--[if lt IE 9]>
	<link rel="stylesheet" href="/css/web.css" type="text/css" media="screen"/>
	<![endif]-->

	<link href="https://s3.amazonaws.com/css_sprites/543/10543/ebc4aa8095.css" rel="stylesheet"/>

	<script src="/js/libs/jquery-1.10.2.min.js"></script>
	<script src="/js/util.js"></script>
	<script src="/js/site.js"></script>
	<script>
		NFview = {};
		var common_assets = '';
		site.user_id = <?php echo USER_ID ?>;
		site.username = '<?php echo $this->session->userdata('username'); ?>';
		site.url = '<?php echo site_url() ?>';
		site.segment = ['<?php echo implode("','", $this->uri->segment_array()); ?>'];
	</script>

</head>

<body>