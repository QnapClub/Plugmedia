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

class CORE_Session {

	var $current_session_id;
	var $user = 'Guest';
	var $user_informations;
	var $group;					// array of groups with params array(array(groupename;groupeinfo;groupeaccesspath);array(groupename;groupeinfo;groupeaccesspath))
	var $loggedin = false;
	var $access_path = false;			// array of path array(path1;path2)
	var $embedded_account = true;
	
	function CORE_Session()
	{
		session_start();
		$this->current_session_id = session_id();
		$this->user = 'Guest';
		
	}
	
	function login($user, $pass, $path)
	{
		global $DB;
		$event_qnap = load_class('QNAP_logs');
		//remove extra caracters
		$pass = stripslashes($pass);
		if ($this->verifyUserPassword($user, $pass))
		{
			$this->setData('loggedin', true);
			$this->user = $user;
			$this->setData('user', $user);

			// UPDATING SALT AND PASSWORD AND EMBEDDED ACCOUNT
			loadHelper ('user_management');
			createUser($user,'', '', '', '', '', 'en', false, false, false, false, false);
			
			loadHelper ('utility');
			$token = createToken('', 1, 7);

			($this->embedded_account) ? $embedded = "1" : $embedded = "0";
			
			$ip_user = $this->whatIsip();
			
			$DB->query("UPDATE users SET salt='$token', password='".sha1($token.$pass)."', embeded='$embedded', last_ip='$ip_user'  WHERE login='".$this->user."'","login");

			$this->setUserInformations();

			$this->setData('psw',sha1 ($this->user_informations['salt'].$pass));
			log_message('debug', "User loggedin: ".$this->user);
			//header("Location:".$path);
			
			$event_qnap->writeLoginLog(0, $this->user, $this->whatIsip(false), "---", "Plugmedia", 3, 10);
			
			return true;
		}
		else
		{
			log_message('debug', "Trying to log user: $user with password $pass");
			$event_qnap->writeLoginLog(1, $user, $this->whatIsip(false), "---", "Plugmedia", 3, 9);
			return false;
		}
		
	}
	
