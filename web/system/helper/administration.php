<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
*
* @package Plugmedia
* @copyright (c) 2009 Christophe Lemoine
* @license http://creativecommons.org/licenses/by-nc-nd/2.0/be/  Attribution-Noncommercial-No Derivative Works 2.0 Belgium
* QNAP Systems, Inc is autorize to distribute and transmit the work
*
* Minimum Requirement: PHP 5
*/

function canDownload()
{
	$configDB = load_class('ConfigLoader');
	return (bool) $configDB->getValue('AUTORIZE_DOWNLOAD');
}

function updateConfigurationAutorization($autorization)
{
	if ($autorization == 1)
		$val = 1;
	else
		$val = 0;
	
	$configDB = load_class('ConfigLoader');
	$configDB->setValue('AUTORIZE_DOWNLOAD', $val);
	
}
function updateConfigurationPlugmedia($automatic_check, $pm_url, $pm_admin_mail)
{
	if ($automatic_check == 1)
		$val = 1;
	else
		$val = 0;

	$configDB = load_class('ConfigLoader');
	$configDB->setValue('AUTO_CHECK_UPDATE', $val);
	$configDB->setValue('PLUGMEDIA_URL', $pm_url);
	$configDB->setValue('PLUGMEDIA_ADMIN_EMAIL', $pm_admin_mail);
}

function updateConfigurationThumbnail($small_height, $small_width, $normal_height, $normal_width)
{
	$configDB = load_class('ConfigLoader');
	$configDB->setValue('SMALLTHUMB_HEIGHT', $small_height);
	$configDB->setValue('SMALLTHUMB_WIDTH', $small_width);
	$configDB->setValue('PICTURE_MAX_HEIGHT', $normal_height);
	$configDB->setValue('PICTURE_MAX_WIDTH', $normal_width);
}


function updateConfigurationVisualization($item_per_page, $get_first_pic, $exif_autorotate)
{
	$item_per_page = (int)$item_per_page;
	if ($get_first_pic != 1)
		$get_first_pic = 0;
	if ($exif_autorotate != 1)
		$exif_autorotate = 0;

	$configDB = load_class('ConfigLoader');
	$configDB->setValue('ITEM_PER_PAGE', $item_per_page);
	$configDB->setValue('GET_FIRST_PICTURE', $get_first_pic);
	$configDB->setValue('EXIF_AUTOROTATE', $exif_autorotate);

}

function updateConfigurationProcessing($revoke_outdated, $id3_extract, $id3_cover, $cover_lastfm)
{
	if ($revoke_outdated != 1)
		$revoke_outdated = 0;

	$configDB = load_class('ConfigLoader');
	$configDB->setValue('REVOKE_OUTDATED', $revoke_outdated);
	$configDB->setValue('ID3_EXTRACT', $id3_extract);
	$configDB->setValue('EXTRACT_COVER_FROM_ID3', $id3_cover);
	$configDB->setValue('EXTRACT_COVER_FROM_LASTFM', $cover_lastfm);

}


function deleteConfigurationFilenames($filename_array)
{
	if (is_array ($filename_array))
	{
		$no_error = true;
		foreach ($filename_array as $filename)
		{
			if (!deleteConfigurationFilename($filename))
				$no_error = false;
		}
	}
	return $no_error;
}



function deleteConfigurationFilename($filename)
{
	loadHelper ('utility');
	$filename = hex2bin($filename);
	
	if ($filename == '.' || $filename == '..' || $filename == '.@__comments' || $filename == '.@__desc' ||  $filename == '.@__thumb')
	{
		return false;
		exit();
	}
	
	$configDB = load_class('ConfigLoader');
	$hidding_masks = $configDB->getValue('HIDDING_MASK');
	
	
	$key = array_search($filename, $hidding_masks);
	unset($hidding_masks[$key]);
	
	$configDB->setArrayValue('HIDDING_MASK', $hidding_masks);
	$configDB-> refreshConfig();
	return true;
}

function addConfigurationFilename($filename)
{
	// remove " quote
	$filename = str_replace('\"', "", $filename);
	$filename = str_replace("\'", "", $filename);

	$configDB = load_class('ConfigLoader');
	$hidding_masks = $configDB->getValue('HIDDING_MASK');
	
	if (in_array($filename,$hidding_masks) || $filename=='')
	{
		return false;
		exit();
	}

	$hidding_masks[] = $filename;	

	$configDB->setArrayValue('HIDDING_MASK', $hidding_masks);
	
	return true;
}


