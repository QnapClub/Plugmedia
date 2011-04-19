<?
class CORE_Directory2 {

	private $root_id = '';				// requested directory (id)
	private $root_information = array();
	private $total_dir = 0;			// number of directory in root
	private $total_file = 0;		// number of files in root
	private $outdated = false;		// if true, the database is outdated from physical directory

	// directory access for a member
	private $directory_access = array();	

	// MASKS
	private $hidding_mask_string_sql = "";
	private $hidding_ext_string_sql = "";	
	
	
	// Properties from configuration
	private $mask_name;
	private $mask_extension;
	private $item_per_page;
	private $memory_limit;
	private $exif_autorotate;
	private $utf8_encoding;		
	private $get_first_picture;
	private $recursive_picture_for_song;

	// somes objects references
	private $session_member;
	private $database;
	private $sorting_ordering;

	
	// information on a specific node with exif and id3 tags if needed
	private $specific_node_information = array();	

	
	
	public function CORE_Directory2()
	{
		
		global $DB, $SESSION, $SORTING_ORDER;
		$this->database = $DB;
		$this->session_member = $SESSION;
		$this->sorting_ordering = $SORTING_ORDER;
		
		$this->setConfigurationInformations();
		
		$this->setAccess_directory();

		$this->hidding_mask_string_sql = $this->database->generateSqlString($this->mask_name);
		$this->hidding_ext_string_sql = $this->database->generateSqlString($this->mask_extension);
		
	}
	
	

	
	
	public function setRoot($id_root)
	{
		loadHelper ('utility');
		if ($id_root == '')
		{
			// NO ACCESS...
			$this->root_id = '';
			$this->root_information = '';
			redirectNonAuthorizedUser();
			return false;
			exit();
	
		}
		else
		{
			$prepare_string = "";
			if (is_array($this->directory_access) && count($this->directory_access)>0)
			{
				
				foreach ($this->directory_access as $dir_ac)
				{
					if ($prepare_string =="")
						$prepare_string = " AND (";
					else
						$prepare_string .= " OR ";
					
					if (array_key_exists('directory_id',$dir_ac) && $id_root == $dir_ac['directory_id'])
					{
						$this->root_id = $id_root;
						$this->root_information = $this->getInfoDirectory($dir_ac); 
						return true;
					}
					if (!array_key_exists('directory_id',$dir_ac))
						$dir_ac['parent']="";
					$prepare_string .= "parent||name LIKE '".$dir_ac['parent'].$dir_ac['name']."%'";
				}

				$prepare_string .= ")";
				$this->database->query("select * from directory where id='$id_root' $prepare_string","prepareDirectory");
				$result = $this->database->fetchrow();
				
				if ($id_root == $result['id'])
				{
					$this->root_id = $id_root;
					$this->root_information = $this->getInfoDirectory($result); 
					return true;
				}
				else
				{
					$this->root_id = '';
					$this->root_information = '';
					redirectNonAuthorizedUser();
					return false;
					exit();
				}
			}
			else
			{
				$this->root_id = '';
				$this->root_information = '';
				redirectNonAuthorizedUser();
				return false;
				exit();			
			}
		}
		
		
		
	}
	
	public function getDirectory_access()
	{
		return $this->directory_access;
	}


	// ONLY FOR ADMINISTRATION USE !!!
	public function ListDirectoryWithoutAccess($root_id, $onlydir = false, $onlyfile = false, $start=0, $size=0)
	{
		$this->root_id = $root_id;
		return $this->listDirectory($onlydir, $onlyfile, $start, $size);
	}


