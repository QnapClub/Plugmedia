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


require_once ('smarty_3_0_4/Smarty.class.php');

class CORE_Smarty extends Smarty {
	
	var $CSS = array();
	var $JS = array();
	var $adresseCSS;
	var $adresseJS;
	 
	
	function CORE_Smarty() 
	{
   
       


		$CFG = load_class('Config');
		$CFG->load('smarty.php');
		parent::__construct();
		//template par défaut
		
		$this->setTemplateDir(BASEPATH.$CFG->item('smarty_template_dir'));
		$this->setComplieDir(BASEPATH.$CFG->item('smarty_compile_dir'));
		$this->setCacheDir(BASEPATH.$CFG->item('smarty_cache_dir'));
		$this->setAdresseImages(SYS_FOLD.'/'.$CFG->item('image_dir'));
		
		$this->setAdresseDesign(SYS_FOLD.'/'.$CFG->item('root_dir'));
		
		$this->adresseCSS = SYS_FOLD.'/'.$CFG->item('css_dir');
		$this->setAdresseCSS($this->adresseCSS);
		
		$this->adresseJS = SYS_FOLD.'/'.$CFG->item('js_dir');
		$this->setAdresseJS($this->adresseJS);
		
		$this->setAdresseROOT($CFG->item('root_dir'));
		

		
		$this->compile_check = $CFG->item('compile_check');
		$this->debugging = $CFG->item('debugging');

		$this->caching = $CFG->item('caching');
		
		//$this->register_block('dynamic', 'smarty_block_dynamic', false);
		$this->use_sub_dirs = $CFG->item('use_sub_dirs');
		
		//log_message('debug', "Smarty Class Initialized");
	}
	

		
	function setTemplateDir($adress)
	{
		$this->template_dir = $adress;	
	}
	function setComplieDir($adress)
	{
		$this->compile_dir = $adress;	
	}
	function setConfigDir($adress)
	{
		$this->config_dir = $adress;	
	}
	function setCacheDir($adress)
	{
		$this->cache_dir = $adress;	
	}
	function setAdresseDesign($adress)
	{
		$this->assign("adresse_design",$adress);	
	}	
	function setAdresseImages($adress)
	{
		$this->assign("adresse_images",$adress);	
	}	
	function setAdresseJS($adress)
	{
		$this->assign("adresse_js",$adress);	
	}	
	function setAdresseCSS($adress)
	{
		$this->assign("adresse_css",$adress);	
	}
	function setAdresseROOT($adress)
	{
		$this->assign("adresse_root",$adress);	
	}	
	
	function setRssFeed($cache = true)
	{
		$CFG = load_class('Config');
		$CFG->load('smarty.php');

		$this->setTemplateDir(BASEPATH.'views/rss/tpl');
		$this->setComplieDir(BASEPATH.'_compiled');
		$this->setAdresseImages($CFG->item('base_url').'system/views/rss/img');
		if ($cache)
			$this->caching = true;
		else
			$this->caching = false;
	}	
	
	function setMobileTemplate($reference)
	{
		global $db_config;
		$this->setTemplateDir(SYS_FOLD.'/views/'.$db_config['TEMPLATE_MOBILE_PHONE'].'/tpl');
		$this->setComplieDir(BASEPATH.'_compiled');
		$this->setAdresseImages(SYS_FOLD.'/views/'.$db_config['TEMPLATE_MOBILE_PHONE'].'/img');
		$this->setAdresseCSS (SYS_FOLD.'/views/'.$db_config['TEMPLATE_MOBILE_PHONE'].'/css');
		$this->setAdresseJS (SYS_FOLD.'/views/'.$db_config['TEMPLATE_MOBILE_PHONE'].'/js');
	}


	function display_($tpl, $reference_cache=NULL)
	{
		global $PHP_ERROR;
		if ($reference_cache==NULL)
			$reference_cache = $tpl;
		$BM = load_class('Benchmark');
		$ERROR = load_class('Error');
		$DB = load_class('Db_postgresql');
		
		// ASSIGNATION DU TEMPS DE GENERATION DE LA PAGE
		$this->assign("generated_time",$BM->elapsed_time('loading_time_base_classes_start','total_execution_time_end')); 
		// ASSIGNATION DU NOMBRE DE REQUETES
		$this->assign("number_request",$DB->get_statistiques()); 
		$this->assign("detail_request",$DB->get_detail_stat()); 		
		// ASSIGNATION DES ERREURS PHP OU AUTRE
		$this->assign("PHP_ERROR",$PHP_ERROR); 	
		// ASSIGNATION DES ERREURS|INFORMATIONS
		$this->assign("error",$ERROR->displayError()); 
		
		$this->assign("version_package",VERSION_PACKAGE); 
		
		$this->assign("common_image",SYS_FOLD.'/common_style');	
		
		// ASSIGNATION DES CSS SUPPLEMENTAIRES
		$tab_css = "";
		foreach ($this->CSS as $item)
		{
			$tab_css .= '<link rel="stylesheet" type="text/css" href="'.$this->adresseCSS.'/'.$item.'" />';
		}
		$this->assign("extra_css",$tab_css);
		//ASSIGNATION DES JS
		$tab_js = "";
		foreach ($this->JS as $item)
		{
			$tab_js .= '<script type="text/javascript" language="javascript" src="'.$this->adresseJS.'/'.$item.'"></script>';
		}
		$this->assign("extra_js",$tab_js);
		$this->display($tpl,$reference_cache);
		/*
		if ($this->template_exists($tpl))
			$this->display($tpl);
		else
			$this->display("../common/".$tpl);
		*/
		
	}
	
	function fetch_($tpl)
	{

		$this->assign("version_package",VERSION_PACKAGE); 
		
		$this->assign("common_image",SYS_FOLD.'/common_style');	
		
		// ASSIGNATION DES CSS SUPPLEMENTAIRES
		$tab_css = "";
		foreach ($this->CSS as $item)
		{
			$tab_css .= '<link rel="stylesheet" type="text/css" href="'.$this->adresseCSS.'/'.$item.'" />';
		}
		$this->assign("extra_css",$tab_css);
		//ASSIGNATION DES JS
		$tab_js = "";
		foreach ($this->JS as $item)
		{
			$tab_js .= '<script type="text/javascript" language="javascript" src="'.$this->adresseJS.'/'.$item.'"></script>';
		}
		$this->assign("extra_js",$tab_js);
		
		return $this->fetch($tpl);
		
	}
	
	
		
	function addExtraCss($css_filename)
	{
		$this->CSS[count($this->CSS)+1] = $css_filename;
	}
	function addExtraJs($js_filename)
	{
		$this->JS[count($this->JS)+1] = $js_filename;
	}

					
}

	function smarty_block_dynamic($param, $content, &$smarty) {
		return $content;
	}	
	

	
	
	


?>