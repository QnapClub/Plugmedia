<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PlugMedi@</title>
{literal}
<style type="text/css">
#loading-mask{position:absolute;left:0;top:0;width:100%;height:100%;z-index:20000;background-color:white;}
#loading{position:absolute;left:45%;top:40%;padding:2px;z-index:20001;height:auto;}
#loading .loading-indicator{background:white;color:#444;font:bold 13px tahoma,arial,helvetica;padding:10px;margin:0;height:auto;}
#loading-msg {font: normal 10px arial,tahoma,sans-serif;}
</style>
{/literal}
{if isset($cooliris)}
	{$cooliris}
{/if}
<link rel="shortcut icon" href="{$adresse_images}/favicon.gif" type="image/x-icon" />

<div id="loading-mask" style=""></div>
    <div id="loading"><div class="loading-indicator"><img src="{$adresse_images}/preload.gif" width="32" height="32" style="margin-right:8px;float:left;vertical-align:top;"/>Plugmedia <br /><span id="loading-msg">{t}LOADINGSTYLE{/t}...</span></div></div>


    {$extra_css}
    {if $loggedin}
    {literal}
    <style type="text/css">
		.avatar_tb{	background-image: url(system/common_style/avatar/16/{/literal}{$user_info.avatar}{literal}) !important;	background-repeat: no-repeat; }
	</style>
	{/literal}
    {/if}




	
	<link rel="stylesheet" type="text/css" href="{$adresse_css}/pm_complex.css" />
	<link rel="stylesheet" type="text/css" href="{$adresse_css}/pm_breadCrumb.css" />    
	<link type="text/css" href="{$adresse_css}/black-tie/jquery-ui-1.8.5.custom.css" rel="stylesheet" />	



	<!--[if lte IE 7]>
		<style type="text/css"> body { font-size: 85%; } </style>
	<![endif]-->
	<script type="text/javascript">document.getElementById('loading-msg').innerHTML = '{t}LOADINGJS{/t}...';</script>

	<script type="text/javascript" src="api.php?ac=getPlugmediaJavascriptConfig"></script>
    <script type="text/javascript" src="api.php?ac=generateToolbar"></script>
	<script type="text/javascript" src="{$adresse_js}/jquery/jquery-1.4.4.min.js"></script>
	<script type="text/javascript" src="{$adresse_js}/jquery/jquery-ui-1.8.7.custom.min.js"></script>
	<script type="text/javascript" src="{$adresse_js}/jquery/plugmedia-ui.js"></script>   
    
	<script type="text/javascript" src="{$adresse_js}/jquery/jquery.layout.min.js"></script>

	<script type="text/javascript" src="{$adresse_js}/jquery/jquery.jstree.min.js"></script>
	<script type="text/javascript" src="{$adresse_js}/jquery/jquery.cookie.min.js"></script>
	<script type="text/javascript" src="{$adresse_js}/jquery/jquery.hotkeys.min.js"></script>	
	<script type="text/javascript" src="{$adresse_js}/jquery/jquery.lazyload.min.js"></script>
	<script type="text/javascript" src="{$adresse_js}/jquery/jquery.jBreadCrumb.1.1.min.js"></script>
	<script type="text/javascript" src="{$adresse_js}/jquery/jquery.simplemodal.1.4.min.js"></script>   
	<script type="text/javascript" src="{$adresse_js}/jquery/jquery.queue.min.js"></script>  	
	<script type="text/javascript" src="{$adresse_js}/jquery/jquery.validate.min.js"></script>  	
	<script type="text/javascript" src="{$adresse_js}/jquery/jquery.growl.min.js"></script>  
 	<script type="text/javascript" src="{$adresse_js}/jquery/jquery.bubblepopup.v2.3.1.min.js"></script>   
    <script type="text/javascript" src="{$adresse_js}/jquery/jquery.tag.editor.js"></script>
    <script type="text/javascript" src="{$adresse_js}/jquery/jquery.jeditable.mini.js"></script>
	<script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
	<script type="text/javascript" src="{$adresse_js}/jwplayer.js"></script>	

        
    {$extra_js} 
     
	<script type="text/javascript" src="{$adresse_js}/jquery/jquery.form.min.js"></script> 
	   
 
   	{literal}
	   
    <script language="javascript">$(document).ready( function() {  PM.init();  
	

	
	 });</script>
	{/literal}

    <div class="ui-layout-west">
        <div class="header"></div>
        <div class="content"><div id="pm_tree" class="pm_tree"></div></div>
        <div class="footer">&nbsp;</div>
    </div>
    
    
    
    <!-- {* ---------  NORTH PART OF THE DESIGN (logo)  --------- *} -->
    <div class="ui-layout-north">
        
        <div id="header">  
  			<img src="{$adresse_images}/logo_qnap.png" />  
			<img src="{$adresse_images}/logo_plugmedia.png" class="logo_pm" />  
		</div>
	</div>
    
    <!-- {* ---------  MAIN CONTENT OF THE DESIGN   --------- *} -->
    <div id="mainContent">