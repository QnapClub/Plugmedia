<?
/**
*
* @package Plugmedia
* @copyright (c) 2009 Christophe Lemoine
* @license http://creativecommons.org/licenses/by-nc-nd/2.0/be/  Attribution-Noncommercial-No Derivative Works 2.0 Belgium
* QNAP Systems, Inc is authorize to distribute and transmit the work
*
* Minimum Requirement: PHP 5
*/

class CORE_Controller {

	var $controller_loaded = array();
	var $bd;
	var $smarty;
	var $benchmark;
	var $config;
	
	function CORE_Controller()
	{
		$this->_core_init();	
	}
	
	function _core_init()
	{
		// Assign all the class objects that were instantiated by the
		// front controller to local class variables
		/*$this->bd =& load_class(Db);
		$this->config =& load_class(Config);
		$this->benchmark =& load_class(Benchmark);
		$this->smarty =& load_class(Smarty);
		$this->controller_loaded = 1;*/
	}
	
	function getDB()
	{
		return $this->bd;
	}
	
	

}


?>