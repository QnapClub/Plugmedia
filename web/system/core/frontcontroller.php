<?php 
/**
*
* @package Plugmedia
* @copyright (c) 2009 Christophe Lemoine
* @license http://creativecommons.org/licenses/by-nc-nd/2.0/be/  Attribution-Noncommercial-No Derivative Works 2.0 Belgium
* QNAP Systems, Inc is authorize to distribute and transmit the work
*
* Minimum Requirement: PHP 5
*/
error_reporting(E_ALL  & ~E_NOTICE);

define('VERSION_PACKAGE','2.4');

/*if (!isset($no_obgzhandler) || !$no_obgzhandler)
	ob_start("ob_gzhandler");
*/
global $PHP_ERROR;
$system_folder = "system";
$sys_dir = $system_folder;



define('EXT', '.'.pathinfo(__FILE__, PATHINFO_EXTENSION));
define('ROOTPATH', '/share/Qweb/plugmedia');
define('BASEPATH', ROOTPATH.'/system/');
define('SYS_FOLD',$system_folder);
ini_set("include_path", BASEPATH.'libraries/MDB2/');
require BASEPATH.'libraries/MDB2/MDB2.php';



// NEEDED FOR LOGIN, ACCESSING QNAP DATABASE
$QNAP_HTTPD_PORT = exec("/sbin/getcfg SYSTEM \"Web Access Port\" -f /etc/config/uLinux.conf");    // Default is 8080 but ...
$FORCE_SSL = exec("/sbin/getcfg SYSTEM \"Force SSL\" -f /etc/config/uLinux.conf");
$QNAP_HTTPS_PORT = exec("/sbin/getcfg Stunnel \"Port\" -f /etc/config/uLinux.conf");
if ($FORCE_SSL == 1)
{
	if ($QNAP_HTTPS_PORT != 443)
		$contact_port = ":".$QNAP_HTTPS_PORT;
	else
		$contact_port = "";
	$protocol = "https";
}
else
{
	$protocol = "http";
	$contact_port = ":".$QNAP_HTTPD_PORT;
}
define ('FORCE_SSL',$FORCE_SSL);
define ('HTTPD_URL',$protocol."://127.0.0.1".$contact_port."/cgi-bin/plugmedia/");


require(BASEPATH.'core/common'.EXT);
set_error_handler('_exception_handler');




// TIC TAC TIC TAC TIC TAC
$BM =& load_class('Benchmark');
$BM->mark('total_execution_time_start');

// LOADING DEFAULT CLASS

$BM->mark('loading_time_base_classes_start');
$DB =& load_class('Db_postgresql');
if (!$DB->connexionbd())
{
	show_error($DB->getError());
	exit();
}


$CONFIGDB =& load_class('ConfigLoader');
$db_config = $CONFIGDB->getDbConfig();

define('SMALLTHUMB_HEIGHT',$CONFIGDB->getValue('SMALLTHUMB_HEIGHT'));
define('SMALLTHUMB_WIDTH',$CONFIGDB->getValue('SMALLTHUMB_WIDTH'));
define('NORMALTHUMB_HEIGHT',$CONFIGDB->getValue('PICTURE_MAX_HEIGHT'));
define('NORMALTHUMB_WIDTH',$CONFIGDB->getValue('PICTURE_MAX_WIDTH'));


$CFG =& load_class('Config');
$SMARTY =& load_class('Smarty');
$ERROR =& load_class('Error');

/**** PLUGIN MANAGEMENT **/

$PLUGIN_MGT =& load_class('PluginManagement');
$PLUGIN =& load_class('Plugin');
$PLUGIN_MGT->initialize();  
$PLUGIN_MGT->hook( "PluginLoad");
  
/*************************/


$SESSION =& load_class('Session');
$SORTING_ORDER =& load_class('SortingOrdering');	// SORTING ORDERING
$SESSION->setData('order',$SORTING_ORDER->getOrdering());
$SESSION->setData('tris',$SORTING_ORDER->getSorting());