	public function listDirectory($onlydir = false, $onlyfile = false, $start=0, $size=0)
	{

		if ($this->root_id == '')
		{
			// No listing allowed, forward to the index page
			loadHelper ('utility');
			redirectNonAuthorizedUser();
			return false;
			exit();
		}
	
		$dir = $this->root_id;
		
		if ($start == $size && $start == 0)
			$limit = "";
		else
			$limit = " OFFSET ".$start." LIMIT ".$size;
		$all_f = array();
		
		
		$ordering = $this->sorting_ordering->getOrdering();
		$sorting = $this->sorting_ordering->getSorting();
		
		
		if (!$onlyfile)
		{
			switch ($sorting)
			{
				case 'N': $sorting_strg= "formated_name"; break;
				case 'D': $sorting_strg= "original_date"; break;
				case 'S': $sorting_strg= "formated_name"; break;
				case 'M': $sorting_strg= "formated_name, original_date"; break;	
				default: $sorting_strg= "formated_name"; break;			
			}
			
			$this->database->query("select * from directory where name NOT IN ".$this->hidding_mask_string_sql." AND parent = (SELECT parent||name||'/' from directory where id='$dir') order by $sorting_strg $ordering $limit","listDirectory");
			
			$dir_array = $this->database->fetcharray();
			$size_dir = count($dir_array);
			
			foreach ($dir_array as $dir_item)
			{
				$all_f[] = $this->getInfoDirectory($dir_item);
				if ($size == 0)
				{
					// If size == 0 we want all files/directory UNDER root_id, so we will count items directly in this function...
					$this->total_dir ++;
				}
			}
		}
		
		if (!$onlydir)
		{		
			switch ($sorting)
			{
				case 'N': $sorting_strg= "fil.formated_name"; break;
				case 'D': $sorting_strg= "fil.original_date"; break;
				case 'S': $sorting_strg= "fil.filesize"; break;
				case 'M': $sorting_strg= "fil.formated_name, original_date"; break;	
				default: $sorting_strg= "fil.formated_name"; break;			
			}


			if ($limit != "")
				$limit = " OFFSET ".$start." LIMIT ".($size - $size_dir);
			$this->database->query("SELECT fil.*, dir.parent||dir.name as directory_name FROM files fil, directory dir WHERE extension NOT IN ".$this->hidding_ext_string_sql." AND filename NOT IN ".$this->hidding_mask_string_sql." AND fil.directory_id = dir.id AND fil.directory_id = '$dir'   order by $sorting_strg $ordering   $limit","listDirectory");
			$files_array = $this->database->fetcharray();		
			$size_files = count($files_array);
			
			
			foreach ($files_array as $file_item)
			{
				$all_f[] = $this->getInfoFile($file_item);
				if ($size == 0)
				{
					// If size == 0 we want all files/directory UNDER root_id, so we will count items directly in this function...
					$this->total_file ++;
				}				
			}
		}
		
		return $all_f;
	
	
	}
	
	// DETECT outdated directory (ONLY ADD or REMOVED file/directory in $directory_fullpath), renamed file/folder are handled in ListDirectory with a physical existing check
	public function detectOutdatedDirectory($directory_fullpath, $total_dir=false, $total_file=false)
	{

		$total['dir']=0;
		$total['file']=0;
		$dossier=opendir($directory_fullpath);
		
		//$dossier = scandir($directory_fullpath);
		
		
		
		
		while ($fichier = readdir($dossier))
		{
			
			if (!in_array( $fichier, $this->mask_name))
			{
				
				$continue = true;
				preg_match("/\.([^\.]+)$/", $fichier, $matches);
				if (isset ($matches[1]))
				{

					if (@in_array(strtolower($matches[1]), $this->mask_extension))
						$continue = false;
				}
				if ($continue)
				{
					// IS A DIRECTORY OR OTHER?
					if (is_dir($directory_fullpath."/".$fichier))
					{
						$total['dir'] ++;
					}else
						$total['file'] ++;	
				
				}		
			
			}
		}

		if ($total['dir'] != $total_dir)
			$this->outdated = true;
		if ($total['file'] != $total_file)
			$this->outdated = true;			
		return $total;
	}
	
	public function getSpecificItemInDirectory($id_file, $number_before=2, $number_after=2)
	{
		
		$return = $this->listDirectory(false, true, 0, 0);
		
		$item_position = 0;
		foreach ($return as $key=>$file)
		{
			$item_position++;
			if ($file['file_id'] == $id_file)
			{
				$current_item = $file;
				$current[0] = $file;
				$current[0]['current'] = true;
				$this->specific_node_information = $current[0];
				// we will set in the session the url of the file for a next display...
				$this->session_member->setData('file_to_display',$this->specific_node_information);
				break;
			}
		}
		
		
		
		$cut_from_position = $item_position - 1 - $number_before;
		if ($cut_from_position < 0)
			$cut_from_position = 0;
		
		
		
		$first_cut = array_slice($return, $cut_from_position, $number_before+1+$number_after);
		
		
		$array_element_before = array();
		$array_element_after = array();
		$before = true;
		foreach ($first_cut as $item)
		{
			if ($item['file_id'] != $id_file)
			{
				if ($before)
					$array_element_before[] = $item;
				else
					$array_element_after[] = $item;	
			}
			else
				$before = false;
			
			
		}
		
		
		if (is_array(end ($array_element_before)))
			$result['prev_item'] = end ($array_element_before);
		else
			$result['prev_item'] = "";
		
		if (array_key_exists(0,$array_element_after) && is_array($array_element_after[0]))
			$result['next_item'] = $array_element_after[0];
		else
			$result['next_item'] = "";	
		
		
		$result['list'] = array_merge((array)$array_element_before, (array)$current, (array)$array_element_after);
		
		
		
		return $result;

	}
	
