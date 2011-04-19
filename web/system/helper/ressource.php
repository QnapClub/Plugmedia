<?


function render_ressource($id_ressource, $access_folder, $force_download = true, $autorized_access= true)
{
	global $DB;
	$force_download = (bool)$force_download;

	if ($ressource = allowRessourceAccess($id_ressource, $access_folder, $autorized_access))
	{

		// all is good	
		$file = $ressource['parent'].$ressource['name']."/".$ressource['filename'];
		$file_name = $ressource['filename'];
		$taille=filesize($file);
		

		if (!$force_download)
		{
			
			header("Content-Type: " . $ressource['mimetype']);
			header('Content-length: ' . $taille);
			header('Content-Disposition: filename="' . $file_name . '"');
			header('Connection: close');
			header('Cache-Control: no-cache');
			header("Content-Transfer-Encoding: binary");
			
						
			/*$fp = fopen($file,"rb");
			$taillefichier = filesize($file);
			$offset = 0;
			
			while(!feof($fp))
			{
				if (!connection_aborted())
				{
					
					$meuh = fread($fp,8192-$offset);
					
					
					
					echo $meuh;
					
					//ob_flush();
    				//flush();
					
					if(feof($fp))
					{
						$offset = $taillefichier;
					}
					 
				}
				else
					exit();
			}
			/*
			//
			$fh = fopen($file, "rb");
			fseek($fh, 0);
			while (!feof($fh)) {
				print (fread($fh, 16384));
				// print (fread($fh, filesize($file)));
			}
			fclose($fh);
			print(pack('N', 9 ));
			*/
			readfile($file);
			
		}
		else
		{
			
			if(ini_get('zlib.output_compression'))
  				ini_set('zlib.output_compression', 'Off');
			header("Pragma: public"); // required
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false); // required for certain browsers 
			header("Content-Type: application/force-download; name=\"$file\"");
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: $taille");
			header("Content-Disposition: attachment; filename=\"$file_name\"");
			header("Expires: 0");
			header("Cache-Control: no-cache, must-revalidate");
			readfile($file);
			
		}
		
		exit(); 
	}
	
}

function allowRessourceAccess($id_ressource, $access_folder, $autorized_access= true)
{
	global $DB;
	$prepare_string = "";
	if ($autorized_access)
	{
		foreach ($access_folder as $dir_ac)
		{
			if ($prepare_string =="")
				$prepare_string = " AND (";
			else
				$prepare_string .= " OR ";
			$prepare_string .= "parent||name LIKE '".$dir_ac['parent'].$dir_ac['name']."%'";
		}
		$prepare_string .= ")";
	}
	
	$DB->query("select  mimetype, files.*, directory.* from files LEFT JOIN directory ON directory.id = files.directory_id LEFT JOIN mimetype ON mimetype.extension = files.extension where files.id = '$id_ressource' $prepare_string","");
	
	$array = $DB->fetchrow();
	if (is_array($array))
		return $array;
	else
		return false;
}


?>
