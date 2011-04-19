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

class CORE_ConfigLoader {

	var $db_config;
	var $database;


	public function CORE_ConfigLoader()
	{
		// GET CONFIG SYST FROM DATABASE
		global $DB;
		$this->database = $DB;
		$this->database->query("SELECT * FROM sys_conf_settings WHERE enable = 1","CORE_ConfigLoader");
		$db_config = $this->database->fetcharray();
		foreach ($db_config as $value)
		{
			if ($value['conf_value'] == "")
				$value_conf = $value['conf_default'];
			else
				$value_conf = $value['conf_value'];
			
			if ($value_conf != $value['conf_default'] && $value['conf_value']!='')
				$db_config[$value['conf_key']."_reset"] = true; 
			else
				$db_config[$value['conf_key']."_reset"] = false; 
				
			$db_config[$value['conf_key']] = $value_conf;	
		}

		// extract array
		
		$db_config['HIDDING_MASK'] = $this->unserialiseArray($db_config['HIDDING_MASK']);
		$db_config['EXTENSION_IMG'] = $this->unserialiseArray($db_config['EXTENSION_IMG'] );
		$db_config['HIDDING_EXTENSION'] = $this->unserialiseArray($db_config['HIDDING_EXTENSION']);			
		$db_config['EXTENSION_MOV'] = $this->unserialiseArray($db_config['EXTENSION_MOV']);			
		$db_config['EXTENSION_MOV_DISPLAYABLE'] = $this->unserialiseArray($db_config['EXTENSION_MOV_DISPLAYABLE']);			
		$db_config['EXTENSION_SONG'] = $this->unserialiseArray($db_config['EXTENSION_SONG']);			
		$db_config['EXTENSION_EXIF'] = $this->unserialiseArray($db_config['EXTENSION_EXIF']);			
		$db_config['EXTENSION_RAW'] = $this->unserialiseArray($db_config['EXTENSION_RAW']);			
		$db_config['AVAILABLE_LANG'] = $this->unserialiseArray($db_config['AVAILABLE_LANG']);
				
		
		$this->db_config = $db_config;
	}
	
	public function refreshConfig()
	{
		$this->CORE_ConfigLoader();
	}
	
	private function unserialiseArray($conf_value)
	{
		return unserialize(base64_decode($conf_value)); 
	}
	
	public function getDbConfig()
	{
		return $this->db_config;
	}
	
	public function setValue($conf_key, $conf_value)
	{
		$this->database->query("UPDATE sys_conf_settings SET conf_value='".$this->database->protectString($conf_value)."' WHERE conf_key='".$this->database->protectString($conf_key)."'","resetValue");
	}

	public function setArrayValue($conf_key, $array)
	{
		$string = base64_encode(serialize($array));
		$this->setValue($conf_key, $string);
	}
	
	public function resetValue($conf_key)
	{
		$this->setValue($conf_key, '');
	}
	
	public function getValue($conf_key)
	{
		if (array_key_exists($conf_key, $this->db_config))
			return $this->db_config[$conf_key];
		else
		{
			log_message('error', "Unable to retrieve the conf key : ".$conf_key);	
			return false;
		}
	}
	
	public function canResetValue($conf_key)
	{
		if (array_key_exists($conf_key."_reset", $this->db_config))
			return $this->db_config[$conf_key."_reset"];
	
	}


}



?>