	public function getSpecificNodeInformation()
	{

		$configDB =& load_class('ConfigLoader');
		// add exif informations if needed
		
		if (in_array(strtolower($this->specific_node_information['readable_type']), $configDB->getValue('EXTENSION_IMG')) || in_array(strtolower($this->specific_node_information['readable_type']), $configDB->getValue('EXTENSION_RAW')))
		{
			$this->database->query("SELECT * FROM metadata_exif WHERE files_id = '".$this->specific_node_information['file_id']."'","getSpecificNodeInformation");
			$this->specific_node_information['exif'] = $this->database->fetchrow();				
			
			$this->specific_node_information['exif_rotate'] = $this->specific_node_information['exif']['orientation'];
			// exif autorotate
			if ($this->exif_autorotate) {
				switch ($this->specific_node_information['exif_rotate']) 
				{
						case 1:
							$rotate_angle = 0;
						break;
						case 3:
							$rotate_angle = 180;
						break;
						case 6:
							$rotate_angle = 270;
						break;
						case 8:
							$rotate_angle = 90;
						break;
						default:
							$rotate_angle = 0;
						break;
				}
			}
			$this->specific_node_information['exif_rotate'] = $rotate_angle;
			if ($this->specific_node_information['exif']['gpslatituderef'] && $this->specific_node_information['exif']['gpslatitude'] && $this->specific_node_information['exif']['gpslongituderef'] && $this->specific_node_information['exif']['gpslongitude'])
			{
				loadHelper ('geocode');
				$this->specific_node_information['exif_geocode'] = getCoordinate($this->specific_node_information['exif']['gpslatituderef'], split(",",$this->specific_node_information['exif']['gpslatitude']), $this->specific_node_information['exif']['gpslongituderef'], split(",",$this->specific_node_information['exif']['gpslongitude']) );
			}

			
			loadHelper ('picture');
			
			$this->specific_node_information = array_merge($this->specific_node_information,resizePicture($this->specific_node_information));
		}
		
		// add ID3 tags informations if needed
		// IF SONG, GET IDTAG
		if ($this->specific_node_information['extension'] == 'song')
		{
			$mp3 =& load_class('ID3');
			$mp3->addFilname($this->specific_node_information['name']);
			$this->specific_node_information['mp3_info'] = $mp3->getInfo();
			
			// LOOKING FOR PICTURE IN THE DIRECTORY...
			$picture = $this->findElement('jpg', array('front','cover','folder'), $this->root_information['name'], $this->recursive_picture_for_song);
			if (is_array($picture))
			{
				$first = reset($picture);	
				// Add the picture to array
				if ($first['file_thumb'] != "" && substr_count($first['file_thumb'], "&")>0)
					$this->specific_node_information['album_pic'] = "api.php%3Fac%3DgetFileContent%26file%3D".$first['id']."%26dwl%3D0";
				else
					$this->specific_node_information['album_pic'] = $first['file_thumb'];
			}
		
		}

		// add comments on the file	
		loadHelper ('comments');
		$this->specific_node_information['comments'] = getComments($this->specific_node_information['file_id']);
		
		
		return $this->specific_node_information;
	
	}
	


	public function getItemPerPage()
	{
		return (int)$this->item_per_page;
	}


	public function countItemInDirectory()
	{
		if ($this->total_dir == 0)	// ALREADY FILL IN IN LISTDIRECTORY FUNCTION?
			$this->countDirectoryInDirectory();
		if ($this->total_file == 0)	// ALREADY FILL IN IN LISTDIRECTORY FUNCTION?
			$this->countFileInDirectory();
		if (REVOKE_OUTDATED)
			$this->detectOutdatedDirectory($this->root_information['name'], $this->total_dir, $this->total_file);
		
		return $this->total_dir + $this->total_file;
	}



