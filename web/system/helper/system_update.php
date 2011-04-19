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


function generalIndexing()
{
	// ********* STARTING INDEXATION *******************
	
	
	$qnap_event =& load_class('QNAP_logs');
	$benchmark =& load_class('Benchmark');
	$index =& load_class('Indexing'); 
	$monitoring =& load_class('Directory_monitoring');
	
	loadHelper ('directory');	
	loadHelper ('utility');	
	
	
	$benchmark->mark('start');
	log_message('debug', "Starting scandirectory indexing");	
	$index->scanDirectories(STARTING_FOLDER,'', true, false, true, array());
	log_message('debug', "Ending scandirectory indexing");	
	$benchmark->mark('end');	
	$result = $benchmark->elapsed_time('start','end');
	
	$string = "Indexing: directory (".$index->getInserted_directory()." added, ".$index->getSkipped_directory()." skipped), files (".$index->getInserted_file()." added, ".$index->getUpdated_file()." updated, ".$index->getSkipped_file()." skipped) --- Total processing time: ".secondsToWords($result);
	
	
	$qnap_event->writeEventLog(0,'Plugmedia', '127.0.0.1', 'localhost' , $string);
	log_message('debug', "Qnap event database populated with string ".$string." DEBUG: ".$result);	

	
	
	$monitoring->sendMailFollowers();
	
}
	



function extractMetadata()
{
	
	// ********* METADATA EXTRACTION *******************
	global $DB;
	
	$qnap_event =& load_class('QNAP_logs');
	$metadata =& load_class('Metadata');


	loadHelper ('utility');
	
	
	$qnap_event->writeEventLog(0,'Plugmedia', '127.0.0.1', 'localhost' , 'Starting metadata extraction');
	log_message('debug', "Starting metadata extraction");

	
	

	$DB->query("select count(*) as result from files WHERE metadata_extracted = 0","CLI");
	$result = $DB->fetchrow();
	$total = $result['result'];

	$number_per_query = 500;	
	$count = 0;	
	$last_id = 0;
	
	
	
	while ($total > 0)
	{
		$DB->query("select parent , name , filename , files.id , extension, file_hash  FROM  files  LEFT JOIN directory ON files.directory_id = directory.id  WHERE metadata_extracted=0  ORDER BY files.id LIMIT ".$number_per_query ,"CLI");
		$result = $DB->fetcharray();
		foreach ($result as $item)
		{
			$metadata->extractMetadata($item['id'], $item['filename'], $item['parent'].$item['name']."/",$item['file_hash'], $item['extension']);
		}
		$total -=$number_per_query;
	}

	$qnap_event->writeEventLog(0,'Plugmedia', '127.0.0.1', 'localhost' , 'End metadata extraction');
	log_message('debug', "End metadata extraction");


}


function thumbnailGeneration()
{
	
	// ********* THUMBNAIL GENERATION *****************************
	global $DB;
	global $db_config;

	$qnap_event =& load_class('QNAP_logs');
	loadHelper ('thumbnail');	

	log_message('debug', "Memory usage start: ".memory_get_usage(true));

	$qnap_event->writeEventLog(0,'Plugmedia', '127.0.0.1', 'localhost' , 'Start Thumbnail generation');
	log_message('debug', "Start Thumbnail generation");
	
	$number_per_query = 500;
	$string_movie = '';
	foreach ($db_config['EXTENSION_MOV_DISPLAYABLE'] as $item)
		$string_movie .= ",'".$item."'";

	$DB->query("SELECT count(*) as result FROM files WHERE (file_thumb='' OR file_thumb_normal = '') AND extension IN ('jpg','jpeg','gif','png'".$string_movie.")","CLI");
	$result = $DB->fetchrow();
	$total = $result['result'];
	
	
	$last_id = 0;
	
	while ($total > 0)
	{
		
		
		
		$DB->query("SELECT fil.id as file_id, file_thumb, file_thumb_normal, orientation, dir.parent as parent, dir.name as name, fil.filename as filename, fil.extension as extension, fil.file_hash as file_hash FROM files fil JOIN directory dir ON dir.id=fil.directory_id LEFT JOIN metadata_exif ON fil.id = files_id WHERE (file_thumb='' OR file_thumb_normal = '') AND extension IN ('jpg','jpeg','gif','png'".$string_movie.") AND fil.id > ".$last_id." ORDER BY fil.id LIMIT ".$number_per_query,"CLI");
		$result = $DB->fetcharray();
		foreach ($result as $item)
		{

			// generate thumbnail (small and big)
			if ($item['file_thumb'] == '')
				$val = generateThumbWithFilepath($item['filename'], $item['parent'], $item['name'], $item['orientation'], $item['extension'], $item['file_hash'], false, 'small', $item['file_id']);
			if ($item['file_thumb_normal'] == '')
				$val = generateThumbWithFilepath($item['filename'], $item['parent'], $item['name'], $item['orientation'], $item['extension'], $item['file_hash'], false, 'normal', $item['file_id']);
			$last_id = $item['file_id'];
		}
		
		$total -= $number_per_query;
		
	}	
	
	$qnap_event->writeEventLog(0,'Plugmedia', '127.0.0.1', 'localhost' , 'End Thumbnail generation');
	log_message('debug', "End Thumbnail generation");
	
	
	
	
	
	
	
	
	
}


function thumbnailDirectory()
{

	// ********* STEP 4: THUMBNAIL DIRECTORY ASSIGN *******************

	global $DB;

	$qnap_event =& load_class('QNAP_logs');
	
		
	$qnap_event->writeEventLog(0,'Plugmedia', '127.0.0.1', 'localhost' , 'Start Thumbnail directory');
	log_message('debug', "Start Thumbnail directory");
	
	$number_per_query = 500;
	$DB->query("SELECT count(*) as result FROM directory WHERE 	thumbnail_random is NULL ","CLI");
	$result = $DB->fetchrow();
	$total = $result['result'];
	$last_id = 0;
	while ($total > 0)
	{
		$DB->query("SELECT * FROM directory WHERE thumbnail_random is NULL LIMIT ".$number_per_query,"CLI");
		$result = $DB->fetcharray();
		foreach ($result as $item)
		{
			// try to find a thumbnail
			
			$DB->query("UPDATE directory SET thumbnail_random = (select file_thumb from files fl, directory dr where 
					dr.id = fl.directory_id 
					AND file_thumb!='' 
					AND directory_id IN (select id from directory where 
											parent||name like((select parent||name from directory where id= '".$item['id']."')||'%') ORDER BY parent,name) LIMIT 1) WHERE id = '".$item['id']."' ","thumbnailDirectory");
																			
											
		}
		
		$total -= $number_per_query;
		
		
	}	
	
	$qnap_event->writeEventLog(0,'Plugmedia', '127.0.0.1', 'localhost' , 'End Thumbnail directory');
	
	log_message('debug', "End Thumbnail directory");
	
	
	
}












?>