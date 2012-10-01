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
loadHelper ('picture');
loadHelper ('utility');
loadHelper ('administration');
loadHelper ('comments');
loadHelper ('metadata_management');

$directory = load_class('Directory2');		// LOAD DIRECTORY



if (isset ($_GET['dir'])) 	$dir =  $_GET['dir']; else 	$dir='';	// DEFAULT DIR

$directory->setRoot($dir);



$SMARTY->assign("trail",$directory->parseDirectory($_GET['ref']));
$current_dir = $directory->getInfoFromRoot();
$SMARTY->assign("current_dir",$current_dir);

$elements = $directory->getSpecificItemInDirectory($_GET['file'], 2, 2);

$current_media = $directory->getSpecificNodeInformation();


$SMARTY->assign("walk_elements",$elements['list']);



$SMARTY->assign("time",time());


$SMARTY->assign("current_media",$current_media);
$SMARTY->assign("next_media",$elements['next_item']);
$SMARTY->assign("prev_media",$elements['prev_item']);

$SMARTY->assign("can_download_file",canDownload());
$SMARTY->assign("can_read_comment",canReadComment($user_info));
$SMARTY->assign("can_add_comment",canAddComment($user_info));
$SMARTY->assign("can_manage_metadata",canManageMetadata($user_info));


$array_tag = getTags($_GET['file']);
$SMARTY->assign("tag_list_array",$array_tag);
$SMARTY->assign("tag_list",getTagsToString($array_tag));




$SMARTY->assign("btn_link",1);

if ($_GET['view']=='inline')
{
	// output as JSON
	$array_output['breadCrumb0'] =  $SMARTY->fetch('breadcrumb.tpl','breadcrumb');
	$array_output['ui_content_pm'] =  $SMARTY->fetch('display_media.tpl','display_media');
	if (!isset($_GET['nojson']))
		print(json_encode($array_output));
	else
		echo $array_output['ui_content_pm'];
}
else
	$SMARTY->display_('display.tpl','display');

?>