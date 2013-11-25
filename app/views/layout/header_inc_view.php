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
	
	<link href="/css/normalize.css" rel="stylesheet"/>
	<link href="/css/bootstrap.min.css" rel="stylesheet"/>
	<link href="/css/util.css" rel="stylesheet"/>
	<link href="/css/mobile.css" rel="stylesheet"/>
	<link href="/css/web.css" rel="stylesheet" media="screen and (min-width: 992px)"/>
	<link href="https://s3.amazonaws.com/css_sprites/543/10543/ebc4aa8095.css" rel="stylesheet"/>
	<script src="/js/libs/jquery-1.10.2.min.js"></script>
	<script src="/js/util.js"></script>
	<script>
		var common_assets = '';
	</script>

</head>

<body>