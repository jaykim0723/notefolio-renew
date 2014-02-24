<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="utf-8">
<title><?=$title?> - Notefolio Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="apple-mobile-web-app-capable" content="yes"><link href="/css/normalize.css" rel="stylesheet"/>
<link href="/css/acp/bootstrap.min.css" rel="stylesheet">
<link href="/css/acp/bootstrap-responsive.min.css" rel="stylesheet">
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600"
        rel="stylesheet">
<link href="/css/acp/font-awesome.css" rel="stylesheet">
<link href="/css/acp/style.css" rel="stylesheet">
<link href="/css/acp/pages/dashboard.css" rel="stylesheet">
<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>
<body>
<div class="navbar navbar-fixed-top">
  <div class="navbar-inner">
    <div class="container">
    	<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
    		<span class="icon-bar"></span>
    		<span class="icon-bar"></span>
    		<span class="icon-bar"></span> 
    	</a>
    	<a class="brand" href="/acp/">Admin</a>
      <div class="nav-collapse">
        <ul class="nav pull-right">
          <li class="dropdown">
          	<a href="#" class="dropdown-toggle" data-toggle="dropdown">
          		<i class="icon-user"></i> <?=$this->tank_auth->get_username()?> <b class="caret"></b>
          	</a>
            <ul class="dropdown-menu">
              <li><a href="/auth/logout">로그아웃</a></li>
              <li><a href="/auth/unelevate">권한해제</a></li>
            </ul>
          </li>
        </ul>
        <form class="navbar-search pull-right">
          <input type="text" class="search-query" placeholder="Search">
        </form>
      </div>
      <!--/.nav-collapse --> 
    </div>
    <!-- /container --> 
  </div>
  <!-- /navbar-inner --> 
</div>
<!-- /navbar -->
<div class="subnavbar">
  <div class="subnavbar-inner">
    <div class="container">
      <ul class="mainnav">
        <li id="menu-dashboard"<?=($this->uri->segment(2)=='dashboard')?' class="active"':''?>>
        	<a href="/acp/dashboard">
        		<i class="icon-dashboard"></i>
        		<span>대시보드</span>
        	</a>
        </li>
        <li id="menu-site" class="dropdown<?=($this->uri->segment(2)=='site')?' active':''?>">
        	<a href="/acp/site" class="dropdown-toggle" data-toggle="dropdown">
        		<i class="icon-sitemap"></i>
        		<span>사이트</span>
        		<b class="caret"></b>
        	</a>
          <ul class="dropdown-menu">
            <li><a href="/acp/site/keywords">키워드</a></li>
            <li><a href="/acp/site/main_banner">메인배너</a></li>
            <li><a href="/acp/site/access_log">접속로그</a></li>
          </ul>
        </li>
        <li id="menu-user" class="dropdown<?=($this->uri->segment(2)=='user')?' active':''?>">
        	<a href="/acp/user" class="dropdown-toggle" data-toggle="dropdown">
        		<i class="icon-group"></i>
        		<span>사용자</span>
        		<b class="caret"></b>
        	</a>
          <ul class="dropdown-menu">
            <li><a href="/acp/user/member"><i class="icon-user"></i> 회원</a></li>
          </ul>
        </li>
        <li id="menu-work" class="dropdown<?=($this->uri->segment(2)=='work')?' active':''?>">
        	<a href="/acp/work" class="dropdown-toggle" data-toggle="dropdown">
        		<i class="icon-picture"></i>
        		<span>작품</span>
        		<b class="caret"></b>
        	</a>
          <ul class="dropdown-menu">
            <li><a href="/acp/work/works">목록</a></li>
            <li><a href="/acp/work/uploads">업로드</a></li>
          </ul>
        </li>
        <li id="menu-act" class="dropdown<?=($this->uri->segment(2)=='act')?' active':''?>">
        	<a href="/acp/act" class="dropdown-toggle" data-toggle="dropdown">
        		<i class="icon-bell"></i>
        		<span>활동</span>
        		<b class="caret"></b>
        	</a>
          <ul class="dropdown-menu">
            <li><a href="/acp/activity/logs/">활동로그</a></li>
            <li><a href="/acp/activity/alarms/">알림</a></li>
            <li><a href="/acp/activity/feeds/">피드</a></li>
          </ul>
        </li>
        <li id="menu-stat" class="dropdown<?=($this->uri->segment(2)=='statics')?' active':''?>">
        	<a href="/acp/stat" class="dropdown-toggle" data-toggle="dropdown">
        		<i class="icon-bar-chart"></i>
        		<span>통계</span>
        		<b class="caret"></b>
        	</a>
          <ul class="dropdown-menu">
            <li><a href="/acp/statics/research">분석</a></li>
            <li><a href="/acp/statics/stat">현황</a></li>
          </ul>
        </li>
      </ul>
    </div>
    <!-- /container --> 
  </div>
  <!-- /subnavbar-inner --> 
</div>