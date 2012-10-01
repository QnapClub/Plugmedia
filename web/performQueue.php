#!/usr/local/apache/bin/php
<?php
set_time_limit(0);
if (isset($argc))  
{
	require_once 'system/core/frontcontroller.php';
	
	$queue = load_class('Queue');

	log_message('debug', "QUEUE : Starting movie convert queue"); ".\n";
	$queue->performQueue('movie_convert',10);	// perform treatement of the QUEUE movie_convert	
	
}
?>