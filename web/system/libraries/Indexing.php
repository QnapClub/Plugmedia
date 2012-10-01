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

class CORE_Indexing {

	private $inserted_directory = 0;
	private $skipped_directory = 0;
	private $inserted_file = 0;
	private $updated_file = 0;
	private $skipped_file = 0;
	private $usage_mb = 0;

	private $root_path;
	private $root_id;
	
	private $extract_metadata;

	private $metadata;
	private $config;
	private $DB;
	private $config_array;	

	public function CORE_Indexing()
	{
		global $DB, $db_config;	
		$this->DB = $DB;
		$this->metadata = load_class('Metadata');
		$this->config = load_class('ConfigLoader');
		
		$this->config_array = $db_config;
		$this->setRoot();
		
	}
	
	// FIRST call of the class (define the root node)
	public function setRoot()
	{
		
		$this->root_path = STARTING_FOLDER;
		// get root path information
		
		$this->root_path = $this->DB->protectString($this->root_path);
		$this->DB->query("SELECT * FROM directory WHERE parent = '".$this->root_path."'","setRoot");
		$root_info = $this->DB->fetchrow();
		if ($root_info==false)
		{
			if (!is_dir($this->root_path))
			{
				echo 'error with root path';
				exit();
			}
			// need to index root path...
			$this->root_id = $this->insertDirectory($this->root_path, '');
			// assign to administrator...
			$this->DB->query("INSERT into group_accesspath (group_id, directory_id) VALUES('1','".$this->root_id."');","setRoot");
		
		}else{
			$this->root_id = $root_info['id']; 
		}
		
	}
	
	
	public function scanDirectories($base='', $last_value='', $recursif=true, $echo_process = false, $extract_metadata = false, $parents_id)
	{
		$local_insert_directory = 0;
		$local_insert_file = 0;
		$local_update_file = 0;
		
		$this->extract_metadata = $extract_metadata;
		
		if ($echo_process)
			global $i18n;
		
		log_message('debug', "starting scan function with directory".$base.$last_value);

		
		$backup_base =$base;
		$backup_last_value = $last_value;
		 
		$thumb_find = false;

		$base_protected = $this->DB->protectString($base);
		$last_value_protected = $this->DB->protectString($last_value);
	  	
		$this->DB->query("SELECT id FROM directory WHERE parent='$base_protected' AND name='$last_value_protected'","scanDirectories");
		$id_dir = $this->DB->fetchrow();
		
		if (!isset($id_dir['id']))
		{
			$local_insert_directory++;
			$id_parent = $this->insertDirectory($base, $last_value);
			$file_array = array();
			
		}
		else
		{
			$this->skipped_directory ++;
			
			$this->DB->query("SELECT * FROM files WHERE directory_id='".$id_dir['id']."'","scanDirectories"); 
			$id_parent = $id_dir['id'];
			$files_in_dir = $this->DB->fetcharray();
			$total_affected = count($files_in_dir);
			if ($total_affected>0)
			{
				foreach ($files_in_dir as $file)
					$file_array[$this->DB->protectString($file['filename'])] = $file;
			}
			else
				$file_array = array();
			
		}
		
		
	
		// find all directories in the directory $base.$last_value
		$this->DB->query("SELECT * FROM directory WHERE parent='".$this->DB->protectString($base).$this->DB->protectString($last_value)."/'","scanDirectories"); 
		
		$dir_in_dir = $this->DB->fetcharray();
		$total_affected = count($dir_in_dir);
		if ($total_affected>0)
		{
			foreach ($dir_in_dir as $dir)
				$dirarray[$this->DB->protectString($dir['name'])] = $dir;
		}
		else
			$dirarray = array();
		
	  	
	
		
		
		$base = $base.$last_value.'/';
		$array = array_diff(scandir($base), array('.', '..', '.@__thumb','.@__comments')); 
		
		$count = count($array);
		$i=0;
		
		
		
		foreach($array as $key=>$value)
		{
			$i++;
			if ($echo_process)
			{
				echo "<div id='waiting_message'><div id='content_a'>".$i18n->translate('PM_OUTDATED', '')."<br /><br />".$i18n->translate('PERFORMING', $i, $count, $value)."</div></div>";
            	ob_flush();
            	flush();
			}
 
 			if (isset($parents_id) && is_array($parents_id) && !in_array($id_parent,$parents_id))
				$parents_id[] = $id_parent;
			
        			
			if (is_dir($base.$value))
			{
				$value_protected = $this->DB->protectString( $this->encodetoUTF8($value));
				
				unset ($dirarray[$value_protected]);

				if ($recursif)
				{
					$data = $this->scanDirectories($base,$value, true, $echo_process, $this->extract_metadata, $parents_id); 
				}
				else
				{
					$local_insert_directory++;
					$this->insertDirectory($base, $value);
				}
					
			}
			else
			{

				$value_protected = $this->DB->protectString( $this->encodetoUTF8($value));
				if (!$last_update_time_file = @filemtime($base.$value))
					$last_update_time_file = 0;
				if (!$file_size = filesize($base.$value))
					$file_size = 0;
		  		$file_extension = $this->getExtension($value); 
				
				if (!array_key_exists($value_protected, $file_array))
				{
					
					$original_date = $last_update_time_file;
					$exif_info = "";
					
					loadHelper ('filesys');
					$footprint = get_footprint($base.$value);
					$local_insert_file++;
					$this->insertFileInDatabase($id_parent, $value_protected, $last_update_time_file,$original_date, $file_size, strtolower($file_extension), '', $footprint, $base);
														
		  			$this->inserted_file ++;
					$this->usage_mb += $file_size;
		  		}
		  		else
		  		{
					if ($last_update_time_file > $file_array[$value_protected]['timestamp_modification'])
					{
						// Need update
						loadHelper ('filesys');
						$footprint = get_footprint($base.$file_array[$value_protected]['filename']);
						$local_update_file++;
						log_message('debug', "Update of a  file in database for id : ".$file_array[$value_protected]['id']);
						$this->updateFileInDatabase($file_size, $last_update_time_file, $file_array[$value_protected]['id'], $footprint);
					
					}else
						$this->skipped_file ++;
		  		}

		  
			}
			unset($file_array[$value_protected]);
			

		}
	
		
		if (isset($parents_id))
		{
			$monitoring = load_class('Directory_monitoring');
			// add all directory with changes in the table queue_news
			$monitoring->addChangedDirectories($parents_id, $local_insert_directory, $local_insert_file, $local_update_file);
		}
		
		
		// CLEAN THE DB with obsolete files
		$this->deleteFileInDirectory($file_array);
		$this->deleteDirectoryInDirectory($dirarray);
		
		
       	if ($echo_process)
	    	ob_end_flush();
		return true;		
	}
	