function deleteConfigurationExtensions($extension_array)
{
	if (is_array ($extension_array))
	{
		$no_error = true;
		foreach ($extension_array as $extension)
		{
			if (!deleteConfigurationExtension($extension))
				$no_error = false;
		}
	}
	return $no_error;
}



function deleteConfigurationExtension($extension)
{
	loadHelper ('utility');
	$extension = hex2bin($extension);

	$configDB = load_class('ConfigLoader');
	$hidding_extension = $configDB->getValue('HIDDING_EXTENSION');

	
	$key = array_search($extension, $hidding_extension);
	unset($hidding_extension[$key]);

	$configDB->setArrayValue('HIDDING_EXTENSION', $hidding_extension);
	$configDB-> refreshConfig();	
	
	return true;
}

function addConfigurationExtension($extension)
{
	// remove " quote
	$extension = strtolower($extension);
	$extension = str_replace('\"', "", $extension);
	$extension = str_replace("\'", "", $extension);
	$extension = str_replace(".", "", $extension);

	$configDB = load_class('ConfigLoader');
	$hidding_extension = $configDB->getValue('HIDDING_EXTENSION');
	

	if (in_array($extension,$hidding_extension) || $extension=='')
	{
		return false;
		exit();
	}
	$hidding_extension[] = $extension;	
	
	$configDB->setArrayValue('HIDDING_EXTENSION', $hidding_extension);	
	
	return true;
}



function getUserFromSmbConf()
{
	$return = false;
	$smbconf = array ();
	$section_name = "UNKNOWN";
	$value_name = "UNKNOWN";
	$join_line = false;
	$lines = file ('/etc/config/smb.conf');
	foreach ($lines as $line) {
		$trim_line = trim ($line);
		$begin_char = substr($trim_line, 0, 1);
		$end_char = substr($trim_line, -1);
		if (($begin_char == "#") || ($begin_char == ";")) { // comment
			$raw = $trim_line; 
		} elseif (($begin_char == "[") && ($end_char == "]")) { // section
			$raw = $trim_line;
			$section_name = substr ($trim_line, 1, -1);
		} elseif ($trim_line != "") { // values
			$raw = $trim_line;
			$pieces = explode("=", $trim_line, 2); 
			if ($join_line) {
				$smbconf[$section_name][$value_name][] = $trim_line;    
			} elseif (count ($pieces) == 2) {
				$value_name = trim ($pieces[0]);
				$smbconf[$section_name][$value_name][] = trim ($pieces[1]);    
			}
		}
	$join_line = $end_char == "\\";
	}
	$smbconf = explode (",", $smbconf['Qmultimedia']['valid users'][0]);
	$user_list = array();
	foreach ($smbconf as $user_val)
	{
		if (substr($user_val, 0, 1) == '@')
		{
			$user_val = trim($user_val, "@\"");
			$goupr = file ('/etc/config/group');
			foreach ($goupr as $grp) {
				$temp = explode (":", $grp);	
				if ($temp[0] == $user_val)
				{
					$list = explode (",", end($temp));	
					foreach ($list as $usr)
					{
						if (! in_array($usr,$user_list))
						{
							$usr = trim($usr);
							$usr  = str_replace('"', '', $usr);
							if (array_search($usr, $user_list)===false)
								$user_list[] = $usr;
						}
					}
					break;
				}
			}
		}
		else
		{
			$user_val  = str_replace('"', '', $user_val);
			if (array_search($user_val, $user_list)===false)
				$user_list[] = $user_val;
		}
	}
	return $user_list;
}


function syncUsers()
{

	global $DB, $ERROR;
	// first try to read configuration file 
	$array_user_smb = getUserFromSmbConf();
	//$val = print_r($array_user_smb);
	log_message('debug', $val);
	
	// first doing our stuff in plugmedia
	$DB->query("SELECT * FROM users","syncUsers");
	$array_user_pm = $DB->fetcharray();

	foreach ($array_user_pm as $user)
	{
		if (array_search($user['login'], $array_user_smb)!==false){
			$key = array_search($user['login'], $array_user_smb);
			// User in plugmedia is sync with smb conf
			unset($array_user_smb[$key]);
		}
		else
		{
			// IS the user is linked to plugmedia??
			if (array_key_exists('embeded',$user) && $user['embeded'] == '0' && $user['id']!=1 && $user['id']!=2)
			{
				loadHelper ('user_management');
				if (removeUser($user['id']))
					log_message('debug', "Syncing - User ".$user['name']." removed");
			}
		}
		
	}

	foreach ($array_user_smb as $user)
	{
		if ($user != 'root'){
			loadHelper ('user_management');
			if (createUser($user,'', '', '', '', '', 'en', true, false, false, false, false,false))
				log_message('debug', "Syncing - User ".$user." added");
		}
	}
	
	// destroy error
	$ERROR->clearErrors();
	
}


