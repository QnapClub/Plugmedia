<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
*
* @package Plugmedia
* @copyright (c) 2009 Christophe Lemoine
* @license http://creativecommons.org/licenses/by-nc-nd/2.0/be/  Attribution-Noncommercial-No Derivative Works 2.0 Belgium
* QNAP Systems, Inc is autorize to distribute and transmit the work
*
* Minimum Requirement: PHP 5
*/

function searchItems($string, $filename=false, $tags=false, $title=false, $description=false)
{




	global $DB;
	$session_member =& load_class('Session');
	$access_path = $session_member->getAccess_path();		
	
	$access_string = "";
	if (is_array($access_path))
	{
		
		foreach ($access_path as $access)
		{
			$access_string .= " parent||name LIKE '".$access['parent'].$access['name']."%' OR ";	
		}
		
		if ($access_string != "")
		{
			$access_string=substr($access_string,0,(strLen($access_string)-3));//this will eat the last AND
			$access_string = " AND (".$access_string.") ";
			
		}
	}

	// parse string
	
	
	$string = trim(str_replace('\"','"',$string));
	
	if ($string!="") 
		$words = ProcessNeedle($string);


	

	
	// FIRST PART, search in files table based on filename, smart_name and smart_description
	$fields = array();
	if ((bool)$filename)
		$fields[] ="filename";
	if ((bool)$title)
		$fields[] ='files.smart_name';	 
	if ((bool)$description)
		$fields[] ='files.smart_description';	
	
	$return_field = " files.id, directory_id, filename, files.original_date, filesize, extension, file_thumb, file_thumb_normal, metadata_extracted, files.smart_name, files.smart_description, parent, name ";
	$table = 'files';



    if (!is_array($fields)) $fields=array($fields);
	

	
	if (count($fields)>=1)
	{
       
		$sql = "SELECT $return_field FROM $table LEFT JOIN directory ON files.directory_id = directory.id WHERE ";
		   
	    $sql .= "(";
		foreach ($words as $word) 
		{
			 $sql .= "(";
			 foreach($fields as $field)
			{
				$sql.=" UPPER($field) LIKE UPPER('%$word%') OR ";
			}
			$sql=substr($sql,0,(strLen($sql)-3));//this will eat the last OR
			$sql .= ") OR ";
		}
		$sql=substr($sql,0,(strLen($sql)-3));//this will eat the last OR
		$sql .= ") ".$access_string;
		$sql.=" ORDER BY filename DESC;"; 	
	

		
		$DB->query($sql,"searchItems");
		$result = $DB->fetcharray();
		
		foreach ($result as $key=>$item)
		{
			$result[$key]['filename_highlighted'] = $result[$key]['filename'];
			$result[$key]['smart_name_highlighted'] = $result[$key]['smart_name'];
			$result[$key]['smart_description_highlighted'] = $result[$key]['smart_description'];	
			
			
			foreach ($words as $word)
			{
				if ((bool)$filename)
				{
					
					$pos1 = stripos($item['filename'], $word);
					if ($pos1 !== false)
					{	
						$result[$key]['filename_highlighted'] = str_ireplace($word, '%%'.$word.'%%', $result[$key]['filename_highlighted']);
					
					}
				}
					
			
			
				if ((bool)$title)
				{
				
					$pos1 = stripos($item['smart_name'], $word);
					if ($pos1 !== false)
						$result[$key]['smart_name_highlighted'] = str_ireplace($word, '%%'.$word.'%%', $result[$key]['smart_name_highlighted']);
						
				}
								
				if ((bool)$description)
				{
				
					$pos1 = stripos($item['smart_description'], $word);
					if ($pos1 !== false)
					{
						//$result[$key]['smart_description_highlighted'] = str_ireplace($word, '<span class="highlight_word">'.$word.'</span>', $result[$key]['smart_description']);
						$result[$key]['smart_description_highlighted'] = str_ireplace($word, '%%'.$word.'%%', $result[$key]['smart_description_highlighted']);
					}
				}	
				
				$result[$key]['filename_highlighted'] = preg_replace('/\%%([^\%%]+)\%%/', '<span class="highlight_word">\1</span>', $result[$key]['filename_highlighted']);
				$result[$key]['smart_name_highlighted'] = preg_replace('/\%%([^\%%]+)\%%/', '<span class="highlight_word">\1</span>', $result[$key]['smart_name_highlighted']);
				$result[$key]['smart_description_highlighted'] = preg_replace('/\%%([^\%%]+)\%%/', '<span class="highlight_word">\1</span>', $result[$key]['smart_description_highlighted']);
	

				
						
			
			}
			

		}

		return ($result);
		
	}
	
	
}

	
function ProcessNeedle($text)
{
	
	$output = array();
	$output2 = array();
	$arr = explode('"',$text);
	
	for ($i=0;$i<count($arr);$i++)
	{
		if ($i%2==0)
			$output=array_merge($output,explode(" ",$arr[$i]));
		else 
			$output[] = $arr[$i];
	}
	foreach($output as $word) if (trim($word)!="") $output2[]=$word;
	return $output2;
} 	

?>