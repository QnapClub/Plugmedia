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

function canManageMetadata($user_info)
{
	if (!isset ($user_info['can_manage_metadata']))
	{
		return false;
		exit();
	}
	if ((bool)$user_info['can_manage_metadata'])
		return true;
	else
		return false;
}

function modifyTitleFile($newtitle, $file_id)
{
	global $DB;
	$types = array('text', 'integer');
	$DB->prepare("UPDATE files SET smart_name = ? WHERE id= ?","modifyTitleFile", $types);
	$DB->execute($newtitle,$file_id);	
	return true;
}

function modifyDescriptionFile($newdescription, $file_id)
{
	global $DB;
	$types = array('text', 'integer');
	$DB->prepare("UPDATE files SET smart_description = ? WHERE id= ?","modifyTitleFile", $types);
	$DB->execute($newdescription,$file_id);	
	return true;	
}


function removeTag($tagname, $file_id)
{
	global $DB;
	$DB->query("SELECT id FROM tags LEFT JOIN tags_files ON tags.id=tag_id WHERE value='$tagname' ","removeTag");
	$array = $DB->fetcharray();
	$total = count($array); 
	if (is_array($array))
	{
		$DB->query("DELETE FROM tags_files WHERE tag_id='".$array[0]['id']."' AND file_id='$file_id' ","removeTag");
		if ($total <=1)
			$DB->query("DELETE FROM tags WHERE value='$tagname' ","removeTag");
	}
}


function addArrayTagToFile($array_tag, $file_id)
{
	global $DB;
	
	// traitement des tags
	// requete: 
	$string_return = $array_tag;
	$array = parseTags($array_tag);
	$insert = $array['INSERT'];
	$select = $array['SELECT'];
	$return_string='';
	if (!$DB->query("INSERT INTO tags (value) VALUES $insert","addTags"))
	{
		// Erreur a l'ajout des tags, il y a donc une/plusieur valeurs qui existent déjà
		
		$DB->query("SELECT value FROM tags where tags.value IN $select","");
		$array_tag = $DB->fetcharray();
		$string = '';
		foreach ($array_tag as $tt)
		{
			$key = array_search($tt['value'], $array['SEARCH']);
			if ($array['SEARCH'][$key] != "" )
			{	
				unset ($array['SEARCH'][$key]);
			}
		}
		foreach ($array['SEARCH'] as $str)
		{
			$string .= "('".$str."'),";
		}
		
		if ($string != '')
			$string = substr($string,0,strlen($string)-1);
		$DB->query("INSERT INTO tags (value)VALUES $string","addTags");
	}

	
	$DB->query("INSERT INTO tags_files (tag_id ,file_id) SELECT tags.id, $file_id FROM tags WHERE  tags.value IN $select","addTags");
	
	return $string_return;
	
}

function getTags($file_id)
{
	global $DB;
	$DB->query("SELECT t.value FROM tags_files th LEFT JOIN tags t ON t.id = th.tag_id WHERE th.file_id = '$file_id'","");
	return $DB->fetcharray();	
}

function getTagsToString($tag_array)
{
	$array = $tag_array;
	$string='';
	foreach($array as $item)
		$string .= '"'.$item['value'].'"'.',';
	$string = substr($string,0,strlen($string)-1);
	return $string;
}

function getTagSuggest($input)
{
	global $DB;
	$DB->query("select * from tags where value  like '%$input%' ","getTagSuggest");
	$result = $DB->fetcharray();
	foreach($result as $item)
		$second_result[] = $item['value'];
	return $second_result;
}


function parseTags($tags)
{
	// retourne une chaine de type (value), (value), ... POUR INSERT
	// et une deuxieme chaine de type (value,value)	 POUR SELECT
	$tab = explode(",", $tags);	
	$array= array();
	$i=1;
	foreach ($tab as $value)
	{
		if ($value != "")
		{
			$value = htmlentities(strtolower($value));
			$array['SEARCH'][$i] = $value;
			$i++;
			$array['INSERT'].= "('".$value."'),";
			$array['SELECT'].= "'".$value."',";
		}
	}
	$array['INSERT'] = substr($array['INSERT'],0,strlen($array['INSERT'])-1);
	$array['SELECT'] = "(".substr($array['SELECT'],0,strlen($array['SELECT'])-1).")";
	return $array;
}




?>