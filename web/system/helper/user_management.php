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

	function createUser($login,$name, $pass, $pass2, $email, $groups, $lang, $embedded= true, $can_read_comment= false, $can_add_comment=false, $can_add_comment=false, $can_manage_metadata=false, $stop_on_needed_value=true, $send_email=false)
	{
		global $ERROR, $DB;
		
		if (($login =='' || $pass=='' ||$pass2=='') && $stop_on_needed_value)
		{
			$ERROR->addError('EMPTYVALUE', 'Error');
			return false;
			exit();		
		}
		if ($pass != $pass2)
		{
			$ERROR->addError('PASSDIFFERENT', 'Error');
			return false;
			exit();
		}
		// now verify lang

		$configDB =& load_class('ConfigLoader');	
		$tab_lang = $configDB->getValue('AVAILABLE_LANG');
		
		if (!array_key_exists($lang,$tab_lang))
		{
			$ERROR->addError('LANGNOTEXIST', 'Error');
			return false;
			exit();
		}
		// now verify that all groups are existing
		$error = false;


		if (is_array($groups))
		{
			
			$DB->query("SELECT * FROM groups","");
			$list_groups = $DB->fetcharray();
			foreach ($list_groups as $group_av)
			{
				$mapping_grp_list[$group_av['id']] = $group_av['id'];
				$l_groups[] = $group_av['id'];
			}
			foreach ($groups as $group)
			{
				if (!in_array($group,$l_groups))
				{
					$error = true;
				}
			}
		}

		
		if ($error)
		{
			$ERROR->addError('GROUPNOTEXIST', 'Error');
			return false;
			exit();		
		}
		if ($email!='' && !filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			$ERROR->addError('EMAILNOTVALIDE', 'Error');
			return false;
			exit();			
		}
		/*if (!ereg('^[0-9a-zA-Z]{4,15}$',$login))
		{
			$ERROR->addError('LOGININCORRECT', 'Error');
			return false;
			exit();					
		}*/
		
		
		$DB->query("SELECT * FROM users WHERE login='".$login."'","");
		
		if (count($DB->fetcharray())>0)
		{
			$ERROR->addError('USERALREADYEXIST', 'Error', $login);
			return false;
			exit();					
		}
	
		// generate random salt
		loadHelper ('utility');		
		$token = createToken('', 1, 7);
		
		$can_read_comment = ($can_read_comment)?'1':'0';
		$can_add_comment = ($can_add_comment)?'1':'0';
		$can_manage_metadata = ($can_manage_metadata)?'1':'0';
		$send_email = ($send_email)?'1':'0';
		
		
		$embedded = ($embedded)?'1':'0';
		
		$DB->query("INSERT INTO users (login,name,password,salt,email,lang,can_read_comment,can_add_comment,default_view,creation_date,last_access,embeded,admin_access,can_manage_metadata) VALUES ('$login','$name','".sha1($token.$pass)."','$token','$email','$lang','$can_read_comment','$can_add_comment','thumb',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,'$embedded','0','$can_manage_metadata')","createUser");
		
		$id_user = $DB->getLastId();

		if (is_array($groups))
		{
			foreach ($groups as $group)
			{
				// need to add the user $login in the group $group
				$id_grp = $mapping_grp_list[$group];
				$DB->query("INSERT INTO usr_grp_mapping (id_grp,id_usr) VALUES ('$id_grp','$id_user')","");
			}
		}
		
		if ($send_email)
		{
			// send welcome mail
			$email_sender =& load_class('Email'); // FIRST INSTRUCTION

			$configDB =& load_class('ConfigLoader');
			$pm_url = $configDB->getValue('PLUGMEDIA_URL');
			$pm_admin_email = $configDB->getValue('PLUGMEDIA_ADMIN_EMAIL');
			


			$email_sender->from($pm_admin_email, 'Plugmedia');

			$email_sender->to($email);
			
			$i18n =& load_class('I18n');
			$backup_lang = $i18n->getCurrent_lang();
			$i18n->setLanguage($lang);
			
			
			
			global $SMARTY;
			$SMARTY->assign("login",$login);
			$SMARTY->assign("name",$name);
			$SMARTY->assign("password",$pass);
			$SMARTY->assign("url_plugmedia",$pm_url);
			
			
			$string = $SMARTY->fetch("email_welcome_user.tpl");

			$email_sender->subject('Plugmedia '.$i18n->translate('WELCOME'));
			$email_sender->message($string);
			$email_sender->send();

			$i18n->setLanguage($backup_lang);
			
		}
		
		log_message('debug', "User: ".$login." created");
		return true;
		
	}
	
	function editUser ($id_edited, $name, $pass, $pass2, $email, $groups, $lang, $can_read_comment= false, $can_add_comment=false, $can_manage_metadata=false, $admin_access=false)
	{
		global $ERROR, $DB;
		if ($pass != $pass2)
		{
			$ERROR->addError('PASSDIFFERENT', 'Error');
			return false;
			exit();
		}
		
		$configDB =& load_class('ConfigLoader');	
		$tab_lang = $configDB->getValue('AVAILABLE_LANG');
		
		if (!array_key_exists($lang,$tab_lang))
		{
			$ERROR->addError('LANGNOTEXIST', 'Error');
			return false;
			exit();
		}
		
		// now verify that all groups are existing

		if (is_array($groups))
		{
			
			$DB->query("SELECT * FROM groups","");
			$list_groups = $DB->fetcharray();
			foreach ($list_groups as $group_av)
			{
				$mapping_grp_list[$group_av['id']] = $group_av['id'];
				$l_groups[] = $group_av['id'];
			}
			foreach ($groups as $group)
			{
				if (!in_array($group,$l_groups))
				{
					$error = true;
				}
			}
		}

		/*$DB->query("SELECT * from groups","");
		$error = false;
		$list_groups = $DB->fetcharray();
		$group_list_str = "";
		if ($groups != "")
			$groups = explode (",", $groups); 
		if (is_array($groups))
		{
			foreach ($groups as $group)
			{
				$found = false;
				foreach ($list_groups as $available_groups)
				{
					if ($group == $available_groups['name'])
					{
						// prepare SQL request
						if ($group_list_str == "")
							$group_list_str .= "('".$group."'";
						else
							$group_list_str .= ",'".$group."'";
						$found = true;
					}
				}
				if (!$found)
					$error = true;
			}
			$group_list_str .= ")";
		}*/
		if ($error)
		{
			$ERROR->addError('GROUPNOTEXIST', 'Error');
			return false;
			exit();		
		}
		
		if ($id_edited == 2 && !in_array('Guest',$groups))
		{
			$ERROR->addError('GUESTGROUPUSRGUEST', 'Error');
			return false;
			exit();			
		}
		if ($id_edited == 1 && !in_array('Administrator',$groups))
		{
			$ERROR->addError('ADMINGROUPUSRADMIN', 'Error');
			return false;
			exit();			
		}
		

		if ($email!='' && !filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			$ERROR->addError('EMAILNOTVALIDE', 'Error');
			return false;
			exit();			
		}

		
		$DB->query("DELETE FROM usr_grp_mapping WHERE id_usr='$id_edited'","editUser");
		if (is_array($groups))
		{
			foreach ($groups as $group)
			{
				// need to add the user $login in the group $group
				$id_grp = $mapping_grp_list[$group];
				$DB->query("INSERT INTO usr_grp_mapping (id_grp,id_usr) VALUES ('$id_grp','$id_edited')","");
			}
		}
		
		/*if ($group_list_str != "")
		{
			$DB->query("SELECT * FROM groups WHERE name IN ".$group_list_str ,"editUser");
			$array = $DB->fetcharray();
			foreach ($array as $grp)
			{
				$DB->query("INSERT INTO usr_grp_mapping (id_grp,id_usr) VALUES ('".$grp['id']."','".$id_edited."')","editUser");		
			}
		}*/
		
		if ($pass != "")
		{
			// generate random salt
			loadHelper ('utility');
			$token = createToken('', 1, 7);
			$pass_str = " , password= '".sha1($token.$pass)."', salt='$token' ";
		}
		else
			$pass_str = '';
		if ($lang != "")
			$lang_str = ", lang='$lang'";
		
		($can_read_comment == 'on')?$can_read_comment= '1':$can_read_comment='0';
		($can_add_comment == 'on')?$can_add_comment='1':$can_add_comment='0';
		($can_manage_metadata == 'on')?$can_manage_metadata='1':$can_manage_metadata='0';	
		($admin_access)?$admin_access='1':$admin_access='0';	
		
		
		$DB->query("UPDATE users SET name='$name' $pass_str , email='$email' $lang_str, can_read_comment= '$can_read_comment', can_add_comment='$can_add_comment', can_manage_metadata='$can_manage_metadata', admin_access='$admin_access' WHERE id= '$id_edited'","editUser");	
		
		log_message('debug', "User: id ".$id_edited." edited");
		return true;
	
	}
	
	function editUser_simple ($id_edited, $name, $pass, $pass2, $email)
	{
		global $ERROR, $DB;
		if (!isset($pass))
		{
			$pass = "";
			$pass2 = "";	
		}
		if ($pass != $pass2)
		{
			$ERROR->addError('PASSDIFFERENT', 'Error');
			return false;
			exit();
		}
		if ($email!='' && !filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			$ERROR->addError('EMAILNOTVALIDE', 'Error');
			return false;
			exit();			
		}
		$name = $DB->protectString($name);
		$pass = $DB->protectString($pass);
		$pass2 = $DB->protectString($pass2);
		$email = $DB->protectString($email);
		
		if ($pass != "")
		{
			// generate random salt
			loadHelper ('utility');
			$token = createToken('', 1, 7);
			$pass_str = " , password= '".sha1($token.$pass)."', salt='$token' ";
		}
		else
			$pass_str = '';
		
		$DB->query("UPDATE users SET name='$name' $pass_str , email='$email' WHERE id= '$id_edited'","editUser");	
		
		log_message('debug', "User: id ".$id_edited." edited");		
		return true;
		
	}
	
	function createGroup($groupname, $users, $access_path)
	{
		global $DB, $ERROR;
		if ($groupname == "")
		{
			$ERROR->addError('EMPTYVALUE', 'Error');
			return false;
			exit();			
		}
		if (!ereg('^[0-9a-zA-Z]{4,15}$',$groupname))
		{
			$ERROR->addError('GROUPNAMEINCORRECT', 'Error');
			return false;
			exit();					
		}
		
		// now parsing users
		$error = false;
		$DB->query("SELECT * FROM users","");
		$list_users = $DB->fetcharray();
		foreach ($list_users as $user_av)
		{
			$mapping_usr_list[$user_av['login']] = $user_av['id'];
			$l_users[] = $user_av['login'];
		}

		foreach ($users as $user)
		{
		
			if (!in_array($user,$l_users))
			{
				$error = true;
			}
		}
		
		if ($error)
		{
			$ERROR->addError('USERNOTEXIST', 'Error');
			return false;
			exit();		
		}
		// now parsing access path
		$list_accesspath_formating = "";
		$error_path = false;
		$access_path = split(',',$access_path);
		foreach ($access_path as $path)
		{
			if ($list_accesspath_formating == '')
				$list_accesspath_formating = "'".sqlite_escape_string($path)."'";
			else
				$list_accesspath_formating .= ",'".sqlite_escape_string($path)."'";
		}
		// all is good
		// create the group:
		if ($DB->query("INSERT INTO groups (name) VALUES ('$groupname')",""))
		{
			$id_grp = $DB->getLastId();
			
			
			if (is_array($users))
			{
				foreach ($users as $user)
				{
					// need to add the user $login in the group $group
					$id_usr = $mapping_usr_list[$user];
					
					$DB->query("INSERT INTO usr_grp_mapping (id_grp,id_usr) VALUES ('$id_grp','$id_usr')","");
				}
			}
			if ($list_accesspath_formating != "")
			{
				
				$DB->query("SELECT * FROM directory WHERE id IN (".$list_accesspath_formating.")","editGroup");
				$array = $DB->fetcharray();
				foreach ($array as $dir)
				{
					$DB->query("INSERT INTO group_accesspath (group_id,directory_id) VALUES ('".$id_grp."','".$dir['id']."')","createGroup");		
				}
				
			}
			
			log_message('debug', "Group: ".$groupname." created");
			return true;
		}
		else
		{
			$ERROR->addError('GROUPALREADYEXIST', 'Error', $groupname);
			return false;
		}
	}
	
	function editGroup($group_id, $users, $access_path)
	{
		global $DB, $ERROR;
		
		// now parsing users
		$error = false;
		$DB->query("SELECT * FROM users","");
		$list_users = $DB->fetcharray();
		foreach ($list_users as $user_av)
		{
			$mapping_usr_list[$user_av['login']] = $user_av['id'];
			$l_users[] = $user_av['login'];
		}

		$user_list_str = "";

		foreach ($users as $user)
		{
			
			if (!in_array($user,$l_users))
			{
				$error = true;
			}
			else
			{
				if ($user_list_str == "")
					$user_list_str .= "('".$user."'";
				else
					$user_list_str .= ",'".$user."'";

			}
		}
		$user_list_str .= ")";
		

		
		if ($error)
		{
			$ERROR->addError('USERNOTEXIST', 'Error');
			return false;
			exit();		
		}

		$list_accesspath_formating = "";
		$error_path = false;
		$access_path = split(',',$access_path);
		foreach ($access_path as $path)
		{
			if ($list_accesspath_formating == '')
				$list_accesspath_formating = "'".sqlite_escape_string($path)."'";
			else
				$list_accesspath_formating .= ",'".sqlite_escape_string($path)."'";
			
		}
	
	
		// all is good
		// update the group:

	
		$DB->query("DELETE FROM usr_grp_mapping WHERE id_grp='$group_id'","editGroup");
		
		if ($user_list_str != ")")
		{
			$DB->query("SELECT * FROM users WHERE login IN ".$user_list_str ,"editGroup");
			$array = $DB->fetcharray();
			foreach ($array as $usr)
			{
				$DB->query("INSERT INTO usr_grp_mapping (id_grp,id_usr) VALUES ('".$group_id."','".$usr['id']."')","editGroup");		
			}
			
		}
		
		
		$DB->query("DELETE FROM group_accesspath WHERE group_id ='$group_id'","editGroup");
		
		if ($list_accesspath_formating != "")
		{
			
			$DB->query("SELECT * FROM directory WHERE id IN (".$list_accesspath_formating.")","editGroup");
			$array = $DB->fetcharray();
			foreach ($array as $dir)
			{
				$DB->query("INSERT INTO group_accesspath (group_id,directory_id) VALUES ('".$group_id."','".$dir['id']."')","editGroup");		
			}
			
		}
		return true;
	
	}
	
	
	
	function getUserList()
	{
		global $DB;
		$DB->query("SELECT users.*, g.name as groups, g.access_path FROM users LEFT JOIN usr_grp_mapping on users.id = id_usr LEFT JOIN groups g ON g.id = id_grp ORDER BY users.id","getUserList");
		$array = $DB->fetcharray();
		$i=-1;
		$last_id = '-1';
		foreach ($array as $value)
		{
			if ($last_id == $value['id'])
				$temp[$i]['groups'][] =  $value['groups'];
			else
			{
				$i++;
				$temp[$i] = $value;
				$temp[$i]['groups'] = array($value['groups']);
				$last_id = $value['id'];
			}	
		}
		return $temp;
	}
	
	function getUserInfo($user_login)
	{
		global $tab_lang;
		global $DB;

		$DB->query("SELECT users.*, g.name as groups, g.access_path FROM users LEFT JOIN usr_grp_mapping on users.id = id_usr LEFT JOIN groups g ON g.id = id_grp WHERE users.id= $user_login","getUserInfo");
		$array = $DB->fetcharray();
		if (count($array)>0)
		{
			$i=-1;
			$last_id = '-1';
			foreach ($array as $value)
			{
				if ($last_id == $value['id'])
					$return['groups'] .=  ";".$value['groups'];
				else
				{
					$i++;
					$return = $value;
					$last_id = $value['id'];
				}	
			}
			$return['long_lang'] = utf8_encode($tab_lang[$return['lang']]);
			return $return;
		}
		else
			return false;
	}
	
	function getGroupFromOneUser($user_login)
	{
		if (is_array($inf = getUserInfo($user_login)))
		{
			if ($inf['groups'] != "")
			{
				$group_list = explode (";", $inf['groups']);
				foreach ($group_list as $group)
				{
					$temp['group_name'] = $group;
					$temp['group_value'] = $group;
					$return[] = $temp;
				}
				return $return;
			}
			else
				return "";
		}
		return "";
	}
	
	function getGroupListAvailableForUser($user_login)
	{
		
		$allgrp_available = getGroupList();
		$grp_user = getGroupFromOneUser($user_login);
		foreach ($grp_user as $group)
		{
			foreach ($allgrp_available as $key=>$available_grp)
			{
				if ($group['group_name'] == $available_grp['name'])
					unset ($allgrp_available[$key]);	
			}
		}
		return $allgrp_available;
		
	}
	
	function recursive_array_search($needle,$haystack) {
		foreach($haystack as $key=>$value) {
			$current_key=$key;
			if($needle===$value OR (is_array($value) && recursive_array_search($needle,$value))) {
				return $current_key;
			}
		}
		return false;
	} 

	function getGroupInfo($group_id)
	{
		global $DB;

		$DB->query("SELECT direc.parent||direc.name as access, groups.*, usr.login as users  FROM groups LEFT JOIN usr_grp_mapping on groups.id = id_grp LEFT JOIN users usr ON usr.id = id_usr LEFT JOIN group_accesspath gacc ON gacc.group_id = groups.id LEFT JOIN directory direc ON direc.id = gacc.directory_id WHERE groups.id= $group_id","getGroupInfo");
		$array = $DB->fetcharray();

		if (count($array)>0)
		{
			$i=-1;
			$last_id = '-1';
			$last_user = "";
			
			foreach ($array as $value)
			{
				if ($last_id == $value['id'])
				{
					if ($value['users'] != "")
					{
						if ($last_user == $value['users'])
						{
							if ($value['access'] != "" && !in_array($value['access'], $return['path_access_array']))
							$return['path_access_array'][] = $value['access'];
						}
						else
						{
							$return['users'] .=  ";".$value['users'];
							if ($return['users'] != "" && !in_array($value['users'], $return['usrs']))
								$return['usrs'][] = $value['users'];
							$last_user = $value['users'];
							if ($value['access'] != "" && !in_array($value['access'], $return['path_access_array']))
								$return['path_access_array'][] = $value['access'];
						}
					}
				}
				else
				{
					
					$i++;
					$return = $value;
					if ($return['users'] != "" && !in_array($value['users'], $return['usrs']))
						$return['usrs'][] = $return['users'];
					else
						$return['usrs'] = "";
					if ($value['access'] != "" && !in_array($value['access'], $return['path_access_array']))
						$return['path_access_array'][] = $value['access'];	
					$last_user = $value['users'];
					$last_id = $value['id'];
				}	
			

			
			}


			return $return;
		}
		else
			return false;
	}
	
	function getGroupList()
	{
		global $DB;
		$DB->query("SELECT direc.parent||direc.name as access , groups.*, u.login as users FROM groups LEFT JOIN usr_grp_mapping on groups.id = id_grp LEFT JOIN users u ON u.id = id_usr LEFT JOIN group_accesspath gacc ON gacc.group_id = groups.id LEFT JOIN directory direc ON direc.id = gacc.directory_id ORDER BY groups.id","getGroupList");
		$array = $DB->fetcharray();
		$i=-1;
		$last_id = '-1';
		$last_user = "";
		$array_user = array();
		foreach ($array as $value)
		{
			if ($last_id == $value['id'])	// same group?
			{
				if ($last_user == $value['users'])
					$temp[$i]['access_path'][] = $value['access'];
				else
					$temp[$i]['users'][] =  $value['users'];
			}
			else
			{
				$i++;
				$temp[$i] = $value;
				$temp[$i]['users']= array($value['users']);
				$temp[$i]['access_path']= array($value['access']);
				$last_id = $value['id'];
				$last_user = $value['users'];

			}	
		}
		//print_r ($temp);
		foreach ($temp as $key=>$group)
		{
			$temp[$key]['users'] = array_unique($group['users']);
			$temp[$key]['access_path'] = array_unique($group['access_path']);
		}
		//print_r ($temp);
		return $temp;
	}
	
	function getUserListAvailableForGroup($group_id, $user_list_for_group)
	{
		$array_complet = getUserList();
		if ($user_list_for_group == "")
			$user_list_for_group = array();
		foreach ($array_complet as $usr_)
		{
			$array_diff[] = $usr_['login'];
		}
		$array_short = $user_list_for_group;
		return array_diff($array_diff, $array_short);
	}
	
	function removeGroup($id_group)
	{
		global $DB;
		$DB->query("DELETE FROM groups WHERE id='$id_group' AND name <> 'Administrator' AND name <> 'Guest'","removeGroup");
		$DB->query("DELETE FROM usr_grp_mapping WHERE id_grp = '$id_group'","removeGroup");
		return true;
		
	}
	
	function removeUser($id_usr)
	{
		global $DB;
		$DB->query("DELETE FROM usr_grp_mapping WHERE id_usr = '$id_usr'","removeUser");
		$DB->query("DELETE FROM users WHERE id='$id_usr' AND login <> 'admin' AND login <> 'Guest'","removeUser");
		return true;
	}
	
	function removeUsers($listofusers)
	{
		if (is_array ($listofusers))
		{
			$no_error = true;
			foreach ($listofusers as $users)
			{
				if (!removeUser($users))
					$no_error = false;
			}
		}
		return $no_error;
	}
	
	function removeGroups($listofgroups)
	{
		if (is_array ($listofgroups))
		{
			$no_error = true;
			foreach ($listofgroups as $group)
			{
				if (!removeGroup($group))
					$no_error = false;
			}
		}
		return $no_error;
	}

	
	
	
	function modifyPasswordUser($login, $newpassword, $checkpassword)
	{
		/*if ($login =='' || $newpassword=='' || $checkpassword =='')
		{
			$ERROR->addError('EMPTYVALUE', 'Error');
			return false;
			exit();		
		}
		if ($pass != $pass2)
		{
			$ERROR->addError('PASSDIFFERENT', 'Error');
			return false;
			exit();
		}
		$ini_users =& load_class('ParsingIni');
		$ini_users->ConfigMagik('system/config/users.php', true, true);
		
		if (!$ini_users->sectionExist($login))
		{
			// login not exist
			return false;
			exit();					
		}
		// generate random salt
		loadHelper ('utility');
		$token = createToken('', 1, 7);
		$ini_users->set('salt', $token , $login);
		$ini_users->set('password', sha1($token.$newpassword), $login);
		*/
	}
	
	
	function listAvatar()
	{
		$path=BASEPATH.'common_style/avatar/48';
		$mask='*.png';
		$dir = array(); // cache result in memory
		if ( !isset($dir[$path])) {
			$dir[$path] = scandir($path);
		}
		foreach ($dir[$path] as $i=>$entry) {
			if ($entry!='.' && $entry!='..' && fnmatch($mask, $entry) ) {
				$sdir[] = $entry;
			}
		}
		return ($sdir);
	}
	
	
	function changeAvatar($id_user, $avatar_name)
	{
		global $DB;	
		$avatar_name = $DB->protectString($avatar_name);
		if (is_file(BASEPATH.'common_style/avatar/48/'.$avatar_name))
		{
			$DB->query("UPDATE users SET avatar = '$avatar_name' WHERE id = $id_user","changeAvatar");
			return true;	
		}
		else
			return false;
		
	}
	
	

	
	


?>