	public function countDirectoryInDirectory()
	{
		global $DB;
		$dir = $this->root_id;
		$this->database->query("select count(id) as total from directory where name NOT IN ".$this->hidding_mask_string_sql." AND parent = (SELECT parent||name||'/' from directory where id='$dir')","countFileInDirectory");
		$total = $this->database->fetchrow();
		$this->total_dir = $total['total'];
		return $this->total_dir;	
	}
	
	
	public function countFileInDirectory()
	{
		global $DB;
		$dir = $this->root_id;
		$this->database->query("SELECT count(fil.id) as total FROM files fil, directory dir WHERE fil.extension NOT IN ".$this->hidding_ext_string_sql." AND fil.filename NOT IN ".$this->hidding_mask_string_sql." AND fil.directory_id = dir.id AND fil.directory_id = '$dir'","countDirectoryInDirectory");
		$total = $this->database->fetchrow();
		$this->total_file = $total['total'];
		return $this->total_file;
	
	}




	
	private function setAccess_directory()
	{
		$access_path = $this->session_member->getAccess_path();		
		if (is_array($access_path))
		{
			foreach ($access_path as $key=>$dir)
			{
				$this->directory_access[$dir['id']] =$this->getInfoDirectory($dir);
			}
		}
		else
			$this->directory_access= array();
	}
	
	
		

	// Get information for one directory (thumb, type(dir or link), name, formated_name, short_name, short_nameformated, date )
	// IN: $directory (array())
	// OUT: array(thumb, type(dir or link), name, formated_name, short_name, short_nameformated, date )		
	private function getInfoDirectory($dir)
	{
		if ($dir == "" || $dir == false)
		{
			return false;
			exit();
		}
		if ($this->get_first_picture)
			$array_return['thumb'] =  $dir['thumbnail_random'];
		else
			$array_return['thumb'] =  $dir['thumbnail'];

		$array_return['thumb_size'] = $this->thumbnailSizeGen($array_return['thumb']);
		
		//$array_return['type'] = filetype($dir);
		$array_return['type'] = 'dir';
		$array_return['name'] = $dir['parent'].$dir['name'];
		if ($dir['name']=="")
		{
			// cut the directory parent
			$champs = explode ("/", $dir['parent']); 
			$array_return['short_name'] = end($champs);
		}
		else
			$array_return['short_name'] = $dir['name'];
		$array_return['readable_type'] = "file folder";
		$array_return['name_formated'] = $dir['name'];
		$array_return['short_name_displayable'] = $this->cutLongFilename($array_return['short_name']);
		$array_return['short_name_formated'] = $array_return['short_name'];
		$array_return['original_date'] = $dir['original_date'];
		if (array_key_exists('directory_id',$dir))
			$array_return['dir_id'] = $dir['directory_id'];
		else
			$array_return['dir_id'] = $dir['id'];
		return $array_return;
	}

	private function thumbnailSizeGen($thumbaddr)
	{
		if (is_file($thumbaddr))
		{
			list($width, $height, $type, $attr) = @getimagesize($thumbaddr);
			if ($width > $height)
				return ' width="'.(400).'" style="width: 4em;" ';
			else
				return ' height="'.(400).'" style="height: 4em;"';
		}
		else
			return '';
	}

