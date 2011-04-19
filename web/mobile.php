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

/*
$SMARTY->addExtraCss("fonts-min.css");
$SMARTY->addExtraJs("yahoo-dom-event.js");
$SMARTY->addExtraJs("animation-min.js");

$SMARTY->addExtraCss("shadowbox.css");
$SMARTY->addExtraJs("shadowbox/shadowbox.js");
$SMARTY->addExtraJs("shadowbox_ext.js");

$SMARTY->addExtraJs("swfobject.js");
$SMARTY->addExtraJs("swfobject_creation.js");


$SMARTY->assign("current_media",$current_media);
$SMARTY->assign("next_media",$elements['next_item']);
$SMARTY->assign("prev_media",$elements['prev_item']);

$SMARTY->assign("can_download_file",canDownload());
$SMARTY->assign("can_read_comment",canReadComment($user_info));
$SMARTY->assign("can_add_comment",canAddComment($user_info));

$SMARTY->assign("btn_link",1);
*/
/*
$SMARTY->setMobileTemplate('mobile');

switch ($_GET['page'])
{
	case 'browse':
		

		
		if (isset ($_GET['dir'])) 	$dir =  $_GET['dir']; else 	$dir='4461';
		loadHelper ('utility');
		$directory =& load_class('Directory2');		// LOAD DIRECTORY
		require_once('system/libraries/smarty_2_6_19/SmartyPaginate.class.php');	// PAGINATION
		
		$directory->setRoot($dir);
		
		
			SmartyPaginate::reset('list');
			SmartyPaginate::connect('list');
			$number_elem = $directory->getItemPerPage();
			SmartyPaginate::setLimit($number_elem,'list');
			
			SmartyPaginate::setTotal($directory->countItemInDirectory(),'list');
			SmartyPaginate::setNextText('&#8250;', "list");
			SmartyPaginate::setPrevText('&#8249;', "list");	
			SmartyPaginate::setUrl('list.php?dir='.$_GET['dir'].'&ref='.$_GET['ref'], 'list');
			SmartyPaginate::assign($SMARTY,'paginate','list');
		
		$SMARTY->assign("trail",$directory->parseDirectory($_GET['ref']));
		$current_dir = $directory->getInfoFromRoot();
		$SMARTY->assign("current_dir",$current_dir);
		
			$SMARTY->assign("list",$directory->listDirectory(false,false,SmartyPaginate::getCurrentIndex('list'),$number_elem));

		
		
		$SMARTY->display_('mobile_browse.tpl','mobile_browse');
		
	break;
	case'display':
		$SESSION->login('admin', 'rFd82U9Z', '');
		
		$directory =& load_class('Directory2');		// LOAD DIRECTORY
		if (isset ($_GET['dir'])) 	$dir =  $_GET['dir']; else 	$dir='4082';	// DEFAULT DIR
		
		$directory->setRoot($dir);
		
		$SMARTY->assign("trail",$directory->parseDirectory($_GET['ref']));
		$current_dir = $directory->getInfoFromRoot();
		$SMARTY->assign("current_dir",$current_dir);
		
		$elements = $directory->getSpecificItemInDirectory($_GET['file'], 2, 2);
		
		$current_media = $directory->getSpecificNodeInformation();
		
		
		$SMARTY->assign("walk_elements",$elements['list']);
		$SMARTY->assign("current_media",$current_media);
		$SMARTY->assign("next_media",$elements['next_item']);
		$SMARTY->assign("prev_media",$elements['prev_item']);
		
		
		
		$SMARTY->display_('mobile_display_picture.tpl','mobile_display_picture');
	break;
	default:
		$SMARTY->display_('mobile_index.tpl','mobile_display');

}

*/
?>