	private function deleteFileInDirectory($file_array)
	{
		foreach ($file_array as $todelete)
			$this->DB->query("DELETE FROM files WHERE id = ".$todelete['id'],"");
		
	}
	
	private function deleteDirectoryInDirectory ($dir_array)
	{
		foreach ($dir_array as $todelete)
			$this->DB->query("DELETE FROM directory WHERE id = ".$todelete['id'],"deleteDirectoryInDirectory");
	}
	
	


	private function updateFileInDatabase ($filesize, $timestamp_modification, $id_modification, $footprint)
	{
		$this->updated_file ++;
		$this->DB->query("UPDATE files SET filesize = $filesize, timestamp_modification=$timestamp_modification, file_hash='$footprint', file_thumb_normal='', file_thumb='' WHERE id = $id_modification","updateFileInDatabase");
		$this->DB->query("DELETE FROM metadata_exif WHERE files_id = $id_modification","updateFileInDatabase");

	}

	private function insertFileInDatabase($id_parent, $filename, $timestam_modif, $original_date, $filesize, $extension, $thumb_path, $footprint, $base)
	{
		log_message('debug', "Insert file with ".$id_parent.$filename);
		
		$thumb_path = $this->DB->protectString( $this->encodetoUTF8($thumb_path));
		$extension = $this->DB->protectString( $this->encodetoUTF8($extension));
		
		
		$string = $this->natConvert( $this->encodetoUTF8($filename));
		($this->extract_metadata)? $metadata_extr = 1:$metadata_extr = 0;
		
		if ($this->DB->query("INSERT INTO files (directory_id,filename, detail_file,timestamp_modification,original_date,filesize,extension,file_thumb,file_hash, formated_name, metadata_extracted) VALUES ('$id_parent','$filename','','$timestam_modif','$original_date','$filesize','$extension','$thumb_path','$footprint','$string',$metadata_extr)","insertFileInDatabase"))
		{
			$id_inserted = $this->DB->getLastId();
			
			// Extract metadata if needed
			
			if ($this->extract_metadata)
			{
				$meta = load_class('Metadata');
				$meta->extractMetadata($id_inserted, $filename, $base, $footprint, $extension);
			}
		}
	

		
	}


	
	private function insertDirectory($parent, $name)
	{
		$this->inserted_directory ++ ;
		$encoded_parent = $this->DB->protectString($parent);
		$encoded_name = $this->DB->protectString( $this->encodetoUTF8($name));
		
		$natural_sorting_name = $this->DB->protectString( $this->encodetoUTF8($this->natConvert($name)));
		
		$original_date = @filemtime($parent.$name);
		log_message('debug', "Insert directory with ".$parent.$name);
		$this->DB->query("INSERT INTO directory (parent,name,thumbnail,thumbnail_random, original_date, formated_name) VALUES ('$encoded_parent','$encoded_name',NULL,NULL,$original_date,'$natural_sorting_name')","indexDirectory");
		
		$id_parent = $this->DB->getLastId();
		$this->inserted_directory ++;
		return $id_parent;
	}
	


