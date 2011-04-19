<?
/**
*
* @package Plugmedia
* @copyright (c) 2009 Christophe Lemoine
* @license http://creativecommons.org/licenses/by-nc-nd/2.0/be/  Attribution-Noncommercial-No Derivative Works 2.0 Belgium
* QNAP Systems, Inc is authorize to distribute and transmit the work
*
* Minimum Requirement: PHP 5
*/

require_once '../system/core/frontcontroller.php';

function checkAll()
{

	$recup['GD'] = getGDInfo();

	$recup['PHP'] = getPhpVersion();
	$recup['IM'] = getImageMagickInfo();
	$recup['MM']  = getQmultimediaReadFile();
	$recup['WF']  = getWritableFolders();
	$recup['IMRALL']  = getIMRALL();
	$recup['EXIF']  = getExifInfo();
	$recup['SSL']  = getOpenSSL();
	$recup['PGSQL']  = getPgsql();
	$recup['CGI']  = getCGIInfo();
	$recup['DB']  = check_db_version();
	$recup['DCRAW']  = getDcrawInfo();	
	return $recup;

}

function getGDInfo()
{
	
	$gdInfo['gd_string']  = gd_version(true);
	$gdInfo['gd_numeric'] = gd_version(false);
	if ($gdInfo['gd_numeric'] >= '2')
	{
		$gdInfo	['global_status'] = '2';
		$gdInfo	['global_css'] = 'all_good';
	}
	else
	{
		
		$gdInfo	['global_status'] = '0';
		$gdInfo	['global_css'] = 'error';
	}	
	return $gdInfo;
}	

function getImageMagickInfo()
{
	$info =  ImageMagickVersion();
	return $info;
}

function getPhpVersion()
{
	$php_array['php_version'] =  phpversion();
	if ($php_array['php_version'] >= '5.0.0')
	{
		$php_array	['global_status'] = '2';	// GOOD
		$php_array	['global_css'] = 'all_good';
	}
	else
	{
		$php_array	['global_status'] = '0'; 	// BAD
		$php_array	['global_css'] = 'error';
	}
	return $php_array;

}

function getQmultimediaReadFile()
{
	if (is_readable(STARTING_FOLDER) && @filemtime(STARTING_FOLDER))
	{
		$mm['global_status'] = '2';	// GOOD
		$mm	['global_css'] = 'all_good';
	}
	else
	{
		$mm['global_status'] = '0';	// BAD
		$mm	['global_css'] = 'error';
	}
	return $mm;

}

function getWritableFolders()
{
	$error = false;
	if (is_writable('../system/_cache'))
	{
		$write_foler['folder_cache'] = true;
	}	
	else
	{
		$write_foler['folder_cache'] = false;
		$error = true;
	}
		
	if (is_writable('../system/_compiled'))
	{
		$write_foler['folder_compile'] = true;
	}
	else
	{
		$write_foler['folder_compile'] = false;
		$error = true;
	}
	
	if (is_writable('../system/_cache/thumb'))
	{
		$write_foler['folder_cache_thumb'] = true;
	}
	else
	{
		$write_foler['folder_cache_thumb'] = false;
		$error = true;
	}

	if (is_writable('../system/logs'))
	{
		$write_foler['folder_logs'] = true;
	}
	else
	{
		$write_foler['folder_logs'] = false;
		$error = true;
	}

	
	if (is_writable('../thumb'))
	{
		$write_foler['folder_thumb'] = true;
	}
	else
	{
		$write_foler['folder_thumb'] = false;
		$error = true;
	}	

	if ($error)
	{
		$write_foler['global_status'] = '0';
		$write_foler['global_css'] = 'error';
	}
	else
	{
		$write_foler['global_status'] = '2';
		$write_foler['global_css'] = 'all_good';
	}
	return $write_foler;

}


