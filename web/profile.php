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
loadHelper ('follower');
loadHelper ('user_management');
loadHelper ('utility');

if (!$is_loggedin)
{
	redirectNonAuthorizedUser();
}

if (isset($_POST['delfollow']))
{	
	removeFollowers($user_info['id'], $_POST['delfollow']);	
}

if( isset($_GET['avatar']) && ($_GET['avatar'] != '') ) 
{
	changeAvatar($user_info['id'], $_GET['avatar']);
	header("location: profile.php");
}


$SMARTY->addExtraCss("multiselect.css");

$SMARTY->assign("follower_list",followerList($user_info['id']));

$SMARTY->assign("avatar_list",listAvatar());


if ($_GET['view']=='inline')
{
	// output as JSON
	$array_output['breadCrumb0'] =  $SMARTY->fetch('breadcrumb.tpl','breadcrumb');
	$array_output['ui_content_pm'] =  $SMARTY->fetch('profile_inline.tpl','profile_inline');
	print(json_encode($array_output));
}
else
	$SMARTY->display_('profile.tpl','profile');

?>