	// Get information for one file
	// IN: $file (array())
	// OUT: array(thumb, type, name, formated_name, short_name, short_nameformated, date )		
	private function getInfoFile($fichier)
	{
		$array['size'] = $this->convertSize($fichier['filesize']);
			
			
		$extension = $fichier['extension'];
		
		$array['type'] = 'file';
		$array['file_id'] = $fichier['id'];
		$array['directory'] = $fichier['directory_id'];
		$array['readable_type'] = $extension;
		$array['name'] = $fichier['directory_name']."/".$fichier['filename'];
		$array['name_encoded'] = urlencode($array['name']);
		$array['name_formated'] = $array['name'];
		$array['short_name'] = $fichier['filename'];
		$array['short_name_displayable'] = $this->cutLongFilename($fichier['filename']);
		$array['short_name_formated'] = $fichier['filename'];
		$array['last_modification_date'] = $fichier['timestamp_modification'];

		$array['smart_name'] = $fichier['smart_name'];
		$array['smart_description'] = $fichier['smart_description'];
		
		if (!isset ($array['original_date']))
		{	
			$array['original_date'] = $fichier['original_date'];
		}
		
		//$array['thumbnail'] = $fichier['file_thumb'];
				
		if (in_array(strtolower($extension), $this->extension_img)||in_array(strtolower($extension), $this->extension_raw))
		{
			// recognize picture
			$array['extension'] = 'picture';										
			$array['tpl_media'] = 'media_picture.tpl';
			// get thumb
			$thumb_info = $this->getPictureThumb($fichier['file_thumb'],$fichier['file_thumb_normal']); // GET THUMBNAIL
			$array = array_merge($array, $thumb_info);
									
		}elseif (in_array(strtolower($extension), $this->extension_mov))	
		{
			$array['tpl_media'] = 'media_other.tpl';
			$array['extension'] = 'movie';
			
			$thumb_info = $this->getPictureThumb($fichier['file_thumb'],$fichier['file_thumb_normal']); // GET Thumbail and converted movie
			$array = array_merge($array, $thumb_info);			
			
		}
		elseif (in_array(strtolower($extension), $this->extension_song))	
		{
			$array['tpl_media'] = 'media_song.tpl';
			$array['extension'] = 'song';
		
			$thumb_info = $this->getSongThumb($fichier['file_thumb'],$fichier['file_thumb_normal']); // GET THUMBNAIL
			$array = array_merge($array, $thumb_info);
			
		}elseif (in_array(strtolower($extension),$this->extension_mov_displayable))	
		{
			$array['tpl_media'] = 'media_movie.tpl';
			$array['extension'] = 'movie_displayable';
			$thumb_info = $this->getPictureThumb($fichier['file_thumb'],$fichier['file_thumb_normal']); // GET THUMBNAIL
			$array = array_merge($array, $thumb_info);
		}
		else	
		{
			$array['tpl_media'] = 'media_other.tpl';
			$array['extension'] = 'other';					
		}
		
		return $array;
	}	

	private function getPictureThumb($possible_small_thumb, $possible_normal_thumb)
	{
		if (!is_file($possible_small_thumb))
		{
			$array['generate_thumb'] = true;
			$array['small_thumbnail'] = '';
			$array['normal_thumbnail'] = '';
			
		}else
		{
			$array['small_thumbnail'] = $possible_small_thumb;			
			$array['normal_thumbnail'] = $possible_normal_thumb;
			$array['small_thumbnail_size'] = $this->thumbnailSizeGen($possible_small_thumb);
		}
		return $array;
	}

	private function getSongThumb($possible_small_thumb, $possible_normal_thumb)
	{
		if (!is_file($possible_small_thumb))
		{
			$array['small_thumbnail'] = '';
			$array['normal_thumbnail'] = '';
			
		}else
		{
			$array['small_thumbnail'] = $possible_small_thumb;
			$array['normal_thumbnail'] = $possible_normal_thumb;
			$array['small_thumbnail_size'] = $this->thumbnailSizeGen($possible_small_thumb);
		}
		return $array;
	}

	
	
	private function cutLongFilename($filename)
	{
		/*if (strlen($filename) > 10)
			$filename = $this->wordwrapBySpecifyChars($filename,11,"<br />\n", array(",", ";" ,".","_","-"));*/
		return $filename;
	}

	private function wordwrapBySpecifyChars($strForWrap, $maxLength = 80, $breakChar = "\n", $wrapChars = array(",", ";" ,".","_","-"))
    {
		$newStr = null;
        $length_of_string = strlen($strForWrap);
       
        if ($length_of_string <= $maxLength)
            {
            return $strForWrap;
            }
           
        $count_of_string = 1;   
        $wait_new_line = false;
       
        for($i=0; $i<$length_of_string; $i++)
            {
            if ( $count_of_string*$maxLength == $i || $wait_new_line)
                {
               
                if (in_array($strForWrap{$i}, $wrapChars ) )
                    {
                    $count_of_string ++;
                    
                    $newStr .= $strForWrap{$i}.$breakChar;
                    $wait_new_line = false;
                    }
                else
                    {
                    $newStr .= $strForWrap{$i};
                    $wait_new_line = true;
                    }
                }
            else
                {
                $newStr .= $strForWrap{$i};  
                }
            
            }
           
		return $newStr;
	}	
	



