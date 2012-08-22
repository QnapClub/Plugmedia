<?php
/**
*
* @package Plugmedia
* @copyright (c) 2009 Christophe Lemoine
* @license http://creativecommons.org/licenses/by-nc-nd/2.0/be/  Attribution-Noncommercial-No Derivative Works 2.0 Belgium
* QNAP Systems, Inc is authorize to distribute and transmit the work
*
* Minimum Requirement: PHP 5
*/


require_once 'system/core/frontcontroller.php';

$directory =& load_class('Directory2'); // FIRST INSTRUCTION

$index =& load_class('Indexing'); 	// Use to add the root path if not present (first time access or root path changed)
if ($index->getInserted_directory()>0)
	header("Location:index.php");


$directory_access = $directory->getDirectory_access();	// obtain all directory that a member can have access


//$PLUGIN_MGT->hook( "index_directory_access", &$directory_access);  


// IF WE GOT ONLY ONE DIRECTORY, REDIRECT TO THE DIR LIST
if (is_array($directory_access) && count($directory_access)==1)
{
	// REDIRECTING
	reset($directory_access);
	$current_value = current($directory_access);
	
	header("location:list.php?dir=".$current_value['dir_id']."&ref=".$current_value['dir_id']);
}


$SMARTY->assign("list",$directory_access);


$PLUGIN_MGT->hook( "index_before_display", $SMARTY);  

$SMARTY->display_('index.tpl','index');	


?>