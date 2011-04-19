<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PlugMedi@</title>




<link rel="shortcut icon" href="system/views/common/img/favicon.gif" type="image/x-icon" />




    <link rel="stylesheet" type="text/css" href="system/views/common/css/shadowbox.css" />
        
    <style type="text/css">
		.avatar_tb{	background-image: url(system/common_style/avatar/16/astronauta.png) !important;	background-repeat: no-repeat; }
	</style>
	
    



	
	<link rel="stylesheet" type="text/css" href="system/views/common/css/pm_complex.css" />

	<link type="text/css" href="system/views/common/css/black-tie/jquery-ui-1.8.5.custom.css" rel="stylesheet" />	





	<script type="text/javascript" src="api.php?ac=getPlugmediaJavascriptConfig"></script>
    <script type="text/javascript" src="api.php?ac=generateToolbar"></script>
	<script type="text/javascript" src="system/views/common/js/jquery/jquery-1.4.4.min.js"></script>
	<script type="text/javascript" src="system/views/common/js/jquery/jquery-ui-1.8.7.custom.min.js"></script>
	<script type="text/javascript" src="system/views/common/js/jquery/plugmedia-ui.js"></script>   
    
	<script type="text/javascript" src="system/views/common/js/jquery/jquery.layout.min.js"></script>

	<script type="text/javascript" src="system/views/common/js/jquery/jquery.jstree.min.js"></script>
	<script type="text/javascript" src="system/views/common/js/jquery/jquery.cookie.min.js"></script>
	<script type="text/javascript" src="system/views/common/js/jquery/jquery.hotkeys.min.js"></script>	
	<script type="text/javascript" src="system/views/common/js/jquery/jquery.lazyload.min.js"></script>
	<script type="text/javascript" src="system/views/common/js/jquery/jquery.jBreadCrumb.1.1.min.js"></script>
	<script type="text/javascript" src="system/views/common/js/jquery/jquery.simplemodal.1.4.min.js"></script>   


        
    <script type="text/javascript" language="javascript" src="system/views/common/js/cooliris-min.js"></script><script type="text/javascript" language="javascript" src="system/views/common/js/shadowbox/shadowbox.js"></script><script type="text/javascript" language="javascript" src="system/views/common/js/swfobject.js"></script><script type="text/javascript" language="javascript" src="system/views/common/js/swfobject_creation.js"></script> 
     
	<script type="text/javascript" src="system/views/common/js/jquery/jquery.form.min.js"></script> 
	<script type="text/javascript" src="system/views/common/js/jwplayer.js"></script>   
 

	

    

<script language="javascript"> 
    $.ajax({
            dataType: 'json',
            url: 'http://loetcris.homedns.org/plugmedia/display.php?dir=7775&file=381011&ref=7456&view=inline',
            cache:true,
            success: function(data) {
               
                jQuery.each(data, function(target, value){
                    $('#'+target).html(value).fadeTo(300, 1);
                });
                       
            },
            error: function(){    },
            complete: function(){     } ,
            beforeSend : function(){}  
        });
</script> 

<div id="breadCrumb0"></div>

<div id="ui_content_pm"></div>
    
</body>
</html> 