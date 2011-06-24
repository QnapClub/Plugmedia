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
if ($_GET['ac'] == 'radio')
	$no_obgzhandler = true;

require_once 'system/core/frontcontroller.php';

switch ($_GET['ac'])
{
	case 'cooliris':
		loadHelper ('cooliris');
		getCoolirisRss($_GET['dir']);
	break;
	case 'radio':
	
		$streamRadio =& load_class('StreamRadio');
		$streamRadio->generatePlaylist($_GET['token'], $_GET['id']);
		$streamRadio->start();
	break;
	case 'logout':
		$SESSION->logout();
		header("Location:index.php");
	break;	

	case 'getdir':
		loadHelper ('directory');
		header("Content-Type: application/json");
		echo json_encode(GetAllDirectory($_POST['node']));
	break;
	case 'getalloweddir':
		loadHelper ('directory');
		header("Content-Type: application/json");
		echo json_encode(getAllowedDirectory($_POST['node']));
	break;	
	case 'getimg_turing':
		loadHelper ('turing');
		$img = getPicture();
		header('Expires: Thu, 19 Nov 1981 08:52:00 GMT');
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
		header("Cache-Control: no-store, no-cache, must-revalidate"); 
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header("Content-Type: image/png");
		imagePNG($img);
		imagedestroy($img);	
	break;
	case 'rotatePic':
/*		loadHelper ('picture'); 
		if (!isset($_GET['percent']))
			$_GET['percent'] = 1;
		renderPicture($_GET['pic'],$_GET['percent']);
*/
		loadHelper ('thumbnail');
		
		$return = generateThumbnail($_GET['pic'], false, 'big', true);
		
	break;
	case 'genth':
		loadHelper ('thumbnail');
		$return = generateThumbnail($_GET['img'], false, 'small');
		list($width, $height, $type, $attr) = getimagesize($return);
		if ($width > $height)
			$val = ' width="'.(400).'" style="width: 4em;" ';
		else
			$val = ' height="'.(400).'" style="height: 4em;"';
		
		echo($return.'|'.$_GET['img'].'|'.$val);
	break;
	case 'generateToolbar':
		loadHelper ('utility');
		echo (generateToolbar());
	break;
	case 'login':
		if ($_GET['type']=='json')
		{
			header("Content-Type: application/json");
			if (isset ($_POST['login']))
			{
				if ($SESSION->login($_POST['login'], $_POST['password'], 'index.php'))
					echo json_encode(true);
				else
					echo json_encode(false);
			}else
				echo json_encode(false);
			
			
		}else{
			echo 1;
		}
			
	break;	
	case 'getHelpPage':
		echo $SMARTY->fetch('help.tpl');
	break;
	case 'getCopylink':
		loadHelper ('utility');
		$SMARTY->assign("server_name",$_SERVER['HTTP_HOST']);
		$SMARTY->assign("link_pic",$_GET['pic']);
		echo $SMARTY->fetch('link_it.tpl');	
	break;
	
	case 'getPlsFile':
		global $user_info, $DB;
		// is the user get the same radio?
		$DB->query("SELECT * FROM radio_token WHERE id_creator = '".$user_info['id']."' AND id_directory = '".$_GET['dir']."'","API getPlsFile");
		$val = $DB->fetchrow();
		if (isset($val['token']))
		{
			// already created this radio 
			$DB->query("UPDATE radio_token SET last_access_date = CURRENT_TIMESTAMP WHERE id_creator = '".$user_info['id']."' AND id_directory = '".$_GET['dir']."'","API getPlsFile");
			$token = $val['token'];
		}
		else
		{
			// generate a token for the user
			loadHelper ('utility');
			$token = createToken('', 2, 4);
			
			$DB->query("INSERT INTO radio_token (token,id_creator,id_directory,create_date,last_access_date) VALUES ('$token','".$user_info['id']."','".$_GET['dir']."',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP)","API getPlsFile");
		}
		
		
		
		$string = "[playlist]\n";
		$string .= "NumberOfEntries=1\n";
		$string .= "File1=http://".$_SERVER['HTTP_HOST']."/plugmedia/api.php?ac=radio&token=".$token."&id=".$user_info['id']."\n";
		$string .= "Title1=Qnap Plugmedia Stream\n";
		$string .= "Length1=-1\n";
		$string .= "Version=2\n";
		
		$taillestr = strlen($string);
		$sizestr = ceil( (float)$taillestr / 16.0 );
		$strbyte = chr($sizestr);
		
		header("Content-Type: application/force-download;");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: $strbyte");
		header("Content-Disposition: attachment; filename=\"playlist.pls\"");
		header("Expires: 0");
		header("Cache-Control: no-cache, must-revalidate");
		header("Pragma: no-cache");
		echo $string;
		exit(); 
	break;
	case 'getGroupListAvailable':
		if ($is_loggedin && $user_info['admin'])
		{
			loadHelper ('user_management');
			header("Content-Type: application/json");
			$list_gr = getGroupList();
			foreach ($list_gr as $content)
			{
				$temp['group_name'] = $content['name'];
				$temp['group_value'] = $content['name'];
				$output_list[] = $temp;
			}
			echo '{"groups":';
			echo json_encode($output_list);
			echo '}';
		}
	break;

	case 'getEmptyGroup':
			echo '{"groups":[]}';
	break;

	case 'getGrpListAvailableForUsr':
		loadHelper ('user_management');
		$list = getGroupListAvailableForUser($_GET['usr']);
		foreach ($list as $content)
		{
			$temp['group_name'] = $content['name'];
			$temp['group_value'] = $content['name'];
			$output_list[] = $temp;
		}
		echo '{"groups":';
		echo json_encode($output_list);
		echo '}';

	break;
	case 'getGrpForUsr':
		loadHelper ('user_management');
		echo '{"groups":';
		echo json_encode(getGroupFromOneUser($_GET['usr']));
		echo '}';
	break;


	case 'getAvailableLanguage':
		global $tab_lang;
		foreach ($tab_lang as $lang=>$content)
		{
			$temp['lang_name'] = utf8_encode($content);
			$temp['lang_value'] = $lang;
			$output_list[] = $temp;
		}
		echo '{"lang":';
		echo json_encode($output_list);
		echo '}';
	break;
	
	case 'getRadioUserbar':
		loadHelper ('radio');
		generateUserBar($_GET['id']);
	break;
	
	case 'editUser':
		if ($is_loggedin)
		{
			loadHelper ('user_management');
			if (editUser_simple($user_info[id],$_POST['name'],$_POST['pass'], $_POST['pass-cfrm'], $_POST['email']))
			{
				$return['message'] = $ERROR->getNotFormatedError('PROFILE_EDITED','');
				$return['success'] = true;
				echo json_encode($return);
				
				
				
			}
			else
				echo '{success: false, message:"'.$ERROR->displayErrorWhitoutFormatting().'"}';
				
		}
	break; 
	case 'getFileContent':
		// first security, is the user loggedin?
		loadHelper ('ressource');
		if ($is_loggedin)
		{
			loadHelper ('ressource');
			$directory =& load_class('Directory2'); 
			render_ressource($_GET['file'], $directory->getDirectory_access(), $_GET['dwl'], true);
		}
	break;
	case 'getdisplay_media':
			loadHelper ('picture');
			loadHelper ('utility');
			loadHelper ('administration');
			loadHelper ('comments');
			
			$directory =& load_class('Directory2');		// LOAD DIRECTORY
			if (isset ($_GET['dir'])) 	$dir =  $_GET['dir']; else 	$dir='';	// DEFAULT DIR
			
			$directory->setRoot($dir);

			$SMARTY->assign("trail",$directory->parseDirectory($_GET['ref']));
			$current_dir = $directory->getInfoFromRoot();
			$SMARTY->assign("current_dir",$current_dir);
			
			$elements = $directory->getSpecificItemInDirectory($_GET['file'], 2, 2);
			
			$current_media = $directory->getSpecificNodeInformation();
			
			
			$SMARTY->assign("walk_elements",$elements['list']);
			
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


			echo $SMARTY->fetch('display_media.tpl');

	break;
	case 'addfollower':
		if ($is_loggedin)
		{
			loadHelper ('follower');
			if (addFollower($user_info['id'], $_GET['dir_id'], 'immediate'))
				$array['success'] = true;
			else
				$array['success'] = false;
			echo json_encode($array);
		}
	break;
	case 'removefollower':
		if ($is_loggedin)
		{
			loadHelper ('follower');
			if (removeFollower($user_info['id'], $_GET['dir_id']))
				$array['success'] = true;
			else
				$array['success'] = false;
			echo json_encode($array);
		}
	break;	
	case 'test':
		$index =& load_class('Indexing'); 
		$index->updateOutdatedDirectory($_POST['id']);
	break;
	
	case 'clearIndexing':
		
	break;
	
	case 'indexingRunning':
		loadHelper ('indexing');
		if (isIndexingRunning())
			echo json_encode(array('running'=>true, 'message'=>""));
		else
			echo json_encode(array('running'=>false, 'message'=>lastIndexingDate()));
	break;
	case 'startindexing':
		loadHelper ('indexing');
		if (startIndexing())
			echo json_encode(array('success'=>true, 'message'=>""));   
		else
			echo json_encode(array('success'=>false, 'message'=>""));   
	break;
	case 'stopindexing':
		loadHelper ('indexing');
		if (stopIndexing())
			echo json_encode(array('success'=>true, 'message'=>""));
		else
			echo json_encode(array('success'=>false, 'message'=>"")); 
	break;	
	
	case 'clearcache':
		loadHelper ('indexing');
		if (clearCache())
			echo json_encode(array('success'=>true, 'message'=>""));
		else
			echo json_encode(array('success'=>false, 'message'=>"")); 	
	break;


	case 'getPlugmediaJavascriptConfig':
		$CFG =& load_class('Config');
		echo 'var PM_config = {
		 			url_css: "'.SYS_FOLD.'/'.$CFG->item('css_dir').'",
					url_img: "'.SYS_FOLD.'/'.$CFG->item('image_dir').'",
					current_lang: "'.$i18n->getCurrent_lang().'",
					lang_fillinfield: "'.$i18n->translate('EMPTYVALUE').'",
					lang_errorlogin: "'.$i18n->translate('LOGINPASSERROR').'"					
			  }';	
	break;	
	
	case 'getcomment':
		loadHelper ('comments');
		header("Content-Type: application/json");
		if (canReadComment($user_info))
		{	
			$result = getComments($_GET['file_id']);
			$SMARTY->assign("comments",$result);
			echo $SMARTY->fetch_('comment_element.tpl');
		}
	
	break;

	case 'addcomment':	
		loadHelper ('comments');
		if (canAddComment($user_info))
		{
			if (addComment($_GET['file_id'], $_GET['comment'], $user_info['id'], $_GET['name'], $_GET['email'], $_GET['securite']))
			{	
				$result = getLastCommentFromUser($_GET['file_id'], $user_info['id']);
				$SMARTY->assign("comments",$result);
				echo json_encode(array('success'=>true, 'message'=>$SMARTY->fetch_('comment_element.tpl'))); 
			}
			else
				echo json_encode(array('success'=>false, 'message'=>$ERROR->displayErrorWhitoutFormatting())); 
		}
	break;
	
	case 'suggest_tag':
		loadHelper ('metadata_management');
		$result = getTagSuggest($_GET['tag']);
		echo json_encode($result);
	break;
	
	case 'create_tag':
		loadHelper ('metadata_management');
		if ($is_loggedin && canManageMetadata($user_info))
		{
			
			$string = addArrayTagToFile($_GET['values'], $_GET['file_id']);
			echo json_encode($string);
		}
	
	break;
	case 'remove_tag':
		loadHelper ('metadata_management');
		if ($is_loggedin && canManageMetadata($user_info))
		{
			removeTag($_GET['values'], $_GET['file_id']);
			//$string = addArrayTagToFile($_GET['values'], $_GET['file_id']);
			//echo json_encode($string);
		}
	break;
	case 'editTitle':
		loadHelper ('metadata_management');
		if ($is_loggedin && canManageMetadata($user_info))
		{
			modifyTitleFile($_POST['value'], $_GET['id']);
			echo stripslashes($_POST['value']);
		}
	break;	
	case 'editDescription':
		loadHelper ('metadata_management');
		if ($is_loggedin && canManageMetadata($user_info))
		{
			modifyDescriptionFile($_POST['value'], $_GET['id']);
			echo stripslashes(nl2br($_POST['value']));
		}
	break;		
		