	function verifyUserPassword($user, $pass, $passencoded = false)
	{
		global $DB;

		if (!$this->checkPasswordCgiAuth($user, $pass))
		{
			// maybe the user is only defined under Plugmedia, lets check
			$DB->query("SELECT * FROM users WHERE login='$user'","");
			if ($infos = $DB->fetchrow())
			{
				if ($passencoded)
					$verifypass = $pass;
				else
					$verifypass = sha1($infos['salt'].$pass); 
				
				if ($verifypass == $infos['password'])
				{	
					$this->embedded_account = true;
					return true;
				}
				else
					return false;
			}
			else
				return false;
		}
		else
			return true;
	}
	
	
	function checkPasswordCgiAuth($user, $pass)
	{
		if (CGI_AUTH)
		{
			// FIRST try: loggin using linux account using /home/httpd/cgi-bin/cgi_auth.cgi
			$QNAP_HTTPD_PORT = exec("/sbin/getcfg SYSTEM \"Web Access Port\" -f /etc/config/uLinux.conf");    // Default is 8080 but ...
			$ch = curl_init();
			curl_setopt($ch,CURLOPT_URL,HTTPD_URL.'cgi_auth.cgi');
			curl_setopt($ch, CURLOPT_POST,true);
			$data = http_build_query(array('user' => $user, 'passwd' => $pass, 'submit' => 'submit'));
			curl_setopt($ch, CURLOPT_POSTFIELDS    , $data);
			curl_setopt($ch, CURLOPT_HEADER      ,0);  
			curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  
			if (FORCE_SSL)
			{
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER  ,FALSE );
				//curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS );
			} 
			$access = curl_exec($ch);

			if(curl_errno($ch))
			{
				log_message('debug', "Erreur Curl : " . curl_error($ch));
			}
			
			curl_close($ch);
		
			if ($access == "0")
			{
				// GOOD the login and pass are rights....
				$this->embedded_account = false;
				return true;			
			}
			else
				return false;
		
		}
		else
			return false;
	}
	
	function isLoggedin()
	{
		
		$return_val = false;
		if ($this->getData('loggedin')== true)
		{
			// verify user name AND password
			if ($this->verifyUserPassword($this->getData('user'), $this->getData('psw'), true))
			{
				$this->user = $this->getData('user');
				$this->loggedin = true;
				$return_val = true;
			}
			else
				$return_val = false;
		}
		else
			$return_val = false;
		
		log_message('debug', "Loading user: ".$this->user);
		
		$this->setUserInformations();

		return $return_val;
	}
	
	
	function logout()
	{
		$_SESSION = array(); 
		session_destroy(); 
		return true;
	}
	
	function detectionLanguage()
	{
		global $DB;
		if ($this->user == 'Guest')
		{
			// trying to find language info in Cookies
			if (isset ($_COOKIE["lang"]))
			{
				return 	$_COOKIE["lang"];
			}
			else
			{
				// COOKIES NON DEFINI
				
				$configDB = load_class('ConfigLoader');	
				return $configDB->getValue('DEFAULT_LANG');
			}
		}
		else
		{
			$DB->query("SELECT * FROM users WHERE login='".$this->user."'","detectionLanguage");
			$usr = $DB->fetchrow();
			return $usr['lang'];
		}
	}
	

	function setLanguage($lang)
	{
		log_message('debug', "Setting language ".$lang." for user ".$this->user);	
		$this->rememberData('lang', $lang, $this->user);
	}
	
	function setDefaultView($view)
	{
		log_message('debug', "Setting default view ".$view." for user ".$this->user);	
		switch ($view)
		{
			case 'list': $view = 'list'; break;
			case 'thumb': $view = 'thumb'; break;
			case 'thumb_list': $view = 'thumb_list'; break;
			default: $view = 'thumb'; break;
		}
		$this->rememberData('default_view', $view);
	}
	
	function rememberData($key, $value)
	{
		if ($this->user == "Guest")
		{
			// impossible to set value in ini file
			// define a cookies
			setcookie($key, $value, (time()+60*60*24*30*12));
		} else {
			// set lang in the ini file, overwriting language from group
			global $DB;
			if ($DB->query("UPDATE users SET $key='$value' WHERE login='".$this->user."'","rememberData"))
				return true;
			else
				return false;
		}
	
	}
	
	function getSettingUser($key)
	{
		if ($this->user == 'Guest')
		{
			// trying to find $key info in Cookies
			if (isset ($_COOKIE[$key]))
			{
				return 	$_COOKIE[$key];
			}
			else
			{
				// COOKIES NON DEFINI
				return false;
			}
		}
		else
		{
			// reading from the config file of the user
			global $DB;
			$DB->query("SELECT * FROM users WHERE login='".$this->user."'","getSettingUser");
			$arr = $DB->fetchrow();
			return $arr[$key];
		}
	}	

	
	function getAccess_path()
	{
		return $this->access_path;
	}

	function setUserInformations()
	{
		
		// FIRST LOADING USER INFORMATIONS
		global $DB, $SORTING_ORDER;
		
		
		$DB->query("SELECT * FROM users WHERE login = '".$this->user."'","setUserInformations");
	
		$this->user_informations = $DB->fetchrow();
		$this->user_informations['user']  = $this->user;
		$this->SetIsAdmin();
			
		
		// ordering and sorting for accesspath
		$extra_ordering = $SORTING_ORDER->extraOrdering (false);
	
		$DB->query("SELECT  grp.name as group_name, gap.*, dir.* FROM usr_grp_mapping ugm, groups grp, group_accesspath gap, directory dir WHERE dir.id = gap.directory_id AND  grp.id= ugm.id_grp AND gap.group_id=grp.id AND ugm.id_usr = '".$this->user_informations['id']."' ".$extra_ordering,"setUserInformations");
		
		$group_list = $DB->fetcharray();

		$this->user_informations['groups']  = $group_list;
			
		if (isset($this->user_informations['groups']))
		{
			foreach ($this->user_informations['groups'] as $value)
			{
				log_message('debug', "User accesspath from group (".$value['group_name']."): ".$value['parent'].$value['name']);
				if (is_array($this->access_path))
				{
					if (!@in_array($value['directory_id'], $this->access_path))
						$this->access_path[] = $value;
				}
				else
					$this->access_path[] = $value;
							
			}
		}
		
	}
	
	function SetIsAdmin()
	{
		if ($this->user_informations['admin_access'])
			$this->user_informations['admin'] = true;
		else
			$this->user_informations['admin'] = false;
	}
	
	function getUser()
	{
		return $this->user;
	}
	
	function setData($key,$value)
	{
		$_SESSION[$key]=$value;
	}
	function getData($key)
	{
		if (array_key_exists($key,$_SESSION))
			return $_SESSION[$key];
		else
			return false;
	}
	function removeData($key)
	{
		unset($_SESSION[$key]);
	}
	
	function getUser_informations()
	{
		return $this->user_informations;
	}
	
	
	function whatIsip($encoded=true) {
		loadHelper ('utility');
		if($_SERVER) {
		   if(array_key_exists('HTTP_X_FORWARDED_FOR',$_SERVER))
			 $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		   elseif(array_key_exists('HTTP_CLIENT_IP',$_SERVER))
			 $ip = $_SERVER['HTTP_CLIENT_IP'];
		   else
			 $ip = $_SERVER['REMOTE_ADDR'];
		}
		else 
		{
		   if(getenv('HTTP_X_FORWARDED_FOR'))
			 $ip = getenv('HTTP_X_FORWARDED_FOR');
		   elseif(getenv('HTTP_CLIENT_IP'))
			 $ip = getenv('HTTP_CLIENT_IP');
		   else
			 $ip = getenv('REMOTE_ADDR');
		}
		if ($encoded)
			return encode_ip($ip);
		else
			return $ip;
	}


}


?>