function getIMRALL()
{
	if (file_exists("/usr/local/sbin/ImR_all")) 
	{
		if (is_executable("/usr/local/sbin/ImR_all"))
		{
			$imrall['global_status'] = '2';
			$imrall['global_css'] = 'all_good';
		}
		else
		{
			$imrall['global_status'] = '1';
			$imrall['global_css'] = 'warning';
		}
	
	}	
	else
	{
		$imrall['global_status'] = '1';
		$imrall['global_css'] = 'warning';
	}	
	return $imrall;

}



function getExifInfo()
{
	if (extension_loaded('exif'))
	{
		$exif_info['global_status'] = '2';	
		$exif_info['global_css'] = 'all_good';
	}else{
		$exif_info['global_status'] = '1';	
		$exif_info['global_css'] = 'warning';
	}
	return $exif_info;
}

function getDcrawInfo()
{
	$output['string'] = shell_exec('/opt/bin/dcraw');
	$matches = explode ("\n",$output['string']);
	$dcraw_info['dcraw_string'] = $matches[1];
	if ($matches[1]!='')
	{
		$dcraw_info['global_status'] = '2';	
		$dcraw_info['global_css'] = 'all_good';
	}else{
		$dcraw_info['global_status'] = '1';	
		$dcraw_info['global_css'] = 'warning';
	}
	return $dcraw_info;
}


function getOpenSSL()
{
	if (extension_loaded('openssl'))
	{
		$openssl['global_status'] = '2';
		$openssl['global_css'] = 'all_good';			
	}else{
		$openssl['global_status'] = '1';	
		$openssl['global_css'] = 'warning';	
	}
	return $openssl;
}
function getPgsql()
{
	if (extension_loaded('pgsql'))
	{
		$pgsql['global_status'] = '2';	
		$pgsql['global_css'] = 'all_good';
	}else{
		$pgsql['global_status'] = '0';	
		$pgsql['global_css'] = 'error';
	}
	return $pgsql;
}

function getCGIInfo()
{
	$error = false;
	if (file_exists("/home/httpd/cgi-bin/plugmedia/chmod_db.cgi") && is_executable("/home/httpd/cgi-bin/plugmedia/chmod_db.cgi")) 
	{	
		$cgi['chmod'] = true;	
	}
	else
	{
		$cgi['chmod'] = false;	
		$error = true;	
	}
	if (file_exists("/home/httpd/cgi-bin/plugmedia/cgi_auth.cgi") && is_executable("/home/httpd/cgi-bin/plugmedia/cgi_auth.cgi")) 
	{	
		$cgi['auth'] = true;	
	}
	else
	{
		$cgi['auth'] = false;	
		$error = true;	
	}
	if ($error)
	{
		$cgi['global_status'] = '1';
		$cgi['global_css'] = 'warning';
	}
	else
	{	
		$cgi['global_status'] = '2';
		$cgi['global_css'] = 'all_good';
	}
	return $cgi;
	

}

function check_db_version()
{

	global $DB;
	$DB->query("SELECT * from sys_conf_settings where conf_key ='DATABASE_SCHEMA_VERSION'","check_db_version");
	$recup = $DB->fetchrow();

	$db_version['DB_version'] = $recup['conf_default'];
	$db_version['system_version'] = VERSION_PACKAGE;
	
	if ($recup['conf_default'] == VERSION_PACKAGE)
	{
		$db_version['global_status'] = '2';
		$db_version['global_css'] = 'all_good';
	}else{
		$db_version['global_status'] = '0';
		$db_version['global_css'] = 'error';
	}
	return $db_version; 
}




function gd_version($fullstring=false) {
	static $cache_gd_version = array();
	if (empty($cache_gd_version)) {
		$gd_info = gd_info();

		if (eregi('bundled \((.+)\)$', $gd_info['GD Version'], $matches)) {
			$cache_gd_version[1] = $gd_info['GD Version'];  // e.g. "bundled (2.0.15 compatible)"
			$cache_gd_version[0] = (float) $matches[1];     // e.g. "2.0" (not "bundled (2.0.15 compatible)")
		} else {
			$cache_gd_version[1] = $gd_info['GD Version'];                       // e.g. "1.6.2 or higher"
			$cache_gd_version[0] = (float) substr($gd_info['GD Version'], 0, 3); // e.g. "1.6" (not "1.6.2 or higher")
		}
	}
	return $cache_gd_version[intval($fullstring)];
}

