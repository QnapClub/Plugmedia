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


require_once 'system/core/frontcontroller.php';

exec('/usr/local/apache/bin/php -c /etc/config/php.ini  '.ROOTPATH.'/performQueue.php > /dev/null &'); 

loadHelper ('utility');
loadHelper ('follower');
$directory =& load_class('Directory2');		// LOAD DIRECTORY
require_once('system/libraries/smarty_2_6_19/SmartyPaginate.class.php');	// PAGINATION


if (isset ($_GET['dir'])) 	$dir =  $_GET['dir']; else 	$dir='';


	

// ------- VIEW DETECTION --------------------------------------------------------
if (isset($_GET['defaultview']))
{
	// Envie de changer de vue...
	$SESSION->setDefaultView($_GET['defaultview']);
}




$directory->setRoot($dir);


		


// -------- PAGINATE ----------------------------------
SmartyPaginate::reset('list');
SmartyPaginate::connect('list');
$number_elem = $directory->getItemPerPage();
SmartyPaginate::setLimit($number_elem,'list');

SmartyPaginate::setTotal($directory->countItemInDirectory(),'list');
SmartyPaginate::setNextText('&#8250;', "list");
SmartyPaginate::setPrevText('&#8249;', "list");	
SmartyPaginate::setUrl('list.php?dir='.$_GET['dir'].'&ref='.$_GET['ref'].'&view=inline', 'list');
SmartyPaginate::assign($SMARTY,'paginate','list');
	
$directory_list = $directory->listDirectory(false,false,SmartyPaginate::getCurrentIndex('list'),$number_elem);	

$PLUGIN_MGT->hook( "list_directory_list", &$directory_list);  

$SMARTY->assign("list",$directory_list);

// -------- END PAGINATE  ------------------------------
$pageURL = 'http';
 if (isset($_SERVER['HTTPS'])) {$pageURL .= "s";}
$pageURL .= "://";
$SMARTY->assign("cooliris_value",$pageURL.$_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"]."/plugmedia/"."api.php?ac=cooliris&dir=".$_GET['dir']."&ref=".$_GET['ref']);


// HERE IF THE Database is outdated, no need to continue...
if ($directory->isOutdated() && REVOKE_OUTDATED)
{
	$link = "outdated.php?id=".$_GET['dir']."&ref=".$_GET['ref'];
	if ($_GET['view']=='inline')
	{
		$array_output['ui_content_pm'] =  '<script language="javascript">window.location.href ="'.$link .'"; </script>';
		print(json_encode($array_output));
		exit();
	}
	else
	{
		header("Location:".$link);
		exit();
	}
}



$SMARTY->assign("trail",$directory->parseDirectory($_GET['ref']));
$current_dir = $directory->getInfoFromRoot();
$SMARTY->assign("current_dir",$current_dir);





$SMARTY->assign("btn_cooliris",1);
$SMARTY->assign("btn_thumb",1);
$SMARTY->assign("btn_thumb_list",1);
$SMARTY->assign("btn_list",1);
$SMARTY->assign("btn_slideshow",1);
$SMARTY->assign("btn_radio",1);
if (!isFollower($user_info['id'], $dir))
	$SMARTY->assign("btn_follow",1);
else
	$SMARTY->assign("btn_follow",2);

$default_view = $SESSION->getSettingUser('default_view');

$SMARTY->assign('default_view', $default_view);

if (isset($_GET['view']) && $_GET['view']=='inline')
{
	// output as JSON

	$array_output['breadCrumb0'] =  $SMARTY->fetch('breadcrumb.tpl','breadcrumb');
	
	if ($default_view == 'list')
		$array_output['ui_content_pm'] =  $SMARTY->fetch('list_list_inline.tpl','list_list_inline');
	else
		$array_output['ui_content_pm'] =  $SMARTY->fetch('list_media_content.tpl','list_media_content');
	print(json_encode($array_output));
	print("\n");
	

	
}
else
{
		
	if ($default_view == 'list')
		$SMARTY->display_('list_list.tpl','list_list');
	else
		$SMARTY->display_('list_thumb.tpl','list_thumb');			
}
?>