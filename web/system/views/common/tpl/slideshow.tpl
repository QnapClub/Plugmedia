
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Slideshow</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
{literal}
<style type="text/css">	
	body,html {
		background:#121212;
		margin:0;
		padding:0;
		height:100%;
		overflow:hidden;
		text-align:center;
	}
	div#flashcontent {
	   	height:100%;
	}
</style>
 
<script type="text/javascript" src="{/literal}{$adresse_js}{literal}/swfobject_2_0.js"></script>

<script type="text/javascript">
function flashPutHref(href) { location.href = href; }
// SWFObject embed
var flashvars = {
	paramXMLPath: "../../../../api_slideshow.php?params=slideshow_param|dir={/literal}{$smarty.get.dir}{literal}",
	initialURL: escape(document.location)
}
var params = { 
	base: ".",
	bgcolor: "#121212",
	allowfullscreen: "true"
}                
var attributes = {}
swfobject.embedSWF("system/views/common/player/slideshow.swf", "flashcontent", "100%", "100%", "9.0.0", false, flashvars, params, attributes);
</script>
{/literal}
</head>   

<body>
	
		<div id="flashcontent">
			This SlideShow requires the Flash Player plugin and a web browser with JavaScript enabled.
		</div>
	

</body> 

</html>
