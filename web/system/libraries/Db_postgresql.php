<?
/**
*
* @package Plugmedia
* @copyright (c) 2009 Christophe Lemoine
* @license http://creativecommons.org/licenses/by-nc-nd/2.0/be/  Attribution-Noncommercial-No Derivative Works 2.0 Belgium
* QNAP Systems, Inc is autorize to distribute and transmit the work
*
* Minimum Requirement: PHP 5
*/

class CORE_Db_postgresql
{

	var $query_result;
	var $row = array();
	var $rowset = array();
	var $objet_erreur;
	var $val=0;
	var $historique_bd = array();
	var $config = array();
	var $error;
	
	var $connexion;
	
	// CONSTRUCTEUR
	function CORE_Db_postgresql()
	{
		$config_cl = load_class('Config');
		$config_cl->load('database.php');
		$this->config= $config_cl->item('database');

	}
	
	//-----------------------------------------------------------------------------------------------------------------------
	// Connexion a la Base de donnée
	//-------------------------------

	function connexionbd() 
	{
		if (!function_exists("pg_connect"))
		{
			$error_msg = "[DATABASE] It seems that php was not build with postgresql extension, please check postgresql QPKG";
			log_message('error', $error_msg);	
			$this->error = $error_msg;
			return FALSE;
			exit();
		}
		
		$dsn = array ( 'phptype' => 'pgsql',
			 'hostspec' => $this->config['host'],
			 'username' => $this->config['login'],
			 'password' => $this->config['pass'],
			 'database' => $this->config['database']
		);
		$options = array(
				'debug' => 2,
				'result_buffering' => false,
		);
		
		$this->connexion = MDB2::connect($dsn, $options);
		
		if (PEAR::isError($this->connexion)) {
			$error_msg = "[DATABASE] Plugmedia Database was not correctly installed or is missing, please check the qpkg or contact Plugmedia Website support"; 
			log_message('error', $error_msg );	
			$this->error = $error_msg;
			return FALSE;
			exit();
		}
		
		$this->connexion->setFetchMode(MDB2_FETCHMODE_ASSOC); 
		return true;
		
		
	}
	//-------------------------------------------------------------------------------------------------------------------------


	public function getError()
	{
		return $this->error;	
	}

	// Requête
	function query($query, $fontion)
	{
		static $var, $historique_bd;
		// Détruit toutes les requêtes précédentes
		unset($this->query_result);
		
		$this->query_result = $this->connexion->query($query);
		if (PEAR::isError($this->query_result)) {
			log_message('error', "SQL QUERY ERROR : ".$query." - ".$fontion." - ".$this->query_result->getMessage());
			return false;
		}		
		
		unset($this->row);
		unset($this->rowset);
		// Statistiques
		$this->val++;
		$taille = count($this->historique_bd);
		$this->historique_bd[$taille+1] = $query." (".$fontion.")";
		
		return true;
	}
	
	function prepare($query, $fontion, $types)
	{
		static $var, $historique_bd;
		// Détruit toutes les requêtes précédentes
		unset($this->query_result);
		$this->query_result = $this->connexion->prepare($query, $types, MDB2_PREPARE_MANIP);
		if (PEAR::isError($this->query_result)) {
			log_message('error', "SQL QUERY ERROR : ".$query." - ".$fontion." - ".$this->query_result->getMessage());
			return false;
		}		
		
		unset($this->row);
		unset($this->rowset);
		// Statistiques
		$this->val++;
		$taille = count($this->historique_bd);
		$this->historique_bd[$taille+1] = $query." (".$fontion.")";
		
		return true;		
		
	
		
	}
	
	function execute()
	{
		$result = $this->query_result->execute(func_get_args()); 
		if (PEAR::isError($result)) {
			log_message('error', "SQL QUERY ERROR : ".$query." - ".$fontion." - ".$this->query_result->getMessage());
			return false;
		}
		return $result;		 	
	}

	function begin_transaction()
	{
		$this->query("BEGIN", "Begin transaction");
	}
	function commit_transaction()
	{
		$this->query("COMMIT", "Commit transaction");
	}	
	function rollback_transaction()
	{
		$this->query("ROLLBACK", "Rollback transaction");
		log_message('debug', "Rollback transaction");	
	}
	//Fetch d'une requête qui ne renvoie qu'une seule ligne
	function fetchrow()
	{
		
		if (!PEAR::isError($this->query_result)) {
			while ($row2 = $this->query_result->fetchRow())
			{
				$last = $row2;
			}
			if (isset($last))
			{
				$this->row = $last;	
				return $this->row;
			}
			else
				return false;
		}
		else
			return false;
		
	}

	//Fetch d'une requête qui renvoie un tableau
 	function fetcharray()
	{
		if (!PEAR::isError($this->query_result)) {
			$result = array();
			while ($this->rowset = $this->query_result->fetchRow())
			{
				$result[] = $this->rowset;
			}	
			return $result;
		}
		else
			return false;
	}
	
	/*//retourne le nombre de lignes affectées par la requête
	function affectedrows()
	{
		$nbraffected = pg_affected_rows($this->query_result);
		return $nbraffected;
	}*/

	function close()
	{
		pg_close();
	}
	
	function getLastId()
	{
		$id = $this->connexion->lastInsertID(); 
		if (PEAR::isError($id))  
			return false;
		else
			return $id;
	}
	
	function protectString($string)
	{
		return $this->connexion->escape($string); 
	}
	
	/*
	function securiser($securevar)
	{
		if(get_magic_quotes_gpc()==0)
		{
			$securevar=mysql_real_escape_string($securevar);
		}
		return trim(htmlspecialchars($securevar));
	}
	
	function securisertext($securevar)
	{
		$securevar=strip_tags($securevar, '<i><b><center><font>');
		$securevar=nl2br($securevar);
		if(get_magic_quotes_gpc()==0)
		{
			$securevar=mysql_real_escape_string($securevar);
		}
		return $securevar;
	}
	
	function isNumeric ($number)
	{
		return is_numeric($number);
	}
	
	function protectText ($string)
	{
		if (!is_string ($string))
			return false;
		else
			return $this->securiser($string);
	}
	*/

	function get_statistiques()
	{
		static $val; 
		return $this->val;
	}
	function get_detail_stat()
	{
		static $historique_bd;
		return $this->historique_bd;
	}
	
		// input: array()
	// output: string ('item1','item2',...)
	function generateSqlString($array)
	{
		$string_rt = "(";
		foreach ($array as $item)
		{
			$string_rt .= "'".sqlite_escape_string($item)."',";
		}
		$string_rt = substr($string_rt,0,strlen($string_rt)-1);
		$string_rt .= ")";
		return $string_rt;
	}
	
}
?>