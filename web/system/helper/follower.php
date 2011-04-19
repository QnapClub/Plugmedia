<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
*
* @package Plugmedia
* @copyright (c) 2009 Christophe Lemoine
* @license http://creativecommons.org/licenses/by-nc-nd/2.0/be/  Attribution-Noncommercial-No Derivative Works 2.0 Belgium
* QNAP Systems, Inc is authorize to distribute and transmit the work
*
* Minimum Requirement: PHP 5
*/

function addFollower($user_id, $directory_id, $track_type = 'immediate')
{
	global $DB;
	if ($DB->query("INSERT INTO directory_followers (user_id, directory_id, last_send, track_type) VALUES ('".$user_id."', '".$directory_id."', NULL, '".$track_type."')","addFollower"))
		return true;
	else
		return false;
}


function removeFollower($user_id, $directory_id)
{
	global $DB;
	$DB->query("DELETE FROM directory_followers where user_id = '".$user_id."' and directory_id='".$directory_id."'","removeFollower");	
	return true;	
}

function removeFollowers($user_id, $list_directory_id)
{
	if (is_array ($list_directory_id))
	{
		$no_error = true;
		foreach ($list_directory_id as $directory_id)
		{
			if (!removeFollower($user_id, $directory_id))
				$no_error = false;
		}
	}
	return $no_error;
	
}

function followerList($user_id)
{
	global $DB;
	$DB->query("SELECT * from directory_followers LEFT JOIN directory ON directory_id = directory.id WHERE user_id='".$user_id."'","followerList");
	return $DB->fetcharray();
}

function isFollower($user_id, $dir_id)
{
	global $DB;
	$DB->query("SELECT * from directory_followers LEFT JOIN directory ON directory_id = directory.id WHERE user_id='".$user_id."' and directory_id = '".$dir_id."' ","");
	$result = $DB->fetchrow();
	if ($result['directory_id'] != "")
		return true;
	else
		return false;
}


?>