function getComments_admin($start=0, $block_size=20)
{
	global $DB;
	$DB->query("SELECT comments.*, files.id as file_id, files.filename as filename, files.directory_id as dir_id FROM comments, files WHERE comments.file_id = files.id ORDER BY time DESC OFFSET $start LIMIT $block_size","getComments");
	return $DB->fetcharray();
}

function countNewComments()
{
	global $DB;
	$DB->query("SELECT count(*) as total_new FROM comments WHERE \"new\" = 1","countNewComments");	
	return $DB->fetchrow();

}

function readNewComments()
{
	global $DB;
	$DB->query("UPDATE comments SET \"new\" = 0 WHERE \"new\" = 1","readNewComments");	
	return $DB->fetchrow();

}

function deleteComment($id_comment)
{
	global $DB;
	return ($DB->query("DELETE FROM comments WHERE comment_id = '$id_comment'","deleteComment"));	
}

function deleteComments($comment_array)
{
	if (is_array ($comment_array))
	{
		$no_error = true;
		foreach ($comment_array as $comment)
		{
			if (!deleteComment($comment))
				$no_error = false;
		}
	}
	return $no_error;
}

function isUptoDate()
{

	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL,'http://plugmedia.qnapclub.fr/check_pm_version.php');
	curl_setopt($ch, CURLOPT_POST,true);
	curl_setopt($ch, CURLOPT_HEADER      ,0);  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  
	
	$access = curl_exec($ch);
	if(curl_errno($ch))
	{
		log_message('debug', "Erreur Curl : " . curl_error($ch));
		$access = "";
	}
	curl_close($ch);
	
	
	try {
    	libxml_use_internal_errors(TRUE); // N'affiche pas les erreurs
    	$xml = new SimpleXMLElement($access);
		$version_number = (string)$xml->version;
		if (VERSION_PACKAGE == $version_number)
			$result['result'] = true;
		else
			$result['result'] = false;
		$result['link_update'] = (string)$xml->link_update;
		$result['version'] = (string)$xml->version;
		$result['info'] = (string)$xml->info;
		return $result;
		
	} catch (Exception $e) {
		$result['result'] = true;
		return $result;
	}
	
	
}

// out:
// 0: version not up to date
// 1: version up to date
// 2: need manual check
function versionCheck($manual_check=false)
{
	global $SESSION;
	
	if ($SESSION->getData('VERSION_CHECK')!==false && !canAutomaticallyUpdate())
	{
		// version was already checked...
		$result['result'] = $SESSION->getData('VERSION_CHECK');
		$result['link_update'] = $SESSION->getData('VERSION_URL');
		return $result;		
	}
	
	if (canAutomaticallyUpdate() || $manual_check)
	{
		$result = isUptoDate();
		if ($result && $result['result'])
		{
			$SESSION->setData('VERSION_CHECK',1);
			$result['result'] =  1;
		}
		else
		{
			$SESSION->setData('VERSION_CHECK',0);
			$SESSION->setData('VERSION_URL',$result['link_update']);
			$result['result'] =  0;
		}
		
		
	}
	else
	{
		$result['result'] =  2;
	}
	
	return $result;
}

function canAutomaticallyUpdate()
{
	$configDB = load_class('ConfigLoader');
	if ((bool) $configDB->getValue('AUTO_CHECK_UPDATE'))
		return true;
	else
		return false;

}

function dropRepository()
{
	global $DB;	
	//$DB->query("UPDATE files SET file_thumb ='', file_thumb_normal = '', metadata_extracted = 0","dropRepository");
	//$DB->query("UPDATE directory SET thumbnail='', thumbnail_random=''","dropRepository");
	
	$thumb_root = ROOTPATH."/thumb";
	$objects = scandir($thumb_root);
	foreach ($objects as $object) 
	{
		if ($object != "." && $object != "..") {
			if (filetype($thumb_root."/".$object) == "dir")	
			{
				exec("chmod -R 0777 ".$thumb_root."/".$object);
				exec("rm -R ".$thumb_root."/".$object);
				
				//rrmdir($thumb_root."/".$object);
			}
			else
			{
				unlink($thumb_root."/".$object);	
			}
		}
	}
	return true;
}

function rrmdir($dir) {
   if (is_dir($dir)) {
     $objects = scandir($dir);
     foreach ($objects as $object) {
       if ($object != "." && $object != "..") {
         if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
       }
     }
     reset($objects);
     rmdir($dir);
   }
} 

	

?>