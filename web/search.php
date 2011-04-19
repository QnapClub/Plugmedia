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





require_once('system/libraries/smarty_2_6_19/SmartyPaginate.class.php');	// PAGINATION


loadHelper ('search');
if (isset($_POST['text']))
{
	if (isset($_POST['search_description']) &&  $_POST['search_description'] == 'on')
		$search_description = true;
	else
		$search_description = false;

	if (isset($_POST['search_filename']) &&  $_POST['search_filename'] == 'on')
		$search_filename = true;
	else
		$search_filename = false;

	if (isset($_POST['search_tag']) &&  $_POST['search_tag'] == 'on')
		$search_tag = true;
	else
		$search_tag = false;

	if (isset($_POST['search_title']) &&  $_POST['search_title'] == 'on')
		$search_title = true;
	else
		$search_title = false;


	$result = searchItems($_POST['text'],$search_filename,$search_tag,$search_title,$search_description);

}
else
	$result=array();


/*
// -------- PAGINATE ----------------------------------
SmartyPaginate::reset('list');
SmartyPaginate::connect('list');
$number_elem = $directory->getItemPerPage();
SmartyPaginate::setLimit($number_elem,'list');

SmartyPaginate::setTotal($directory->countItemInDirectory(),'list');
SmartyPaginate::setNextText('&#8250;', "list");
SmartyPaginate::setPrevText('&#8249;', "list");	
SmartyPaginate::setUrl('list.php?dir='.$_GET['dir'].'&ref='.$_GET['ref'].'&view=inline', 'list');
SmartyPaginate::assign($SMARTY,'paginate','list');
	
$directory_list = $directory->listDirectory(false,false,SmartyPaginate::getCurrentIndex('list'),$number_elem);	

$PLUGIN_MGT->hook( "list_directory_list", &$directory_list);  

$SMARTY->assign("list",$directory_list);

// -------- END PAGINATE  ------------------------------
$pageURL = 'http';
 if (isset($_SERVER['HTTPS'])) {$pageURL .= "s";}
$pageURL .= "://";*/



$SMARTY->assign("trail","");



$SMARTY->assign("search_result",$result);


if (isset($_GET['view']) && $_GET['view']=='inline')
{
	// output as JSON

	$array_output['breadCrumb0'] =  $SMARTY->fetch('breadcrumb.tpl','breadcrumb');
	
	$array_output['ui_content_pm'] =  $SMARTY->fetch('search_result_inline.tpl','search_result_inline');

	print(json_encode($array_output));
	print("\n");
	

	
}
else
{
		$SMARTY->display_('search_result.tpl','search_result');
}
?>