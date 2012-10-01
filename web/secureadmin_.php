<?php
/**
*
* @package Plugmedia
* @copyright (c) 2009 Christophe Lemoine
* @license http://creativecommons.org/licenses/by-nc-nd/2.0/be/  Attribution-Noncommercial-No Derivative Works 2.0 Belgium
* QNAP Systems, Inc is autorize to distribute and transmit the work
*
* Minimum Requirement: PHP 5
*/

require_once 'system/core/frontcontroller.php';
loadHelper ('user_management');
loadHelper ('administration');

// MAP OF URLS
$array_trail['ADMINISTRATION']['name'] = $i18n->translate('ADMINISTRATION');
$array_trail['ADMINISTRATION']['link'] = 'secureadmin_.php?act=summary&view=inline';
$array_trail['USERLIST']['name'] = $i18n->translate('USERSLIST');
$array_trail['USERLIST']['link'] = 'secureadmin_.php?act=list_user&view=inline';
$array_trail['ADDUSER']['name'] = $i18n->translate('ADDUSER');
$array_trail['ADDUSER']['link'] = 'secureadmin_.php?act=add_user&view=inline';
$array_trail['EDITUSER']['name'] = $i18n->translate('EDITUSER_');
$array_trail['EDITUSER']['link'] = '';
$array_trail['GROUPLIST']['name'] = $i18n->translate('GROUPLIST');
$array_trail['GROUPLIST']['link'] = 'secureadmin_.php?act=list_group&view=inline';
$array_trail['EDITGROUP']['name'] = $i18n->translate('EDITGROUP');
$array_trail['EDITGROUP']['link'] = '';
$array_trail['CONFIGURATION']['name'] = $i18n->translate('CONFIGURATION');
$array_trail['CONFIGURATION']['link'] = '';


