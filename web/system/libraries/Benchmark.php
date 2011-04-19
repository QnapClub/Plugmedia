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


class CORE_Benchmark {
	
	var $marker = array();

	function CORE_Benchmark()
	{
		//log_message('debug', "Benchmark Class Initialized");
	}

	function mark($name)
	{
		$this->marker[$name] = microtime();
	}
  	
	function elapsed_time($point1 = '', $point2 = '', $decimals = 4)
	{
		
		if ( ! isset($this->marker[$point1]))
		{
			return '';
		}
		
		if ( ! isset($this->marker[$point2]))
		{
			$this->marker[$point2] = microtime();
		}
			
		list($sm, $ss) = explode(' ', $this->marker[$point1]);
		list($em, $es) = explode(' ', $this->marker[$point2]);

		return number_format(($em + $es) - ($sm + $ss), $decimals);
	}
 	
}



?>