	private function setConfigurationInformations()
	{
		// Loading configuration informations (mask_name && mask_extension
		global $db_config;
		$this->mask_name = $db_config['HIDDING_MASK']; 
		$this->mask_extension = $db_config['HIDDING_EXTENSION'];	
		$this->item_per_page = $db_config['ITEM_PER_PAGE'];
		$this->get_first_picture = (bool)$db_config['GET_FIRST_PICTURE'];	
		$this->memory_limit = $db_config['PHP_MEMORY_LIMIT'];	
		$this->exif_autorotate = (bool)$db_config['EXIF_AUTOROTATE'];	
		$this->utf8_encoding = false;	//(bool)$db_config['UTF8_ENCODING'];	
		$this->recursive_picture_for_song = (bool)$db_config['RECURSIVE_PICTURE_FOR_SONG'];
		$this->extension_img = $db_config['EXTENSION_IMG'];
		$this->extension_mov = $db_config['EXTENSION_MOV'];
		$this->extension_mov_displayable = $db_config['EXTENSION_MOV_DISPLAYABLE'];		
		$this->extension_song = $db_config['EXTENSION_SONG'];
		$this->extension_raw = $db_config['EXTENSION_RAW'];

				
	
		
		// ADD SPECIAL DIRECTORY IN MASK 
		$this->mask_name[] = ".@__thumb";
		$this->mask_name[] = ".@__comments";
		$this->mask_name[] = ".";
		$this->mask_name[] = "..";
		
	}

	
	private function convertSize($sz)
	{
		if ($sz >= 1073741824)  {$sz = round($sz / 1073741824 * 100) / 100 . " GB";}
		elseif ($sz >= 1048576) {$sz = round($sz / 1048576 * 100)    / 100 . " MB";}
		elseif ($sz >= 1024)    {$sz = round($sz / 1024 * 100)       / 100 . " KB";}
		else                    {$sz = $sz . " Bytes";}
		return $sz;
	}

	
	public function getInfoFromRoot()
	{
		//$current["name"] = end($champs);
		$current["total_dir"] = $this->total_dir;
		$current["total_file"] = $this->total_file;
		$current["link_dir"] = $this->root_id;
		$current = array_merge($current, $this->root_information);
		return $current;

	}

	public function parseDirectory($ref)
	{
		if (!array_key_exists('parent',$this->root_information))
			$this->root_information['parent'] = "";
		$full_path = $this->root_information['parent'].$this->root_information['name'];
	
		// BUILD QUERY STRING
		$starting = explode ("/", STARTING_FOLDER);
		$starting = end($starting);		
		// now we have the starting point		
		
		$toremove = str_replace(STARTING_FOLDER, "",$full_path);
		
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
		$this->database->query("SELECT id, parent, name FROM directory WHERE ".$string." order by parent","");
		$array = $this->database->fetcharray();
		//print_r ($array);
		$display = false;
		$trailer = array();
		$base_url_link = 'list.php';
		foreach ($array as $item)
		{
			if ($ref == $item['id'])
			{
				if ($item['name'] == "")
				{
					$trail['reference'] = $starting;
					$trail['link']	= $base_url_link.'?dir='.$array[0]['id'].'&ref='.$ref.'&view=inline';
				}
				else
				{
					$trail['reference'] = $item['name'];
					$trail['link']	= $base_url_link.'?dir='.$item['id'].'&ref='.$ref.'&view=inline';
				}
 				$trailer[]=$trail;
				$display = true;
				
			} else if ($display)
			{
				$trail['reference'] = $item['name'];
				$trail['link']	= $base_url_link.'?dir='.$item['id'].'&ref='.$ref.'&view=inline';
				$trailer[]=$trail;
			}
		}
		
		
		return $trailer;
	}

	public function isOutdated()
	{
		return $this->outdated;
	}
	
	public function findElement($extension, $tofind, $directory_start_fullpath, $recursive_lookup=true)
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
		
		$this->database->query("SELECT * from files where $str_tofind  AND extension IN ($str_extension) AND directory_id IN (SELECT id FROM directory where parent||name LIKE ('$directory_start_fullpath') order by parent,name);","findElement");
		
		return $this->database->fetcharray();	
	}


	
}

?>