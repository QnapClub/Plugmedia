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

class CORE_PluginManagement{

	static private $plugins_list = array();
	

	public function CORE_PluginManagement()
	{
		
	}
	
	 function initialize()
	{
		$this->syncAvailableAndInstalled();
		$list = $this->getListEnabledPlugin();

   		$newResult = array();
		foreach( $list as $plugin )
		{
			$this->registerPlugin($plugin['filename']);
		}
   
	}
	

	// Hook the active plugins at a checkpoint. 
	static function hook( $checkpoint, $args='' )
	{
		// Cycle through all the plugins that are active
   		foreach(CORE_PluginManagement::$plugins_list as $plugin)
		{
    		if (method_exists($plugin,$checkpoint))
			{
				if(!call_user_func( array( $plugin, $checkpoint ), &$args))
     				throw new Exception( "Cannot hook plugin ($plugin) at checkpoint ($checkpoint)" );
			}
   		}
  	}
  
	// Registration adds the plugin to the list of plugins, and also
	// includes it's code into our runtime.
	static function registerPlugin( $plugin )
	{
		require_once( BASEPATH."plugins/$plugin/$plugin.class.php" ); 
		array_push( CORE_PluginManagement::$plugins_list, $plugin );
		log_message('debug', "Register Plugin:".$plugin);
  	}
 
 	// out:
	// true: Plugin correct
	// false: Plugin removed
 	public function checkPlugin( $plugin_id, $plugin_filename, $version )
	{

		if (is_dir(	BASEPATH."plugins/$plugin_filename") && is_file(BASEPATH."plugins/$plugin_filename/$plugin_filename.class.php"))
		{
			require_once( BASEPATH."plugins/$plugin_filename/$plugin_filename.class.php" ); 
			$info = call_user_func( array( $plugin_filename, 'info' ));
			if ($version == $info['version'])
				return true;
			else
			{
				log_message('debug', "Plugin ".$plugin_filename." was deleted due to mismatch version - installed version: ".$version." ,plugin version:".$info['version']);
				$this->uninstallPlugin($plugin_id);
				return false;				
			}
		}
		else
		{
			// No plugin
			log_message('debug', "Plugin was removed (was deleted from plugin directory) :".$plugin_filename);
			$this->uninstallPlugin($plugin_id);
			return false;
		}
	}
	
	public function disablePlugin($plugin_id)
	{
		global $DB;
		$info = $this->getInfoPlugin($plugin_id);

		if (is_dir(	BASEPATH."plugins/".$info['filename']) && is_file(BASEPATH."plugins/".$info['filename']."/".$info['filename'].".class.php"))
		{
			require_once( BASEPATH."plugins/".$info['filename']."/".$info['filename'].".class.php" ); 
			$info = call_user_func( array( $info['filename'] , 'uninstallPlugin' ));		
		}
		
		$DB->query("UPDATE plugins SET enabled = 0 WHERE id = $plugin_id","disablePlugin");
		log_message('debug', "Plugin ".$info['filename']." was correctly disabled");
	}
	
	
	
	public function enablePlugin($plugin_id)
	{
		global $DB;
		$info = $this->getInfoPlugin($plugin_id);
		if (is_dir(	BASEPATH."plugins/".$info['filename']) && is_file(BASEPATH."plugins/".$info['filename']."/".$info['filename'].".class.php"))
		{
			require_once( BASEPATH."plugins/".$info['filename']."/".$info['filename'].".class.php" ); 
			$info = call_user_func( array( $info['filename'] , 'installPlugin' ));		
		}

		$DB->query("UPDATE plugins SET enabled = 1 WHERE id = $plugin_id","enablePlugin");
		log_message('debug', "Plugin ".$info['filename']." was correctly Enabled");
	}	
	
	public function uninstallPlugin($plugin_id)
	{
		global $DB;
		$info = $this->getInfoPlugin($plugin_id);
		if (is_dir(	BASEPATH."plugins/".$info['filename']) && is_file(BASEPATH."plugins/".$info['filename']."/".$info['filename'].".class.php"))
		{
			require_once( BASEPATH."plugins/".$info['filename']."/".$info['filename'].".class.php" ); 
			$info = call_user_func( array( $info['filename'] , 'uninstallPlugin' ));		
		}
		$DB->query("DELETE FROM plugins WHERE id = $plugin_id","uninstallPlugin");
		
		log_message('debug', "Plugin ".$info['filename']." was correctly removed");
		
	}
	
	
	private function getInfoPlugin($id_plugin)
	{
		if (is_int($id_plugin))
		{
			global $DB;
			$DB->query("SELECT * FROM plugins WHERE id = $id_plugin","disablePlugin");
			$info = $DB->fetchrow();
			return $info;
		}
	}
	
	
	
	public function getListEnabledPlugin()
	{
		global $DB;
		$DB->query("SELECT * FROM plugins WHERE enabled = 1","getListEnabledPlugin");
		return $DB->fetcharray();	
	}
	
	public function getListDisabledPlugin()
	{
		global $DB;
		$DB->query("SELECT * FROM plugins WHERE enabled = 0","getListDisabledPlugin");
		return $DB->fetcharray();
	}
	
	// return all plugins available in the Plugin directory
	private function getAllPluginAvailable()
	{
		$list = array();   
		if ( ($directoryHandle = opendir( BASEPATH.'plugins/' )) == true ) 
   		{
		   while (($file = readdir( $directoryHandle )) !== false) 
		   {
				// Make sure we're not dealing with a file or a link to the parent directory
			   	if( is_dir( BASEPATH.'plugins/' . $file ) && ($file == '.' || $file == '..') !== true )
				array_push( $list, $file );
		   }
		}
		return $list;		
	}
	
	private function syncAvailableAndInstalled()
	{
		// Sync plugin that are available in the plugin directory with installed plugin (based on version number)	
		global $DB;
		$list = $this->getAllPluginAvailable();
		$DB->query("SELECT * FROM plugins","syncAvailableAndInstalled");
		$list_installed = $DB->fetcharray();

		
		foreach ($list_installed as $plugin_i)
		{
			if ($this->checkPlugin( $plugin_i['id'], $plugin_i['filename'], $plugin_i['version'] ))
			{
				unset ($list[array_search($plugin_i['filename'], $list)]);
			}
		}
		

		
		foreach ($list as $plugin_ondisk)
		{
			require_once( BASEPATH."plugins/$plugin_ondisk/$plugin_ondisk.class.php" ); 
			$info = call_user_func( array( $plugin_ondisk, 'info' ));
	
			$DB->query("INSERT INTO plugins (filename, name, version, url, author, authorurl, license, description) VALUES ('".$plugin_ondisk."', '".$info['name']."', '".$info['version']."', '".$info['url']."', '".$info['author']."', '".$info['authorurl']."', '".$info['license']."', '".$info['description']."')","syncAvailableAndInstalled");
			log_message('debug', "Discover new plugin, now added to the list : ".$plugin_ondisk." version ".$info['version']);
	
		}
		
	}


}



?>