	function updateOutdatedDirectory($directory_id)
	{
		$this->DB->query("SELECT * FROM directory WHERE id='$directory_id'","updateOutdatedDirectory");
		$value = $this->DB->fetchrow();
		if (is_array($value))
		{
			$this->scanDirectories($value['parent'],$value['name'], false,true, false,array());
		}
	}	
	
	

	


	private function findMp3CoverInDirectory($extension, $tofind, $directory_start_fullpath, $recursive_lookup=true)
	{

		$str_extension = "";
		if (is_array($extension))
		{
			foreach ($extension as $ext)
				$str_extension .= "'".$ext."',";
			$str_extension = substr($str_extension,0,strlen($str_extension)-1);;
		}
		else
			$str_extension = "'".$extension."'";

		
		if (is_array($tofind))
		{
			$str_tofind = "(";
			foreach ($tofind as $fd)
				$str_tofind .= " filename like ('%".$fd."%') OR";
			$str_tofind = substr($str_tofind,0,strlen($str_tofind)-2);
			$str_tofind .= ")";
		}
		else
			$str_tofind = "filename like ('%".$tofind."%')";

		
		if ($recursive_lookup)
			$directory_start_fullpath = $directory_start_fullpath."%";
		
		$this->DB->query("SELECT * from files where $str_tofind  AND extension IN ($str_extension) AND directory_id IN (SELECT id FROM directory where parent||name LIKE ('$directory_start_fullpath') order by parent,name);","findElement");
		
		return $this->DB->fetcharray();	
	
	
	}


	private function encodetoUTF8($string)
	{
		setlocale(LC_CTYPE, 'en_US.utf8');
		return iconv("UTF-8","UTF-8//IGNORE",$string);
	}

	private function getExtension($fichier) 
	{
    	$bouts = explode(".", $fichier);
    	$extension = array_pop($bouts);
	    return $extension;
	} 

	private function datetime2timestamp($string) {
		list($date, $time) = explode(' ', $string);
		list($year, $month, $day) = explode('-', $date);
		list($hour, $minute, $second) = explode(':', $time);
	
		$timestamp = mktime($hour, $minute, $second, $month, $day, $year);
	
		return $timestamp;
	}		
	
	public function natConvert($string)
	{
		// CREDITS: DRUPAL NATSORT PGSQL function
		
		// Our task is to expand all numbers to have a fixed number of digits.
		// 'I want 3.5 potatoes' -> 'I want [0000003500] potatoes'. 
	
		// Let's start by delimiting all numbers by the '~' character.
		// 'I want 3.5 potatoes' -> 'I want ~3.5~ potatoes'. 
		$string = str_replace("~", "", $string);
		$string = preg_replace('/([0-9]*\.?[0-9]+|[0-9][0-9,]+\.?[0-9,]*[0-9])/i', '~${1}~', $string); 
		
		// Now, let's split the string, on the '~' character, and loop over the elements.
		// Odd elemenets are numbers. Even ones are texts. 
		$array_decoupe = explode("~",$string);
		$part = "";
		foreach ($array_decoupe as $key=>$item)
		{
			if ($key % 2 == 0) 
			{
				$part.= strtolower($item);
			}
			else
			{
				$item = number_format($item, 3, '', '');
				$item = str_pad($item, 10, "0", STR_PAD_LEFT);
				
				$part.= "[".$item."]";
			}
						
		}
		$bad_word = array(",",":",".",";","(",")");
		$part = str_replace($bad_word, "", $part);
		return $part;
	}



	public function getInserted_directory()
	{
		return $this->inserted_directory;
	}
	public function getSkipped_directory()
	{
		return $this->skipped_directory;
	}
	public function getInserted_file()
	{
		return $this->inserted_file;
	}
	public function getUpdated_file()
	{
		return $this->updated_file;
	}	
	public function getSkipped_file()
	{
		return $this->skipped_file;
	}



}



?>