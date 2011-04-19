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

class CORE_QNAP_logs {

	var $event_log_location = '/mnt/HDA_ROOT/.logs/event.log';
	var $login_log_location = '/mnt/HDA_ROOT/.logs/conn.log';
	var $can_write_event = false;
	var $can_write_login = false;
	var $DB_handler;


	function CORE_QNAP_logs()
	{
		if (is_writable($this->event_log_location) || @chmod($this->event_log_location, 0777))
			$this->can_write_event = true;
		else
			$this->can_write_event = false;
		if (is_writable($this->login_log_location) || @chmod($this->login_log_location, 0777))
			$this->can_write_login = true;
		else
			$this->can_write_login = false;
		
		if (!$this->can_write_login || !$this->can_write_event)
		{
			
			// FORCE THE CHMOD
			
			$ch = curl_init();
			curl_setopt($ch,CURLOPT_URL, HTTPD_URL.'chmod_db.cgi');
			curl_setopt($ch, CURLOPT_POST,true);
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
			$this->can_write_event = true;
			$this->can_write_login = true;
		}
		
		
		$this->DB_handler = load_class('Db');

	}
	
	function writeEventLog($event_type,$event_user, $event_ip, $event_comp , $event_desc)
	{
		if ($this->can_write_event)
		{
			$this->DB_handler->overWriteConfigDatabase($this->event_log_location);
			$this->DB_handler->connexionbd();
			$this->DB_handler->query("INSERT INTO NASLOG_EVENT (event_type,event_date,event_time,event_user,event_ip,event_comp,event_desc) VALUES ('".$event_type."',CURRENT_DATE,CURRENT_TIME,'".$event_user."','".$event_ip."','".$event_comp."','".$event_desc."')","writeEventLog");
				
		}
		else
			log_message('debug', "Impossible to write Event log, Event log database is not writable");	
		
	}
	
	function writeLoginLog($conn_type, $conn_user, $conn_ip, $conn_comp, $conn_res, $conn_serv, $conn_action)
	{
		//Conn_type -- 	O: information, 1: Warning, 2: Error
		//Conn_serv --	1: SAMBA, 2: FTP, 3: HTTP, 4: NFS, 5: AFP, 6: TELNET, 7: SSH, 8: ISCSI
		// Conn_action --	1: Delete, 2: Read, 3: Write, 4: Open, 5: MakeDir, 6: Mount OK, 7: Mount Fail, 8: Rename, 9: Login Fail, 10: Login OK, 11: Logout, 12: Unmount, 13: Copy, 14: Move, 15: Add
		if ($this->can_write_login)
		{		
			$this->DB_handler->overWriteConfigDatabase($this->login_log_location);
			$this->DB_handler->connexionbd();
			$this->DB_handler->query("INSERT INTO NASLOG_CONN VALUES(NULL,".$conn_type.",CURRENT_DATE,CURRENT_TIME,'".$conn_user."','".$conn_ip."','".$conn_comp."','".$conn_res."',".$conn_serv.",".$conn_action.");","writeLoginLog");		
			
		}
		else
			log_message('debug', "Impossible to write Login log, Login log database is not writable");	
		
		
	} 


}



?>