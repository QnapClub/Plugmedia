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

class CORE_Directory_monitoring {

	var $DB;
	
	function CORE_Directory_monitoring()
	{
		global $DB, $db_config;	
		$this->DB = $DB;
			
	}
	
	function addChangedDirectories($directory_array, $inserted_directory, $inserted_file, $updated_file)
	{
		
		if (count($directory_array)>0)
		{
			$prepare_statement = 'INSERT INTO queue_news (directory_id, inserted_directory, inserted_file, updated_file, date) VALUES ';
			$insert = false;
			foreach ($directory_array as $dir_n)
			{
				$array_str = array();
				if ($inserted_directory > 0 || 	$inserted_file >0 || $updated_file >0)
				{
					$prepare_statement .= "('".$dir_n."', '".$inserted_directory."', '".$inserted_file."', '".$updated_file."',NOW() ),";
					$insert = true;
				}
				
			}
			
			if ($insert)
			{
				$prepare_statement = substr($prepare_statement,0,-1);
				$this->DB->query($prepare_statement,"addChangedDirectories");
			}
		}

	}
	
	function sendMailFollowers()
	{
		// get all changes directory for all users
		$this->DB->query("SELECT dir.name as dirname, usr.lang, usr.name, usr.login, usr.email, df.user_id, qn.directory_id, SUM(qn.inserted_file) as inserted_file, SUM(qn.inserted_directory) as inserted_directory, SUM(updated_file) as updated_file from directory_followers df LEFT JOIN queue_news qn ON qn.directory_id = df.directory_id LEFT JOIN users usr ON usr.id = df.user_id LEFT JOIN directory dir ON dir.id = df.directory_id WHERE inserted_file is not null and inserted_directory is not null and updated_file is not null GROUP BY qn.directory_id, df.user_id, usr.email,usr.name, usr.login,usr.lang, dir.name","addChangedDirectories");
		$array_member = $this->DB->fetcharray();
		$followers = array();
		foreach ($array_member as $member)
		{
			$followers[$member['user_id']][$member['directory_id']]['inserted_directory'] = $member['inserted_directory'];
			$followers[$member['user_id']][$member['directory_id']]['inserted_file'] = $member['inserted_file'];			 
			$followers[$member['user_id']][$member['directory_id']]['updated_file'] = $member['updated_file'];
			$followers[$member['user_id']][$member['directory_id']]['name'] = $member['dirname'];					
			$followers[$member['user_id']]['info']['lang'] =  $member['lang'];
			$followers[$member['user_id']]['info']['name'] =  $member['name'];
			$followers[$member['user_id']]['info']['login'] =  $member['login'];
			$followers[$member['user_id']]['info']['email'] =  $member['email'];
		}
		
		foreach ($followers as $usr_mail)
		{
			
			$usr_info = $usr_mail['info'];
			unset ($usr_mail['info']);
			
			if ($usr_info['email'] != '')
			{
			
				$email_sender =& load_class('Email'); // FIRST INSTRUCTION
	
				$configDB =& load_class('ConfigLoader');
				$pm_url = $configDB->getValue('PLUGMEDIA_URL');
				$pm_admin_email = $configDB->getValue('PLUGMEDIA_ADMIN_EMAIL');
	
				$email_sender->from($pm_admin_email, 'Plugmedia');
	
				$email_sender->to($usr_info['email']);
				
				$i18n =& load_class('I18n');
				$backup_lang = $i18n->getCurrent_lang();
				$i18n->setLanguage($usr_info['lang']);
				
				
				
				global $SMARTY;
				$SMARTY->assign("login",$usr_info['login']);
				$SMARTY->assign("name",$usr_info['name']);
				$SMARTY->assign("url_plugmedia",$pm_url);
				$SMARTY->assign("directory_array",$usr_mail);				
				
				$string = $SMARTY->fetch("email_follower.tpl");
	
				$email_sender->subject('Plugmedia Activity');//.$i18n->translate('WELCOME'));
				$email_sender->message($string);
				$email_sender->send();
				
				$i18n->setLanguage($backup_lang);
				
			
			
			}
			
			$this->DB->query("DELETE FROM queue_news","");

		}
		
		
		
	}


}



?>