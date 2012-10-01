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

	function GetAllDirectory($folder) {

		
		
		if ($folder == 'getroot_node')
		{
			$root = getRootNodeInfo();
			return array('attr'=> array('id'=> $root['rootid'], 'rel'=>"drive"),'data'=>'NAS', 'state'=>'closed');
		}
		else
		{
	 	
			$directory = load_class('Directory2');
			
			$return = $directory->ListDirectoryWithoutAccess($folder, true, false, 0, 0);
			
			$files = array();
			foreach ($return as $items)
			{
				$files[] = array('attr'=> array('id'=> $items['dir_id'], 'rel'=>"folder"),'data'=>array('title'=>$items['name_formated']), 'state'=>'closed');
				
				//$arr['key'] = $items['dir_id'];
//				$arr['value'] = $items['name_formated'];
//				$arr['readable_value'] = $items['name'];
//				$files[] = $arr;
			}
			return $files;
		}
	}

	
	function getRootNodeInfo()
	{
		global $DB;
		$DB->query("select id as rootid, parent||name as full_path from directory where name = ''","getRootNodeInfo");
		$min = $DB->fetchrow();
		return $min;
	}	
	
	// Return allowed directory in a directory node
	// IN: directory node (ID of the directory or start for starting directory)
	// out: array()
	function getAllowedDirectory($node)
	{
		// USED FOR THE API 
		
		global $SESSION,$PLUGIN_MGT;
		$configDB = load_class('ConfigLoader');
				
		loadHelper ('utility');
		$directory = load_class('Directory2');

		
		
		$nodes = array();
		
		if ($node == 'getroot_node')
		{
			return array('attr'=> array('id'=> "root", 'rel'=>"drive"),'data'=>'NAS', 'state'=>'closed');
			
		}elseif ($node == 'root')
		{
			// first loading, get access path
				
			$var = $directory->getDirectory_access();
			
			foreach ($var as $item_dir)
			{
				if ((bool) $configDB->getValue('UTF8_ENCODING'))
					$item_dir['short_name'] = mb_convert_encoding($item_dir['short_name'], "UTF-8");

				$nodes[] = array('attr'=> array('id'=> $item_dir['dir_id']."_".$item_dir['dir_id'], 'rel'=>"folder"),'data'=>array('title'=>$item_dir['short_name'],'attr'=>array('href'=>"list.php?dir=".$item_dir['dir_id']."&ref=".$item_dir['dir_id']."&view=inline", 'class'=>'custom_target')), 'state'=>'closed');
			}
	
			return $nodes;
		}else
		{
			$champs = explode ("_", $node); 
			
			$directory->setRoot($champs[0]);
			
			$var = $directory->listDirectory(true);

			$PLUGIN_MGT->hook("getAllowedDirectory_hook", &$var); 
			 			
			foreach ($var as $item_dir)
			{
				$directory->setRoot($item_dir['dir_id']);
				$second = $directory->listDirectory(true);	
				
				if (count($second) == 0)
					$empty = '';
				else
					$empty = 'closed';
				
				if ((bool) $configDB->getValue('UTF8_ENCODING'))
					$item_dir['short_name'] = mb_convert_encoding($item_dir['short_name'], "UTF-8");

				//$nodes[] = array('text'=>$item_dir['short_name'], 'id'=>$item_dir['dir_id']."_".$champs[1], 'href'=>"list.php?dir=".$item_dir['dir_id']."&ref=".$champs[1], 'cls'=>'folder','iconCls'=>'folder', 'leaf'=>$empty, 'singleClickExpand'=>'true');
				
				$nodes[] = array('attr'=> array('id'=> $item_dir['dir_id']."_".$champs[1], 'rel'=>"folder"),'data'=>array('title'=>$item_dir['short_name'],'attr'=>array('href'=>"list.php?dir=".$item_dir['dir_id']."&ref=".$champs[1]."&view=inline", 'class'=>'custom_target')), 'state'=>$empty);
			}
			return $nodes;			
		}
	}
	
	
	
	function _compareByvalue($a, $b)
	{
		return (strnatcasecmp($a["value"],$b["value"]));
	} 
	

	
	function getInfoFromFile($file, $config)
	{
		global $DB;
		$metadata = load_class('Metadata');
		$extension = getExtension($file);	
	
		if (in_array(strtolower($extension), $config->item('extension_img')))
		{
			$exif_data = $metadata->saveExifData($file);


			return $exif_data;
		}
		else
			return false;
	}
	
	function getExtension($fichier) 
	{
    	$bouts = explode(".", $fichier);
    	$extension = array_pop($bouts);
	    return $extension;
	} 
	
	
	// in : path url eg: /share/Qmultimedia/test/demo/path
	// out: return a list of all parents (/share/Qmultimedia, /share/Qmultimedia/test, /share/Qmultimedia/test/demo)
	function recursiveParentfromPath( $path )
	{
		global $DB;
		
		$array_id = array();
		
		// BUILD QUERY STRING
		$starting = explode ("/", STARTING_FOLDER);
		$starting = end($starting);		
		// now we have the starting point		
		
		$toremove = str_replace(STARTING_FOLDER, "",$path);
		
		$string= "(parent = '".sqlite_escape_string(STARTING_FOLDER)."' AND name = '')";
		$current_dir = STARTING_FOLDER."/";
		$champs_ac = explode ("/", $toremove);
		
		foreach ($champs_ac as $dir_part)
		{
			if ($dir_part != "")
			{
				$string .= " OR (parent = '".sqlite_escape_string($current_dir)."' AND name = '".sqlite_escape_string($dir_part)."')";
				$current_dir .= $dir_part."/";
			}	
		}
		//echo "SELECT id, parent, name FROM directory WHERE ".$string." order by parent";
		$DB->query("SELECT id, parent||name as path FROM directory WHERE ".$string." order by parent","");
		
		
		$array_result = $DB->fetcharray();
		
		foreach ($array_result as $id)
		{
			$array_id[] = $id['id'];
			if ($id['path'] == $path)
				$array_selected[] = $id['id'];
		}
		$array['id'] = $array_id;
		$array['selected'] = $array_selected;	
		
		return $array;		
		
	}
	function recursiveParentFromPathArray( $path_array)
	{
		$global_array = array();
		foreach ($path_array as $path)
		{
			$temp_array = recursiveParentfromPath( $path );
			$global_array =  array_merge_recursive ($global_array , $temp_array);	
		}
		$global_array['id'] = array_unique($global_array['id']);
		$global_array['selected']= array_unique($global_array['selected']);
		return $global_array;
	}
	


?>