function ImageMagickVersion() {

	$output['string'] = shell_exec('/mnt/ext/opt/twonkymedia/cgi-bin/convert --version');
	
	preg_match_all('/ ImageMagick ([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,2}) /',$output['string'],$matches);
	$output['im_version'] = $matches[1][0];
	
	if ($output['im_version'] >= '6.4.8')
	{
		$output	['global_status'] = '2';	// GOOD
		$output['global_css'] = 'all_good';
	}
	else
	{
		$output	['global_status'] = '1'; 	// BAD
		$output ['global_css'] = 'warning';
	}
	return $output;
}


	
$check = checkAll();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="EN" lang="EN" dir="ltr">
<head>
<title>Plugmedia - System check </title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="imagetoolbar" content="no" />
<script type="text/javascript" src="jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="jquery.qtip-1.0.0-rc3.min.js"></script>




<style type="text/css">

body{
	margin:0;
	padding:0;
	font-size:13px;
	font-family:Georgia, "Times New Roman", Times, serif;
	color:#666666;
	background-color:#FFFFFF;
	}

img{display:block; margin:0; padding:0; border:none;}
.justify{text-align:justify;}
.bold{font-weight:bold;}
.center{text-align:center;}
.right{text-align:right;}
.nostart {list-style-type:none; margin:0; padding:0;}
.clear{clear: both;}
br.clear{clear:both; margin-top:-15px;}

