#!/usr/local/apache/bin/php
<?
set_time_limit(0);
if (isset($argc))  
{
	require_once 'system/core/frontcontroller.php';
	loadHelper ('system_update');	
		

	// ********* DETECT IF INDEXING IS ALREADY RUNNING *********
	$pid =  getmypid();

	$Fnm = ROOTPATH."/thumb/cli.lock"; 
	$Fn_date = ROOTPATH."/thumb/cli.date";
	
	if (file_exists($Fnm))
	{
		// GET content of lock file (PID)
		$last_pid = file_get_contents($Fnm);
		exec('ps | grep '.$last_pid.' | awk \'{print $1}\'', $pid_list); 
		if (array_search($last_pid, $pid_list)!==false)
		{
			// INDEXING ALREADY RUNNING
			log_message('debug', "Indexing Cron: Index already running");	
			exit();	
		}
		else
		{
			// delete the lock file to prevent old pid to stay in the file
			unlink($Fnm);
			unlink($Fn_date);
		}
	}

	// create the lock file
	$inF = fopen($Fnm,"w+");
	fwrite($inF, $pid);
	fclose($inF);
	
	
	$fndate = fopen($Fn_date,"w"); 
	fputs($fndate,time());
	fclose($fndate);
	
	// ********* STEP 1: STARTING INDEXATION & SEND MAIL TO FOLLOWERS *******************
	generalIndexing();

	// ********* STEP 2: METADATA EXTRACTION *******************
	extractMetadata();

	// ********* STEP 3: THUMBNAIL GENERATION *****************************
	thumbnailGeneration();

	// ********* STEP 4: THUMBNAIL DIRECTORY ASSIGN *******************
	thumbnailDirectory();
	
	
	

	
	
	
	unlink($Fnm);

	

	exec("chmod -R 0777 ".ROOTPATH."/thumb/");
	exec("chmod -R 0777 ".ROOTPATH."/system/logs/");




}


?>
