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


function isIndexingRunning()
{

	$pid =  getmypid();

	$Fnm = ROOTPATH."/thumb/cli.lock"; 
	
	if (file_exists($Fnm))
	{
		// GET content of lock file (PID)
		$last_pid = file_get_contents($Fnm);
		exec('ps | grep '.$last_pid.' | awk \'{print $1}\'', $pid_list); 
		if (array_search($last_pid, $pid_list)!==false)
		{
			// INDEXING ALREADY RUNNING
			return true;
		}
		else
			return false;
	}
	else
		return false;




}

function lastIndexingDate()
{
	
	$Fnm = ROOTPATH."/thumb/cli.date"; 
	if (file_exists($Fnm))
	{
		$last_date = file_get_contents($Fnm);
		$last_date = date('d-m-Y H:i:s', $last_date);
		return $last_date;
	}
	else
		return false;

}
function stopIndexing()
{
	$Fnm = ROOTPATH."/thumb/cli.lock"; 
	if (file_exists($Fnm))
	{
		$pid = file_get_contents($Fnm);
		exec("kill ".$pid);
		unlink($Fnm);
		return true;
	}
	else
		return false;

}

function startIndexing()
{
	exec("/usr/local/apache/bin/php -c /etc/config/php.ini ".ROOTPATH."/cli.php  > /dev/null &");
	return true;
}

function clearCache()
{
	global $DB;
	$DB->query("DELETE FROM directory","clearCache");
	return true;
}

?>