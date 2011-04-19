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

class CORE_Error {

	var $_lasterror=array();	// garde en memoire l'erreur qui s'est passée	// de type: array[compteur] = array('numero erreur','niveau') (ID, LEVEL);
	
	//---------------------------------------------------------------------
	// Translation des messages d'erreurs
	//------------------------------------
	function translateError($errorkey, $params)
	{
		global $i18n; // LOADING GLOBAL INTERNATIONALISATION
		return $i18n->translate($errorkey, $params);
	}
	
	function displayError()
	{
		$valeur_retour = "";
		if ($this->_lasterror) 
		{
	
			$tab = $this->_lasterror;
			$size = count($tab);
			for ($i=1;$i<=$size;$i++)
			{
				// on parcour le tableau des erreurs
				switch ($tab[$i]['LEVEL'])
				{
					case 'Error': 
						$valeur_retour .= '<div id="message_1"><strong>'.$this->translateError($tab[$i]['ERRORKEY'],$tab[$i]['PARAMS'] )."</strong>"; 
						break;
					case 'Information': 
						$valeur_retour .= '<div id="message_2"><strong>'.$this->translateError($tab[$i]['ERRORKEY'],$tab[$i]['PARAMS'])."</strong>"; 
						break;
					default: 
						$valeur_retour .= '<div id="message_1"><strong>'.$this->translateError($tab[$i]['ERRORKEY'],$tab[$i]['PARAMS'])."</strong>"; 
						break;
				}
				$valeur_retour .=('<br /></div>'."\n");
				
			}
			return $valeur_retour;
		}
	}
	
	function displayErrorWhitoutFormatting()
	{
		$valeur_retour = "";
		if ($this->_lasterror) 
		{
	
			$tab = $this->_lasterror;
			$size = count($tab);
			for ($i=1;$i<=$size;$i++)
			{
				// on parcoure le tableau des erreurs
				$valeur_retour .= $this->translateError($tab[$i]['ERRORKEY'],$tab[$i]['PARAMS'] );
				$valeur_retour .=('<br />');
				
			}
			return $valeur_retour;
		}
	}

	
	function clear_error()
	{
		$this->_lasterror=array();
	}
	
	//---------------------------------------------------------------
	// Ajout des messages d'erreurs 
	//-------------------------------------
	function addError($erreur_key, $level)
	{
		$taille = count($this->_lasterror);
		$this->_lasterror[$taille+1]['ERRORKEY'] = $erreur_key;
		$this->_lasterror[$taille+1]['LEVEL'] = $level;		
		$this->_lasterror[$taille+1]['PARAMS'] = func_get_args();
		unset ($this->_lasterror[$taille+1]['PARAMS'][0]);
		unset ($this->_lasterror[$taille+1]['PARAMS'][1]);
	}
	//-------------------------------------------------------------------------------
	
	function getNotFormatedError($erreur_key, $param)
	{
		return $this->translateError($erreur_key, $param);
	}

	function clearErrors()
	{
		$this->_lasterror=array();
	}
	
}
?>