if ($is_loggedin && $user_info['admin'])
{
	if (!array_key_exists('act',$_GET) || !is_string($_GET['act']))
		$_GET['act'] = "xx";

	switch ($_GET['act'])
	{
		case 'summary':
			if (isset($_GET['vcheck']))
				$SMARTY->assign('version_check_result',versionCheck(true));	
			else
				$SMARTY->assign('version_check_result',versionCheck(false));	
				
			$SMARTY->addExtraCss("administration.css");
			
			$SMARTY->assign('total_new_comment',countNewComments());

			$SMARTY->assign('trail',array(
						array('reference'=>$array_trail['ADMINISTRATION']['name'],'link'=>$array_trail['ADMINISTRATION']['link'])
			));
			
			if ($_GET['view']=='inline')
			{
				// output as JSON
				$array_output['breadCrumb0'] =  $SMARTY->fetch('breadcrumb.tpl','breadcrumb');
				$array_output['ui_content_pm'] =  $SMARTY->fetch('admin_summary.tpl','admin_summary');
				print(json_encode($array_output));
			}
			else
				$SMARTY->display_('admin_summary.tpl','admin_summary');	
		break;
		case 'list_user':
			syncUsers();
			if (isset($_POST['delusr']))
				removeUsers($_POST['delusr']);	
			$SMARTY->assign('listuser',getUserList());
			
			$SMARTY->assign('trail',array(
						array('reference'=>$array_trail['ADMINISTRATION']['name'],'link'=>$array_trail['ADMINISTRATION']['link']),
						array('reference'=>$array_trail['USERLIST']['name'],'link'=>$array_trail['USERLIST']['link'])
			));
		
			if ($_GET['view']=='inline')
			{
				// output as JSON
				$array_output['breadCrumb0'] =  $SMARTY->fetch('breadcrumb.tpl','breadcrumb');
				$array_output['ui_content_pm'] =  $SMARTY->fetch('admin_list_user.tpl','admin_list_user');
				print(json_encode($array_output));
			}
			else			
				$SMARTY->display_('admin_list_user.tpl','admin_list_user');	
		break;
		case 'list_comment':
			if (isset($_POST['delcom']))
				deleteComments($_POST['delcom']);	
			readNewComments();
			$SMARTY->assign('trail',array(
						array('reference'=>$array_trail['ADMINISTRATION']['name'],'link'=>$array_trail['ADMINISTRATION']['link'])
			));
			$SMARTY->assign('listcomment',getComments_admin());
			$SMARTY->addExtraCss("administration.css");
			if ($_GET['view']=='inline')
			{
				// output as JSON
				$array_output['breadCrumb0'] =  $SMARTY->fetch('breadcrumb.tpl','breadcrumb');
				$array_output['ui_content_pm'] =  $SMARTY->fetch('admin_list_comment.tpl','admin_list_comment');
				print(json_encode($array_output));
			}
			else			
			
			$SMARTY->display_('admin_list_comment.tpl','admin_list_comment');	
		break;		
		case 'add_user':
			global $tab_lang;
			syncUsers();
			if (isset($_POST['login']))
			{
				if (createUser($_POST['login'],$_POST['name'],$_POST['password'], $_POST['password1'], $_POST['email'], $_POST['group_member'],$_POST['lang']))
					$ERROR->addError('USERADDED', 'Information',$_POST['login']);
			}
			$SMARTY->addExtraJs("jquery/jquery.multiselect.js");

			loadHelper ('user_management');
			$list_gr = getGroupList();
			
			$SMARTY->assign('list_grp',$list_gr);

			$SMARTY->assign('trail',array(
						array('reference'=>$array_trail['ADMINISTRATION']['name'],'link'=>$array_trail['ADMINISTRATION']['link']),
						array('reference'=>$array_trail['USERLIST']['name'],'link'=>$array_trail['USERLIST']['link']),
						array('reference'=>$array_trail['ADDUSER']['name'],'link'=>$array_trail['ADDUSER']['link'])
			));

			if ($_GET['view']=='inline')
			{
				// output as JSON
				$array_output['breadCrumb0'] =  $SMARTY->fetch('breadcrumb.tpl','breadcrumb');
				$array_output['ui_content_pm'] =  $SMARTY->fetch('admin_add_user.tpl','admin_add_user');
				print(json_encode($array_output));
			}
			else			
				$SMARTY->display_('admin_add_user.tpl','admin_add_user');	
		break;	
		case 'edit_user':
			syncUsers();
			loadHelper ('user_management');
			$available_group = getGroupList();
			$user_group_list = getUserInfo($_GET['usr']);
			$user_group_list = explode(";", $user_group_list['groups'] );
			foreach ($available_group as $key=>$groupe)
			{
				if (in_array($groupe['name'],$user_group_list))
					$available_group[$key]['checked'] = true;
			}
			$SMARTY->assign('list_grp',$available_group);
			$SMARTY->assign("user_info",getUserInfo($_GET['usr']));
			$SMARTY->assign("user_group_list",getGroupFromOneUser($_GET['usr']));

			$SMARTY->assign('trail',array(
						array('reference'=>$array_trail['ADMINISTRATION']['name'],'link'=>$array_trail['ADMINISTRATION']['link']),
						array('reference'=>$array_trail['USERLIST']['name'],'link'=>$array_trail['USERLIST']['link']),
						array('reference'=>$array_trail['EDITUSER']['name'],'link'=>$array_trail['EDITUSER']['link'])
			));

			
			if ($_GET['view']=='inline')
			{
				// output as JSON
				$array_output['breadCrumb0'] =  $SMARTY->fetch('breadcrumb.tpl','breadcrumb');
				$array_output['ui_content_pm'] =  $SMARTY->fetch('admin_edit_user.tpl','admin_edit_user');
				print(json_encode($array_output));
			}
			else
				$SMARTY->display_('admin_edit_user.tpl','admin_edit_user');	
			
		break;	
		
		case 'list_group':
			syncUsers();
			if (isset($_POST['delgrp']))
				removeGroups($_POST['delgrp']);	

			$SMARTY->assign('trail',array(
						array('reference'=>$array_trail['ADMINISTRATION']['name'],'link'=>$array_trail['ADMINISTRATION']['link']),
						array('reference'=>$array_trail['GROUPLIST']['name'],'link'=>$array_trail['GROUPLIST']['link'])
			));

			
			$SMARTY->assign('grouplist',getGroupList());			
			$SMARTY->addExtraCss("administration.css");
			if ($_GET['view']=='inline')
			{
				// output as JSON
				$array_output['breadCrumb0'] =  $SMARTY->fetch('breadcrumb.tpl','breadcrumb');
				$array_output['ui_content_pm'] =  $SMARTY->fetch('admin_list_group.tpl','admin_list_group');
				print(json_encode($array_output));
			}
			else
				$SMARTY->display_('admin_list_group.tpl','admin_list_group');	
		break;
		case 'edit_group':
			syncUsers();
			loadHelper ('directory');
			$info_group = getGroupInfo($_GET['grp']);
			$SMARTY->assign("group_info",$info_group);
			$available_users = getUserList();
			$users_in_group = getUserListAvailableForGroup($_GET['grp'],$info_group['usrs']);
		
			foreach ($available_users as $key=>$users)
			{
				if (!in_array($users['login'],$users_in_group))
					$available_users[$key]['checked'] = true;
				else
					$available_users[$key]['checked'] = false;
			}

			$SMARTY->assign('trail',array(
						array('reference'=>$array_trail['ADMINISTRATION']['name'],'link'=>$array_trail['ADMINISTRATION']['link']),
						array('reference'=>$array_trail['GROUPLIST']['name'],'link'=>$array_trail['GROUPLIST']['link']),
						array('reference'=>$array_trail['EDITGROUP']['name'],'link'=>$array_trail['EDITGROUP']['link'])
			));

			
			$SMARTY->assign("users_available",$available_users);
			
			$SMARTY->assign("root_node",getRootNodeInfo());
			
			$SMARTY->assign("group_info_path",recursiveParentFromPathArray( $info_group['path_access_array']));

			if ($_GET['view']=='inline')
			{
				// output as JSON
				$array_output['breadCrumb0'] =  $SMARTY->fetch('breadcrumb.tpl','breadcrumb');
				$array_output['ui_content_pm'] =  $SMARTY->fetch('admin_edit_group.tpl','admin_edit_group');
				print(json_encode($array_output));
			}
			else
				$SMARTY->display_('admin_edit_group.tpl','admin_edit_group');	
			
		break;	
			
		case 'config':

			$configDB = load_class('ConfigLoader');
	

			
			$SMARTY->addExtraCss("administration.css");
					
			
			/* DELETE FILENAME MASK */
			if (isset($_POST['del_mask_n']))
				deleteConfigurationFilenames($_POST['del_mask_n']);	
			
			if (isset($_POST['del_mask_e']))
				deleteConfigurationExtensions($_POST['del_mask_e']);	
			
			if (isset($_POST['add_mask_n']))
				addConfigurationFilename($_POST['mask_n']);
			if (isset($_POST['add_mask_e']))
				addConfigurationExtension($_POST['mask_e']);
			if (isset($_POST['change_auth']))
				updateConfigurationAutorization($_POST['allow_dwl']);
			if (isset($_POST['change_processing']))
				updateConfigurationProcessing($_POST['revokeoutdated'], $_POST['extract_id3'],$_POST['id3_extract'], $_POST['cover_lastfm']);				
		
			if (isset($_POST['modify_visu']))
				updateConfigurationVisualization($_POST['item_pp'],$_POST['get_first'], $_POST['exif_autorotate']);

			if (isset($_POST['change_auto_check']))
				updateConfigurationPlugmedia($_POST['allow_auto_check'],$_POST['pm_url'],$_POST['pm_admin_mail']);

			if (isset($_POST['change_thumbnail']))
				updateConfigurationThumbnail($_POST['thumb_small_height'],$_POST['thumb_small_width'],$_POST['normal_small_height'],$_POST['normal_small_width']);



			if (isset($_GET['reset_dwnl']))
				$configDB->resetValue('AUTORIZE_DOWNLOAD');
			if (isset($_GET['reset_firstpic']))
				$configDB->resetValue('GET_FIRST_PICTURE');
			if (isset($_GET['reset_autor']))
				$configDB->resetValue('EXIF_AUTOROTATE');
			if (isset($_GET['reset_itempp']))
				$configDB->resetValue('ITEM_PER_PAGE');
			if (isset($_GET['reset_revoke']))
				$configDB->resetValue('REVOKE_OUTDATED');
			if (isset($_GET['reset_id3extr']))
				$configDB->resetValue('ID3_EXTRACT');
			if (isset($_GET['reset_id3_cover']))
				$configDB->resetValue('EXTRACT_COVER_FROM_ID3');
			if (isset($_GET['resetlastfmcover']))
				$configDB->resetValue('EXTRACT_COVER_FROM_LASTFM');
			if (isset($_GET['reset_mask_filn']))
				$configDB->resetValue('HIDDING_MASK');
			if (isset($_GET['reset_mask_ext']))
				$configDB->resetValue('HIDDING_EXTENSION');
			if (isset($_GET['reset_auto_check']))
				$configDB->resetValue('AUTO_CHECK_UPDATE');
			if (isset($_GET['reset_pm_url']))
				$configDB->resetValue('PLUGMEDIA_URL');
			if (isset($_GET['reset_pm_admin_mail']))
				$configDB->resetValue('PLUGMEDIA_ADMIN_EMAIL');
			if (isset($_GET['reset_thumbsmall']))
			{
				$configDB->resetValue('SMALLTHUMB_WIDTH');
				$configDB->resetValue('SMALLTHUMB_HEIGHT');
			}
			if (isset($_GET['reset_thumb_normal']))
			{
				$configDB->resetValue('PICTURE_MAX_HEIGHT');
				$configDB->resetValue('PICTURE_MAX_WIDTH');
			}			
			$configDB->refreshConfig();
		

			$SMARTY->assign('revoke_outdated',$configDB->getValue('REVOKE_OUTDATED'));
			$SMARTY->assign('revoke_outdated_reset',$configDB->canResetValue('REVOKE_OUTDATED'));

			$SMARTY->assign('id3_extract',$configDB->getValue('ID3_EXTRACT'));
			$SMARTY->assign('id3_extract_reset',$configDB->canResetValue('ID3_EXTRACT'));
			
			$SMARTY->assign('id3_cover',$configDB->getValue('EXTRACT_COVER_FROM_ID3'));
			$SMARTY->assign('id3_cover_reset',$configDB->canResetValue('EXTRACT_COVER_FROM_ID3'));
	
			$SMARTY->assign('cover_lastfm',$configDB->getValue('EXTRACT_COVER_FROM_LASTFM'));
			$SMARTY->assign('cover_lastfm_reset',$configDB->canResetValue('EXTRACT_COVER_FROM_LASTFM'));
	
		
			
			$SMARTY->assign('allow_download',canDownload());
			$SMARTY->assign('allow_download_reset',$configDB->canResetValue('AUTORIZE_DOWNLOAD'));
			
			$SMARTY->assign('mask_name',$configDB->getValue('HIDDING_MASK'));	
			$SMARTY->assign('mask_name_reset',$configDB->canResetValue('HIDDING_MASK'));

			$SMARTY->assign('mask_extension',$configDB->getValue('HIDDING_EXTENSION'));
			$SMARTY->assign('mask_extension_reset',$configDB->canResetValue('HIDDING_EXTENSION'));			
			
			$SMARTY->assign('item_per_page',$configDB->getValue('ITEM_PER_PAGE'));
			$SMARTY->assign('item_per_page_reset',$configDB->canResetValue('ITEM_PER_PAGE'));
			
			$SMARTY->assign('get_first_picture',(bool) $configDB->getValue('GET_FIRST_PICTURE'));
			$SMARTY->assign('get_first_picture_reset',$configDB->canResetValue('GET_FIRST_PICTURE'));

			$SMARTY->assign('exif_autorotate',(bool) $configDB->getValue('EXIF_AUTOROTATE'));	
			$SMARTY->assign('exif_autorotate_reset',$configDB->canResetValue('EXIF_AUTOROTATE'));
			
			$SMARTY->assign('memory_limit',$configDB->getValue('PHP_MEMORY_LIMIT'));

			$SMARTY->assign('auto_check',$configDB->getValue('AUTO_CHECK_UPDATE'));
			$SMARTY->assign('auto_check_reset',$configDB->canResetValue('AUTO_CHECK_UPDATE'));			

			$SMARTY->assign('pm_url',$configDB->getValue('PLUGMEDIA_URL'));
			$SMARTY->assign('pm_url_reset',$configDB->canResetValue('PLUGMEDIA_URL'));	
			$SMARTY->assign('pm_default','http://'.$_SERVER['HTTP_HOST'].'/plugmedia');

			$SMARTY->assign('pm_admin_mail',$configDB->getValue('PLUGMEDIA_ADMIN_EMAIL'));
			$SMARTY->assign('pm_admin_mail_reset',$configDB->canResetValue('PLUGMEDIA_ADMIN_EMAIL'));	

			$SMARTY->assign('thumb_small_h',$configDB->getValue('SMALLTHUMB_HEIGHT'));
			$SMARTY->assign('thumb_small_w',$configDB->getValue('SMALLTHUMB_WIDTH'));
			if ($configDB->canResetValue('SMALLTHUMB_WIDTH') || $configDB->canResetValue('SMALLTHUMB_HEIGHT'))
				$SMARTY->assign('thumb_small_reset',true);
			else
				$SMARTY->assign('thumb_small_reset',false);	
				
			$SMARTY->assign('thumb_normal_h',$configDB->getValue('PICTURE_MAX_HEIGHT'));
			$SMARTY->assign('thumb_normal_w',$configDB->getValue('PICTURE_MAX_WIDTH'));
			if ($configDB->canResetValue('PICTURE_MAX_WIDTH') || $configDB->canResetValue('PICTURE_MAX_HEIGHT'))
				$SMARTY->assign('thumb_normal_reset',true);
			else
				$SMARTY->assign('thumb_normal_reset',false);
				
				
			$SMARTY->assign('trail',array(
						array('reference'=>$array_trail['ADMINISTRATION']['name'],'link'=>$array_trail['ADMINISTRATION']['link']),
						array('reference'=>$array_trail['CONFIGURATION']['name'],'link'=>$array_trail['CONFIGURATION']['link'])
			));

			if ($_GET['view']=='inline')
			{
				// output as JSON
				$array_output['breadCrumb0'] =  $SMARTY->fetch('breadcrumb.tpl','breadcrumb');
				$array_output['ui_content_pm'] =  $SMARTY->fetch('admin_config.tpl','admin_config');
				
				print(json_encode($array_output));
			}
			else			
				$SMARTY->display_('admin_config.tpl','admin_config');	
		break;	
		
		case 'add_group':
			syncUsers();
			loadHelper ('directory');
			if (isset($_POST['groupname']))
			{
				if (createGroup($_POST['groupname'], $_POST['user_group'], $_POST['selectX']))
					$ERROR->addError('GROUPADDED', 'Information',$_POST['groupname']);
			}
			$SMARTY->assign("root_node",getRootNodeInfo());
			
			$SMARTY->addExtraJs("jquery/jquery.multiselect.js");
			$SMARTY->assign('userlist',getUserList());
			if ($_GET['view']=='inline')
			{
				// output as JSON
				$array_output['breadCrumb0'] =  $SMARTY->fetch('breadcrumb.tpl','breadcrumb');
				$array_output['ui_content_pm'] =  $SMARTY->fetch('admin_add_group.tpl','admin_add_group');
				print(json_encode($array_output));
			}
			else			
				$SMARTY->display_('admin_add_group.tpl','admin_add_group');	
		break;

		case 'indexing':
			$SMARTY->assign('trail',array(
						array('reference'=>$array_trail['ADMINISTRATION']['name'],'link'=>$array_trail['ADMINISTRATION']['link'])
			));
			$SMARTY->addExtraCss("administration.css");
			if ($_GET['view']=='inline')
			{
				// output as JSON
				$array_output['breadCrumb0'] =  $SMARTY->fetch('breadcrumb.tpl','breadcrumb');
				$array_output['ui_content_pm'] =  $SMARTY->fetch('admin_indexing.tpl','admin_indexing');
				print(json_encode($array_output));
			}
			else			
				$SMARTY->display_('admin_indexing.tpl','admin_indexing');	
		break;

		case 'plugins':

			$SMARTY->assign('trail',array(
						array('reference'=>$array_trail['ADMINISTRATION']['name'],'link'=>$array_trail['ADMINISTRATION']['link'])
			));
			
			switch ($_GET['pl'])
			{
				case 'enable':
					$PLUGIN_MGT->enablePlugin((int)$_GET['id_p']);
					//header ("Location: ".$_SERVER['PHP_SELF']."?act=plugins");
				break;
				case 'disable':
					$PLUGIN_MGT->disablePlugin((int)$_GET['id_p']);
					//header ("Location: ".$_SERVER['PHP_SELF']."?act=plugins");
				break;
			}
		
			$SMARTY->addExtraCss("administration.css");
			$SMARTY->assign('listplugin',$PLUGIN_MGT->getListEnabledPlugin());
			$SMARTY->assign('listplugindisabled',$PLUGIN_MGT->getListDisabledPlugin());
			if ($_GET['view']=='inline')
			{
				// output as JSON
				$array_output['breadCrumb0'] =  $SMARTY->fetch('breadcrumb.tpl','breadcrumb');
				$array_output['ui_content_pm'] =  $SMARTY->fetch('admin_plugins.tpl','admin_plugins');
				print(json_encode($array_output));
			}
			else			
				$SMARTY->display_('admin_plugins.tpl','admin_plugins');	
		break;		
			
		case 'thumbandtranscode':
			$SMARTY->assign('trail',array(
						array('reference'=>$array_trail['ADMINISTRATION']['name'],'link'=>$array_trail['ADMINISTRATION']['link'])
			));
			$SMARTY->addExtraCss("administration.css");
			if ($_GET['view']=='inline')
			{
				// output as JSON
				$array_output['breadCrumb0'] =  $SMARTY->fetch('breadcrumb.tpl','breadcrumb');
				$array_output['ui_content_pm'] =  $SMARTY->fetch('admin_thumb_transcoding.tpl','admin_thumb_transcoding');
				print(json_encode($array_output));
			}
			else			
				$SMARTY->display_('admin_thumb_transcoding.tpl','admin_thumb_transcoding');	
		break;			
			
			
		default:
			header("Location: secureadmin_.php?act=summary");
		break;				
	}
}
else
	header("Location: index.php");

?>