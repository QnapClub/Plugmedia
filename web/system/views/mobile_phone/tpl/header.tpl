<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
   	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
   	<meta name="apple-mobile-web-app-capable" content="yes" />
   	<meta name="viewport" content="width=320; initial-scale=1.0; maximum-scale=1; user-scalable=0;" />
 	<meta names="apple-mobile-web-app-status-bar-style" content="black-translucent" />
   
	<title>Plugmedi@</title>
	<link rel="stylesheet" href="{$adresse_css}/jquery.mobile-1.0a4.1.css" />
   <link rel="apple-touch-icon" href="{$adresse_images}/jqtouch.png" />
   <link rel="apple-touch-startup-image" href="{$adresse_images}/jqt_startup.png" />
	<link rel="apple-touch-icon-precomposed" href="{$adresse_images}/jqtouch.png">
    
    
	<script src="{$adresse_js}/jquery-1.4.4.min.js"></script>
<script src="{$adresse_js}/jquery-1.4.4.min.js"></script><script type="text/javascript" src="{$adresse_js}/jquery.mobile-1.0a4.1.min.js"></script>
<script type="text/javascript">
$(document).bind("mobileinit", function(){
    $.extend($.mobile, {
        loadingMessage: "Loading...",
		pageLoadErrorMessage: "Error"
    });
    $.mobile.page.prototype.options.backBtnText = "&nbsp;";
});




</script>	
	<script type="text/javascript" src="{$adresse_js}/jquery.mobile-1.0a4.1.min.js"></script>
<script type="text/javascript" src="{$adresse_js}/bookmark.js"></script>

</head> 
<body> 
<div data-role="page" data-theme="a" id="jqm-home">

    <div data-role="header"> 
        <h1>Plugmedia@</h1> 
        <a href="login.html" data-icon="check"  class="ui-btn-right" data-theme="a">Login</a>

    </div> 


	
	
	<div data-role="content">