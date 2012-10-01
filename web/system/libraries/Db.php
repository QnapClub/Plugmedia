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

class CORE_Db
{
	var $query_result;
	var $row = array();
	var $rowset = array();
	var $objet_erreur;
	var $val=0;
	var $historique_bd = array();
	var $config = array();
	//var $error;
	var $error_mysql;
	var $dbHandle;
	
	// CONSTRUCTEUR
	function CORE_Db()
	{
		$config_cl = load_class('Config');
		$config_cl->load('config.php');
		$this->config['database'] = $config_cl->item('sqli_database_name');
		
	}
	
	//-----------------------------------------------------------------------------------------------------------------------
	// Connexion a la Base de donnée
	//-------------------------------



	function connexionbd() 
	{
		//$ERROR = load_class('Error');
		try{
			$this->dbHandle = new PDO('sqlite:'.$this->config['database']);
		}catch( PDOException $exception ){
			log_message('error', "Database Error: ".$exception->getMessage());
			return FALSE;
		}
	}
	//-------------------------------------------------------------------------------------------------------------------------



	// Requête
	function query($query, $fontion="")
	{
		static $var, $historique_bd;
		// Détruit toutes les requêtes précédentes
		unset($this->query_result);

		if (! ($this->query_result = $this->dbHandle->query($query)))
		{
			$this->error_db = $this->dbHandle->errorInfo();
			log_message('error', "Query Error: ".$this->error_db[2]);
			return false;
		}

		
		// Statistiques
		$this->val++;
		$taille = count($this->historique_bd);
		$this->historique_bd[$taille+1] = $query." (".$fontion.")";
		
		return true;
	}

	function begin_transaction()
	{
		$this->dbHandle->beginTransaction();
		log_message('debug', "Database info: Begin Transaction");
	}
	function commit_transaction()
	{
		$this->dbHandle->commit();
		log_message('debug', "Database info: Commit Transaction");
	}	
	function rollback_transaction()
	{
		$this->dbHandle->rollBack();
		log_message('debug', "Database info: Rollback Transaction");
	}
	//Fetch d'une requête qui ne renvoie qu'une seule valeur
	function fetchrow()
	{
		/*$this->row = $this->query_result->fetchColumn();
		return $this->row;*/
		$arr = $this->fetcharray();
		if (is_array($arr) && array_key_exists(0,$arr))
			return $arr[0];
		else	
			return false;
	}

	//Fetch d'une requête qui renvoie un tableau
 	function fetcharray($mode='ASSOC')
	{
		 switch($mode) {
            case 'LAZY' :
            	$mode_ = PDO::FETCH_LAZY;
            break;
            case 'BOTH' :
            	$mode_ = PDO::FETCH_BOTH;
            break;
            case 'ASSOC' :
				$mode_ = PDO::FETCH_ASSOC;
            break;
            default :
            	$mode_ = PDO::FETCH_ASSOC;
			break;
         }
		$result=@$this->query_result->fetchAll($mode_);
		
		return $result;
	}
	
	function prepareQuery($query)
	{
		static $var, $historique_bd;
		unset($this->query_result);
		if (! ($this->query_result = $this->dbHandle->prepare($query)))
		{
			$this->error_mysql = $this->dbHandle->errorInfo();
			return false;
			exit();
		}
		unset($this->row);
		unset($this->rowset);
		$this->val++;
		$taille = count($this->historique_bd);
		$this->historique_bd[$taille+1] = $query." (".$fontion.")";
		return true;
	}
	function bindParameter($param, $value, $type)
	{
		$this->query_result->bindParam($param, $value, $type);
	}
	function execute()
	{
		$this->query_result->execute();
		$this->query_result->closeCursor();
	}	
	
	function affectedrows()
	{
		/*$nbraffected = $this->query_result->rowCount();*/
		$nbraffected = count($this->fetcharray());
		return $nbraffected;
	}

	
	function getLastId()
	{
		return $this->dbHandle->lastInsertId();
	}
	
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
	
	// renvoi le msg d'erreur
    function return_error() {
        return $this->dbHandle->errorInfo();
    }
	
	function getErrorMysql()
	{
		return $this->error_db; 
	}

	function overWriteConfigDatabase($name)
	{
		$this->config['database'] = $name;
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