a{outline:none; text-decoration:none; color:#660000; background-color:#FFFFFF;}

.fl_left{float:left;}
.fl_right{float:right;}

.imgl, .imgr{border:1px solid #C7C5C8; padding:5px;}
.imgl{float:left; margin:0 8px 8px 0; clear:left;}
.imgr{float:right; margin:0 0 8px 8px; clear:right;}


/* ----------------------------------------------Wrapper-------------------------------------*/

div.wrapper{
	display:block;
	width:100%;
	text-align:left;
	}

div.wrapper h1, div.wrapper h2, div.wrapper h3, div.wrapper h4, div.wrapper h5, div.wrapper h6{
	margin:0 0 15px 0;
	padding:0;
	font-size:20px;
	font-weight:normal;
	line-height:normal;
	}

.col0, .col0 a{color:#999999; background-color:#333333;}
.col1{color:#666666; background-color:#000000; border-bottom:1px solid #363636;}
.col2{color:#FFFFFF; background-color:#191919; padding:20px 0;}
.col2 a{color:#FFFFFF; background-color:#191919;}
.col3{margin:1px 0; border-top:1px solid #363636; border-bottom:1px solid #363636;}

.col4{color:#FFFFFF; background-color:#000000;}
.col4 a{color:#FFFF00; background-color:#000000;}

.col5, .col5 a{color:#999999; background-color:#1B1B1B;}

.col4, .col5{ font-family: Verdana, Geneva, sans-serif;}
.col4 h2, .col5 h2{ font-family: Georgia, "Times New Roman", Times, serif;}

/* ----------------------------------------------Generalise-------------------------------------*/

#header, #topline, #topbar, #breadcrumb, .container, #hpage_cats, #adblock, #socialise, #footer, #copyright{
	position:relative;
	margin:0 auto 0;
	display:block;
	width:960px;
	}

/* ----------------------------------------------Header-------------------------------------*/

#header{
	padding:10px 0 20px;
	z-index:1000;
	}

#header h1, #header p, #header ul{
	margin:0;
	padding:0;
	list-style:none;
	line-height:normal;
	}

#header #logo{
	font-family:Verdana, Geneva, sans-serif;
	display:block;
	float:left;
	margin-top:7px;
	overflow:hidden;
	}

#header #logo h1 a{
	font-size:46px;
	color:#FFFFFF;
	background-color:#000000;
	}

#header h1 strong{
	font-weight:normal;
	color:#F3DE33;
	background-color:#000000;
	}

#header #logo p{
	text-align:right;
	margin-top:5px;
	font-family:Arial, Helvetica, sans-serif;
	text-transform:lowercase;
	}


/* ----------------------------------------------Content-------------------------------------*/

.container{
	padding:20px 0;
	}

.content{
	display:block;
	float:left;
	width:630px;
	}
#topnav{
	display:block;
	float:right;
	margin-top:22px;
	width:600px;
	font-size:18px;
	font-family:Georgia, "Times New Roman", Times, serif;
	text-align:center;
	color:#FFF;
	}


.all_good,.warning, .error {
	font-size:16px;
}

.all_good {
	
	color: #008000;
	
}
.warning {
	
	color: #E29521;
	font-weight: bold;	
}
.error {

	color: #BD1A1A;
	font-weight: bold;	
}


</style>
</head>
<body id="top">


<script class="example" type="text/javascript">
// Create the tooltips only on document load
$(document).ready(function() 
{
   level = ["red","cream","green"];
   
   $('.info').each(function(i){

	   
   $(this).qtip({
   content   : $('.tooltipTxt').eq(i), // One content per tooltip
 
 position: {
      corner: {
         target: 'rightMiddle',	
         tooltip: 'leftMiddle'
      }
   },
hide: { when: 'mouseout', fixed: true },
  
   style: { 
      name: level[$('.tooltipTxt').eq(i).attr("level")],
	  tip: 'leftMiddle',
	  width: { max: 800 }
   }
   

   });
});
   
   
});
</script>



<div class="wrapper col0"></div>

<div class="wrapper col1">
  <div id="header">
    <div id="topnav">System check <? echo $check['DB']['system_version']; ?></div>
 <div id="logo">
      <h1><a href="#">Plug<strong>M</strong>edi@</a></h1>
      <p>Multimedia Station free alternative</p>
    </div>
    <br class="clear" />
  </div>
</div>

<div class="wrapper col2"></div>


  <div class="container">
   





<table width="100%" border="0" cellpadding="10" cellspacing="0">
  <tr>
    <td width="200" class="<?= $check['PHP']['global_css'] ?>">PHP version</td>
    <td>
    <?
	if ( $check['PHP']['global_status'] == '2')
	{
	?>
    <a class="info"><img src="accept.png" width="16" height="16" /></a>
    <div class="tooltipTxt" level="2" style="display:none">Your PHP version is Up to date<br />Version: <strong><? echo $check['PHP']['php_version']; ?></strong></div>
	
    <?
	}else{
	?>
	<a class="info"><img src="delete.png" width="16" height="16" /></a>
   	<div class="tooltipTxt" level="0" style="display:none">Please Install the last firmware available for your NAS<br />Current Version: <? echo $check['PHP']['php_version']; ?><br />Php version needed: 5.0.0 + </div>
	
    <?
	}
	?>	
    
	
    </td>
    
  </tr>
  <tr>
    <td width="200" bgcolor="#F7F7F7" class="<?= $check['MM']['global_css'] ?>">Starting Folder</td>
    <td bgcolor="#F7F7F7">
    <?
	if ( $check['MM']['global_status'] == '2')
	{
	?>
    <a class="info"><img src="accept.png" width="16" height="16" /></a>    
    <div class="tooltipTxt" level="2" style="display:none">Your starting folder <?= STARTING_FOLDER ?> <strong>is readable</strong></div>
	
    <?
	}else{
	?>
     <a class="info"><img src="delete.png" width="16" height="16" /></a>
     <div class="tooltipTxt" level="0" style="display:none" >Your starting folder <?= STARTING_FOLDER ?> is not readable, please check the path and make visible the folder</div>
	
    <?
	}
	?>	

  
    
    </td>
    
  </tr>

  <tr>
    <td width="200" class="<?= $check['WF']['global_css'] ?>">Chmod Folder</td>
    <td>
    
     <?
	if ( $check['WF']['global_status'] == '2')
	{
	?>
    <a class="info"><img src="accept.png" width="16" height="16" /></a>    
    <div class="tooltipTxt" level="2" style="display:none">All folders are <strong>ok</strong><br />
     	<? 
			if ( $check['WF']['folder_cache']) echo 'Cache folder is writable';	else echo 'Please Chmod 0777 plugmedia/system/_cache';
			echo '<br>';
			if ( $check['WF']['folder_compile']) echo 'Compile folder is writable';	else echo 'Please Chmod 0777 plugmedia/system/_compile';
			echo '<br>';
			if ( $check['WF']['folder_cache_thumb']) echo 'Cache thumb folder is writable';	else echo 'Please Chmod 0777 plugmedia/system/_cache/thumb';	
			echo '<br>';
			if ( $check['WF']['folder_logs']) echo 'Log folder is writable';	else echo 'Please Chmod 0777 plugmedia/system/logs';	
			echo '<br>';
			if ( $check['WF']['folder_thumb']) echo 'Thumbnail folder is writable';	else echo 'Please Chmod 0777 plugmedia/thumb';
		 ?>    
    
    </div>
	
    <?
	}else{
	?>
     <a class="info"><img src="delete.png" width="16" height="16" /></a>
     <div class="tooltipTxt" level="0" style="display:none" >See additional error:<br />
    
		<? 
			if ( $check['WF']['folder_cache']) echo 'Cache folder is writable';	else echo 'Please Chmod 0777 plugmedia/system/_cache';
			echo '<br>';
			if ( $check['WF']['folder_compile']) echo 'Compile folder is writable';	else echo 'Please Chmod 0777 plugmedia/system/_compile';
			echo '<br>';
			if ( $check['WF']['folder_cache_thumb']) echo 'Cache thumb folder is writable';	else echo 'Please Chmod 0777 plugmedia/system/_cache/thumb';	
			echo '<br>';
			if ( $check['WF']['folder_logs']) echo 'Log folder is writable';	else echo 'Please Chmod 0777 plugmedia/system/logs';	
			echo '<br>';
			if ( $check['WF']['folder_thumb']) echo 'Thumbnail folder is writable';	else echo 'Please Chmod 0777 plugmedia/thumb';
		 ?>        
     </div>
	
    <?
	}
	?>	   
    
    
    	
         </td>
   
  </tr>

  <tr>
  
    <td width="200" bgcolor="#F7F7F7" class="<?= $check['IMRALL']['global_css'] ?>">ImR_All Library</td>
    <td bgcolor="#F7F7F7">
    <?
	if ( $check['IMRALL']['global_status'] == '2')
	{
	?>
    <a class="info"><img src="accept.png" width="16" height="16" /></a>    
    <div class="tooltipTxt" level="2" style="display:none">Library ImR_All is present and executable</div>
	
    <?
	}else{
	?>
     <a class="info"><img src="error.png" width="16" height="16" /></a>
     <div class="tooltipTxt" level="1" style="display:none" >Library ImR_All is no present, it can slow the thumbnail generation process</div>
	
    <?
	}
	?>	

  
    
    </td>  
  
    
  </tr>

  <tr>
    <td width="200" class="<?= $check['EXIF']['global_css'] ?>">Exif support</td>
    <td>
    <?
	if ( $check['EXIF']['global_status'] == '2')
	{
	?>
    <a class="info"><img src="accept.png" width="16" height="16" /></a>    
    <div class="tooltipTxt" level="2" style="display:none">Exif extension loaded</div>
	
    <?
	}else{
	?>
     <a class="info"><img src="error.png" width="16" height="16" /></a>
     <div class="tooltipTxt" level="1" style="display:none" >Exif extension not loaded<br />It will not be possible to extract exif information in pictures</div>
	
    <?
	}
	?>	

  
    
    </td>  


  </tr>

  <tr>

    <td width="200" bgcolor="#F7F7F7" class="<?= $check['SSL']['global_css'] ?>">SSL support</td>
    <td bgcolor="#F7F7F7">
    <?
	if ( $check['SSL']['global_status'] == '2')
	{
	?>
    <a class="info"><img src="accept.png" width="16" height="16" /></a>    
    <div class="tooltipTxt" level="2" style="display:none">SSL extension loaded</div>
	
    <?
	}else{
	?>
     <a class="info"><img src="error.png" width="16" height="16" /></a>
     <div class="tooltipTxt" level="1" style="display:none" >SSL extension not loaded<br />It will not possible to launch plugmedia in https mode</div>
	
    <?
	}
	?>	

  
    
    </td>  
  </tr>

  <tr>
    <td width="200" class="<?= $check['PGSQL']['global_css'] ?>">PHP postgresql support</td>
    <td>
    <?
	if ( $check['PGSQL']['global_status'] == '2')
	{
	?>
    <a class="info"><img src="accept.png" width="16" height="16" /></a>    
    <div class="tooltipTxt" level="2" style="display:none">PHP postgresql extension loaded</div>
	
    <?
	}else{
	?>
     <a class="info"><img src="delete.png" width="16" height="16" /></a>
     <div class="tooltipTxt" level="0" style="display:none" >PHP postgresql extension not loaded<br />Check Postgresql QPKG</div>
	
    <?
	}
	?>	

  
    
    </td> 
  </tr>

  <tr>
    <td width="200" bgcolor="#F7F7F7" class="<?= $check['CGI']['global_css'] ?>">CGI files</td>
    <td bgcolor="#F7F7F7">
    <?
	if ( $check['CGI']['global_status'] == '2')
	{
	?>
    <a class="info"><img src="accept.png" width="16" height="16" /></a>    
    <div class="tooltipTxt" level="2" style="display:none">QNAP NAS ready !!<br /><br />

    
 <?
if ( $check['CGI']['chmod']) echo 'Trace user in Administration console';	else echo 'To trace user in Administration console, please Chmod 0755 /home/httpd/cgi-bin/plugmedia/chmod_db.cgi';
			echo '<br>';
			if ( $check['CGI']['auth']) echo 'Authentication using admin password from the NAS';	else echo 'To authenticate using the admin NAS password, please Chmod 0755 /home/httpd/cgi-bin/plugmedia/cgi_auth.cgi';
			echo '<br>';

	?>    
    
    </div>
	
    <?
	}else{
	?>
     <a class="info"><img src="error.png" width="16" height="16" /></a>
     <div class="tooltipTxt" level="1" style="display:none" >See additional information:<br />
	
    <?
if ( $check['CGI']['chmod']) echo 'Trace user in Administration console';	else echo 'To trace user in Administration console, please Chmod 0755 /home/httpd/cgi-bin/plugmedia/chmod_db.cgi';
			echo '<br>';
			if ( $check['CGI']['auth']) echo 'Authentication using admin password from the NAS';	else echo 'To authenticate using the admin NAS password, please Chmod 0755 /home/httpd/cgi-bin/plugmedia/cgi_auth.cgi';
			echo '<br>';

	?>
    </div>
    
    <?
	}
	?>	

  
    
    </td> 

  </tr>


  <tr>
    <td width="200" class="<?= $check['DB']['global_css'] ?>">Database sheme version</td>
    <td>
    <?
	if ( $check['DB']['global_status'] == '2')
	{
	?>
    <a class="info"><img src="accept.png" width="16" height="16" /></a>
    <div class="tooltipTxt" level="2" style="display:none">System synchronized :<br />

     	<? 
			echo 'Database version : '.$check['DB']['DB_version'];
			echo '<br>';
			echo 'System version : '.$check['DB']['system_version'];
		 ?>  

	</div>
	
    <?
	}else{
	?>
	<a class="info"><img src="delete.png" width="16" height="16" /></a>
   	<div class="tooltipTxt" level="0" style="display:none">The database need to be updated
    
      	<? 
			echo 'Database version : '.$check['DB']['DB_version'];
			echo '<br>';
			echo 'System version : '.$check['DB']['system_version'];
		 ?>    
    
    </div>
	
    <?
	}
	?>	
    
	
    </td>




   
  </tr>


  <tr>
    <td width="200" bgcolor="#F7F7F7" class="<?= $check['GD']['global_css'] ?>">GD enable</td>
    <td bgcolor="#F7F7F7">
    <?
	if ( $check['GD']['global_status'] == '2')
	{
	?>
    <a class="info"><img src="accept.png" width="16" height="16" /></a>    
    <div class="tooltipTxt" level="2" style="display:none">GD is enable<br />

<? 
			echo $check['GD']['gd_string'];
		 ?>    

</div>
	
    <?
	}else{
	?>
     <a class="info"><img src="delete.png" width="16" height="16" /></a>
     <div class="tooltipTxt" level="0" style="display:none" >GD is not available<br />

<? 
			echo $check['GD']['gd_string'];
		 ?>    

</div>
	
    <?
	}
	?>	

  
    
    </td> 


  </tr>
  <tr>
    <td width="200" class="<?= $check['DCRAW']['global_css'] ?>">DCRAW enabled</td>
    <td>
    <?
	if ( $check['DCRAW']['global_status'] == '2')
	{
	?>
    <a class="info"><img src="accept.png" width="16" height="16" /></a>    
    <div class="tooltipTxt" level="2" style="display:none">Dcraw is enable<br />

	<? 
		echo $check['DCRAW']['dcraw_string'];
	?>    

	</div>
	
    <?
	}else{
	?>
     <a class="info"><img src="error.png" width="16" height="16" /></a>
     <div class="tooltipTxt" level="1" style="display:none" >Dcraw is not available<br />To install the extension, just execute this code:<br />
ipkg install dcraw

	</div>
	
    <?
	}
	?>	

  
    
    </td>   
  
  
  
  
    </tr>

  <tr>

    <td width="200" bgcolor="#F7F7F7" class="<?= $check['IM']['global_css'] ?>">ImageMagick support</td>
    <td bgcolor="#F7F7F7">
    <?
	if ( $check['IM']['global_status'] == '2')
	{
	?>
    <a class="info"><img src="accept.png" width="16" height="16" /></a>    
    <div class="tooltipTxt" level="2" style="display:none">ImageMagick is enable<br />

	<? 
		echo $check['IM']['string'];
	?>    

	</div>
	
    <?
	}else{
	?>
     <a class="info"><img src="error.png" width="16" height="16" /></a>
     <div class="tooltipTxt" level="1" style="display:none" >ImageMagick is not available
	</div>
	
    <?
	}
	?>	

  
    
    </td>   
  </tr>



</table>
<br />
<br />
<br />
<br />

<table width="300" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center" valign="top"><a href="http://plugmedia.qnapclub.fr/faq.php" target="_blank"><img src="gnome_applications_office.png" width="64" height="64" /></a></td>
    <td align="center" valign="top"><a href="http://plugmedia.qnapclub.fr/support.php" target="_blank"><img src="gnome_help_faq.png" width="64" height="64" /></a></td>
  </tr>
  <tr>
    <td align="center" valign="top"><a href="http://plugmedia.qnapclub.fr/faq.php" target="_blank">FAQ</a></td>
    <td align="center" valign="top"><a href="http://plugmedia.qnapclub.fr/support.php" target="_blank">HELP & SUPPORT</a></td>
  </tr>
</table>

   
   
   
   
   
   
   
</div>


</body>
</html>







