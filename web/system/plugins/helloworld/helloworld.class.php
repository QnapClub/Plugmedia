<?

class Helloworld extends CORE_Plugin {  

	
	
	public function info()
	{
		return array(
			'name' => 'My little plugin',
			'version' => '1.1',
			'url' => 'http://plugmedia.qnapclub.fr', // url to some information on your plugin
			'author' =>	'Christophe',
			'authorurl' => 'http://qnapclub.fr',
			'license' => 'Apache License', // any license
			'description' => 'This is the first official <strong>Plugmedia plugin</strong>, it demonstrate the plugin system and management.<br />If you enable this plugin, you will see all your folder name in Uppercase.' // what it does 
		);
	}
	
	public function installPlugin()
	{
		// DO some stuff to install the plugin (SQL creation, update, file/directory creation
		return true;	
	}
	public function uninstallPlugin()
	{
		// DO some stuff to iuninstall the plugin (SQL to delete, file/directory remove
		return true;	
	}	
	
	
	
	public function onLoad($args){ 
         
         return true;  
     }  

	public function index_directory_access($directory_access)
	{
		return true;
	}


	public function list_directory_list($list_val)
	{
		foreach ($list_val as $key=>$item)	
		{
			$item['short_name_displayable'] = strtoupper($item['short_name_displayable']);
			$list_val[$key] = $item;
		}
		return true;
	}
	
	
	public function getAllowedDirectory_hook($directory_list)
	{
		foreach ($directory_list as $key=>$item)	
		{
			$item['short_name'] = strtoupper($item['short_name']);
			$directory_list[$key] = $item;
		}
		return true;
	}
	
	
	

}  




?>