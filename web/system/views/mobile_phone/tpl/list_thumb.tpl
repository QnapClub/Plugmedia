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
    $.mobile.page.prototype.options.backBtnText = "&laquo; Previous page";
});
</script>	
	<script type="text/javascript" src="{$adresse_js}/jquery.mobile-1.0a4.1.min.js"></script>


</head> 
<body> 
<div data-role="page">

	<div data-role="header" data-theme="a">
		<h1>Inset list samples</h1>
		<a href="../../" data-icon="home" data-iconpos="notext" data-direction="reverse" class="ui-btn-right jqm-home">Home</a>
	</div><!-- /header -->

	<div data-role="content">
	

		
		<h2>Thumbnail, split button list</h2>
        <ul data-role="listview" data-inset="true">
{foreach from=$list item=media}
	{if $media.type eq 'dir' || $media.type eq 'link'}
    
			
				<li><a href="list.php?dir={$media.dir_id}&ref={$smarty.get.ref}">
				{if $media.thumb neq ""}<img src="{$media.thumb}" title="{$media.short_name|convert_utf8}" width="80" height="80" style="vertical-align:top;" />{else}<img src="{$adresse_images}/blank.gif" title="{$media.short_name|convert_utf8}" width="80" height="80" style="vertical-align:top;" />{/if}
				<h3>{$media.short_name_displayable|truncate:20:"..."}</h3>
				<p>{$media.short_name_displayable|truncate:20:"..."}</p></a></li>
     {/if}
{foreachelse}
	{t}FOLDEREMPTY{/t}
{/foreach}
			</ul>
			

	</div><!-- /content -->

</div><!-- /page -->

</body>
</html>
