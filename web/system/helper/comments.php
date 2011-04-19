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

function addComment($file_id, $comment, $user, $nom, $email, $code_securite)
{
	global $ERROR;
	global $DB;
	
	// User can add comment?
	loadHelper ('turing');
	if (!check_turing($code_securite))
	{
		$ERROR->addError('ERRORTURING', 'Error');
		return false;
		exit();
	}
	
	if (!filter_var($email, FILTER_VALIDATE_EMAIL))
	{
		$ERROR->addError('EMAILNOTVALIDE', 'Error');
		return false;
		exit();			
	}

	
	$comment=str_replace("\'","''",utf8_encode($comment));	// strange in SQLITE
	
	if ($DB->query("INSERT INTO comments (user_id,file_id, displayable_name,email,comment,time) VALUES ('".$user."','".$file_id."','".$nom."','".$email."','".$comment."',CURRENT_TIMESTAMP)","addComment"))
		return true;
	else
		return false;
	
}

function getComments($file_id)
{
	
	global $DB;
	$DB->query("SELECT * FROM comments WHERE file_id = '$file_id'","getComments");
	return $DB->fetcharray();
	
}

function getLastCommentFromUser($file_id, $user_id)
{
	global $DB;
	$DB->query("SELECT * FROM comments WHERE file_id = '$file_id' AND user_id='$user_id' ORDER BY time DESC LIMIT 1","getComments");
	return $DB->fetcharray();	
}



function canReadComment($user_info)
{
	if (!isset ($user_info['can_read_comment']))
	{
		return false;
		exit();
	}
	if ((bool)$user_info['can_read_comment'])
		return true;
	else
		return false;
}
function canAddComment($user_info)
{
	if (!isset ($user_info['can_add_comment']))
	{
		return false;
		exit();
	}
	if ((bool)$user_info['can_add_comment'])
		return true;
	else
		return false;
}


?>