// GETTING INFORMATIONS ON STARTING FOLDER
define('STARTING_FOLDER',$db_config['STARTING_DIRECTORY']); 
// GET INFORMATION TO REVOKE DATABASE
define ('REVOKE_OUTDATED', (bool)$db_config['REVOKE_OUTDATED']);


define('CGI_AUTH',(bool)$db_config['CGI_AUTH_INTERFACE']); 
if (file_exists("/usr/local/sbin/ImR_all")) define('IMR_ALL', true); else define('IMR_ALL', false);

define('FFMPEG', '/usr/bin/ffmpeg');
if (file_exists(FFMPEG)) 
	define('FFMPEG_LIB', true); 
else 
	define('FFMPEG_LIB', false);

define('MOBILE_CONVERSION_FFMPEG',true); // !!!!! have to be in DATABASE with user config


$is_loggedin = $SESSION->isLoggedin();	// IS USER LOGGEDIN?


$BM->mark('loading_time_base_classes_end');
// END LOADING DEFAULT CLASS

// ------- LANGUAGE DETECTION --------------------------------------------------------
$i18n =& load_class('I18n');

$tab_lang = $CONFIGDB->getValue('AVAILABLE_LANG');
if (isset($_GET['lang']))
{
	// Envie de changer de langue...
	$i18n->setLanguage($_GET['lang']);
	$SESSION->setLanguage($_GET['lang']);
	$url=str_replace("&lang=".$_GET['lang'], "", $_SERVER['REQUEST_URI']); 
	$url=str_replace("?lang=".$_GET['lang'], "", $url); 
	header("Location:".$url);
}
else
	$i18n->setLanguage($SESSION->detectionLanguage());
	

// -------  END LANGUAGE DETECTION --------------------------------------------------------



if ($is_loggedin)
{
	$user_info = $SESSION->getUser_informations();
	$SMARTY->assign("user_info",$user_info);
	$SMARTY->assign("loggedin",$is_loggedin);
}

$SMARTY->assign("ffmpeg_lib_support",FFMPEG_LIB);


$SMARTY->assign("available_lang",$tab_lang);
$current_lang = $i18n->getCurrent_lang();
$SMARTY->assign("current_lang_short",$i18n->getCurrent_lang());
$SMARTY->assign("current_lang",$tab_lang[$i18n->getCurrent_lang()]);
if (array_key_exists('REQUEST_URI',$_SERVER))
{
	if (empty($_GET))
		$SMARTY->assign("current_file",$_SERVER['REQUEST_URI']."?");
	else
		$SMARTY->assign("current_file",$_SERVER['REQUEST_URI']."&");
}
else
	$SMARTY->assign("current_file",'');	


$SMARTY->addExtraJs("cooliris-min.js");

if (array_key_exists('HTTP_HOST',$_SERVER))
	$SMARTY->assign("PM_LOCATION",$_SERVER['HTTP_HOST'].'/plugmedia');
else
	$SMARTY->assign("PM_LOCATION",'');
if (array_key_exists('PHP_SELF',$_SERVER))	
	$SMARTY->assign("page_location",$_SERVER['PHP_SELF']);
else
	$SMARTY->assign("page_location",'');


$BM->mark('total_execution_time_intermediate');


$SMARTY->addExtraCss("shadowbox.css");
$SMARTY->addExtraJs("shadowbox/shadowbox.js");
$SMARTY->addExtraJs("swfobject.js");
$SMARTY->addExtraJs("swfobject_creation.js");
if (isset($_GET['dir']) && isset($_GET['ref']))
$SMARTY->assign("cooliris",'<link rel="alternate" href="api.php?ac=cooliris&dir='.$_GET['dir'].'&ref='.$_GET['ref'].'" type="application/rss+xml" title="" id="gallery" />');


$AGENT =& load_class('User_agent');
if ($AGENT->is_mobile())
{	
	// this is a mobile device, SET correct theme...
	$SMARTY->setMobileTemplate($reference);
}



$PLUGIN_MGT->hook( "frontcontroller_end");  




?>
