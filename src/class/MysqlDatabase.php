<?php
require_once "Log.php";

class MysqlDatabase{
	private $_log;
	private $_config = "config/config.php";

	private $_logObject;
	private $_Mysqli;

	var $database;
	var $connection;

	var $result;
	var $upper;

	var $ftwhere;
	var $ftorderby;
	var $ftfrom;
	var $ftfieldlist;
	var $nrstartrows;
	var $nrendrows;

	// constructor
	function __construct(){
		$this->_logObject = new Log(date('Ymd') . "_database.log");

		/** for testing using phpunit we have to use the connectionstring directly  */
		/* localhost */
		if (!defined('NMSERVER')) define('NMSERVER', 'localhost');
		if (!defined('NMUSER')) define('NMUSER', 'root');
		if (!defined('NMPASSWORD')) define('NMPASSWORD', 'Marjilde');
		if (!defined('NMDATABASE')) define('NMDATABASE', 'museum');

		/* get username and password */
		if (file_exists($this->_config))
		{
			/** For now we do without the config file and add the connection string directly */
			//			include $this->_config;
		} else {
			$this->_logObject->writeLog("Config file " . $this->_config . " not available.\n");
		}

		/* connect to the database */
		$this->connection = mysqli_connect(NMSERVER, NMUSER, NMPASSWORD)
			or die("Kan geen verbinding maken met server \n" . mysqli_error($this->connection));
		return;

		/* change character set to utf8 as read in: https://www.toptal.com/php/a-utf-8-primer-for-php-and-mysql */
		if (!mysqli_set_charset($this->connection, "utf8")) {
			print_r("Error loading character set utf8: " . mysqli_error($this->connection) . "\n");
		}

		// select the database
		$this->database  = mysqli_select_db($this->connection, NMDATABASE)
			or die("Kan geen database selecteren\n");

	}

	function executeCommand($query){

		if (empty($query)) 
		{
			throw new InvalidArgumentException();
		} else {
			// exectute the query on the database and return the result
			$this->result = mysqli_query($this->connection, $query)
			or die("Fout " . mysqli_errno($this->connection) . ": "
			. mysqli_error($this->connection) . " bij uitvoeren query in querydb");
		}
		return "hetzelfde";
	}

	function getRecordById(String $database, String $table, Int $id)
	{
		if (empty($database) || empty($table) || empty($id) || $id < 1){
			throw new InvalidArgumentException();
		}

		$pk = $this->_getPrimaryKeyField($database, $table);

		if ($statement = $this->_Mysqli->prepare("SELECT * FROM $database.$table WHERE ?"))
		{


		}

	}

	private function _getPrimaryKeyField(String $database, String $table) : String
	{
		return "";
	}

}
?>