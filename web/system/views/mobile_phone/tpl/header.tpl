<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
   	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
   	<meta name="apple-mobile-web-app-capable" content="yes" />
   	<meta name="viewport" content="width=320; initial-scale=1.0; maximum-scale=1; user-scalable=0;" />
 	<meta names="apple-mobile-web-app-status-bar-style" content="black-translucent" />
   
	<title>Plugmedi@</title>
	<link rel="stylesheet" href="{$adresse_css}/jquery.mobile-1.0b2.css" />
    <link href="{$adresse_css}/photoswipe.css" type="text/css" rel="stylesheet" />
   <link rel="apple-touch-icon" href="{$adresse_images}/jqtouch.png" />
   <link rel="apple-touch-startup-image" href="{$adresse_images}/jqt_startup.png" />
	<link rel="apple-touch-icon-precomposed" href="{$adresse_images}/jqtouch.png">
    
    
	<script src="http://code.jquery.com/jquery-1.6.2.min.js"></script>
    </script><script type="text/javascript" src="{$adresse_js}/jquery.mobile-1.0b2.min.js"></script>
    <script type="text/javascript" src="{$adresse_js}/klass.min.js"></script>
	<script type="text/javascript" src="{$adresse_js}/code.photoswipe.jquery-2.1.0.min.js"></script>


<script type="text/javascript" src="{$adresse_js}/bookmark.js"></script>

</head> 
<body> 
<div data-role="page" data-theme="a" id="jqm-home">

    <div data-role="header"> 
        <h1>Plugmedia@</h1> 
        {if $loggedin}
       <a href="api.php?ac=logout" data-icon="delete" data-ajax="false" class="ui-btn-right" data-theme="a">{$user_info.login}</a>
        {else}
        <a href="login.php" data-icon="check" data-rel="dialog" class="ui-btn-right" data-theme="a">Login</a>
        {/if}

    </div> 


	
	
	<div data-role="content">