//************************************************************************************//
// 								ADMIN OPERATION
//************************************************************************************//
	
	case 'admin_createUser':
		if ($is_loggedin && $user_info['admin'])
		{
			header("Content-Type: application/json");
			loadHelper ('user_management');
			if (createUser($_POST['login'],$_POST['name'],$_POST['pass'], $_POST['pass_cfrm'], $_POST['email'], $_POST['group_member'],$_POST['lang_value'], true,$_POST['can_read_com'], $_POST['can_post_com'], $_POST['can_manage_mtd'], true, $_POST['send_email']))
			{
				$tab['success'] = true;	
				$tab['message'] = $ERROR->getNotFormatedError('USERADDED',$_POST['login']);
			}else{
				$tab['success'] = false;	
				$tab['message'] = $ERROR->displayErrorWhitoutFormatting();
			}
			echo json_encode($tab);
		}
	break;
	
	case 'admin_createGroup':

		if ($is_loggedin && $user_info['admin'])
		{
			header("Content-Type: application/json");	
			loadHelper ('user_management');
			if (createGroup($_POST['groupname'], $_POST['user_group'], $_POST['selectX']))
			{
				$tab['success'] = true;	
				$tab['message'] = $ERROR->getNotFormatedError('GROUPADDED',$_POST['groupname']);
			}else{
				$tab['success'] = false;	
				$tab['message'] = $ERROR->displayErrorWhitoutFormatting();
			}
			echo json_encode($tab);
				
		}
	
	break;
	
	case 'admin_editUser':
		if ($is_loggedin && $user_info['admin'])
		{
			loadHelper ('user_management');
			if (editUser($_POST['id'],$_POST['name'],$_POST['pass'], $_POST['pass-cfrm'], $_POST['email'], $_POST['group_member'],$_POST['lang_value'], $_POST['can_read_com'], $_POST['can_post_com'], $_POST['can_manage_mtd'], $_POST['admin_access']))
			{
				$tab['success'] = true;	
				$tab['message'] = $ERROR->getNotFormatedError('USEREDITED',$_POST['login']);

			}
			else
			{
				$tab['success'] = false;	
				$tab['message'] = $ERROR->displayErrorWhitoutFormatting();
			}
			echo json_encode($tab);
				
		}
	break;

	
	case 'admin_editGroup':
		if ($is_loggedin && $user_info['admin'])
		{
			
			loadHelper ('user_management');
			if (editGroup($_GET['grp'], $_GET['user_group'], $_GET['selectX']))
			{
				$tab['success'] = true;	
				$tab['message'] = $ERROR->getNotFormatedError('GROUPEDITED',$_GET['groupname_']);

			}
			else
			{
				$tab['success'] = false;	
				$tab['message'] = $ERROR->displayErrorWhitoutFormatting();
			}
			echo json_encode($tab);
				
		}
	break;

	case 'get_message_queue':
		//$val = rand(1, 20);
		//if ($val!=15)
		//	echo json_encode(array('converted'=>true, 'message'=>""));		// currently converting the movie $GET[id]
		//else
			echo json_encode(array('emptyqueue'=>true, 'message'=>""));		// ended
	break;	
	
	
	case 'add_movie_to_queue':
		// add movie to the queue
		$queue =& load_class('Queue');
		$queue->putItem('movie_convert', serialize(array('id_movie'=>$_GET['id'],'flv_convert'=>true, 'mobile_convert'=>false,'extract_thumb'=>true )), $_GET['id'])	;
		
		// And empty the queue
		
		$queue->performQueue('movie_convert',10);
		
	break;	
			


}

?>