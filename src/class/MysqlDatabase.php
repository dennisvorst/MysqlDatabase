<?php
/* for testing purposes */
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'On');  //On or Off

class MysqlDatabase{
	var $_debug = true;

	var $_mysqli;
	var $_connection;
	var $_config;
	var $_log;

	var $_statement;
	var $_rows;
	var $_types;
	var $_values;

	var $database;

	// constructor
	function __construct(MysqlConfig $config, Log $log){
		$this->_config = $config;
		$this->_log = $log;

		/* get username and password */
		if ($this->_config)
		{
			$dbName = $this->_config->getDatabase();
			$userName = $this->_config->getUser();
			$serverName = $this->_config->getServer();
			$password = $this->_config->getPassword();
		}

		/** enable error reporting for mysqli before attempting to make a connection */
		mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

		/** connect to the database server */
		$this->_mysqli = new Mysqli($serverName, $userName, $password);

		/** connect to the database */
		$this->connect($dbName);
	}

	/** set the character set to be used in the database */
	function setCharSet(string $charSet) : bool
	{
		if (empty($charSet)) 
		{
			throw new Exception("An error occured.");
		}
		if ($this->mysqli->set_charset($charSet))
		{
			$this->_log->write("Character set {$charSet} loaded succesfully.");
		} else {
			$this->_log->write("Error loading character set {$charSet}: " . $this->mysqli->error($this->_connection) . "\n");
		}
	}

	/** connect to a database */
	function connect(string $dbName)
	{
		/* connect to the database */
		$this->_connection = $this->_mysqli->select_db($dbName)
		or die("Kan geen database selecteren\n");
	}

	/** sql injection save select method */
	function select(string $sql, string $types, array $values)
	{
		$this->_statement = $this->_mysqli->stmt_init();
		if ($this->_statement->prepare($sql)) {

			/** ... is the unpacking operator https://www.php.net/manual/en/migration56.new-features.php */
			if (!empty($types))
			{
				$this->_statement->bind_param($types, ...$values);
			}
		
			/* execute query */
			if (!$this->_statement->execute()) {
				trigger_error('Error executing MySQL query: ' . $statement->error);
			}

			$result = $this->_statement->get_result();

			/* fetch value */
			$rows = [];
			while ($row = $result->fetch_assoc()) {
				$rows[] = $row;
			}
		
			/* close statement */
			if (!$this->_statement->close())
			{
				trigger_error('Error closing the statement: ' . $statement->error);
			}
			return $rows;
		}
	}

	/** these functions should be called one after another when inserting complex records */
	function prepare(string $sql) : bool
	{
		/** init */
		$this->_types = "";
		$this->_values = [];
		$this->_rows = [];

		$this->_statement = $this->_mysqli->stmt_init();
		if (!$this->_statement->prepare($sql))
		{
			return false;
		}
		return true;
	}

	function bind($type, $value)
	{
		$this->_types .= $type;
		$this->_values[] = $value;
	}

	/** then call either the execute or the getrows */
	function execute() : bool
	{
		/** init  */

		if (!empty($this->_types))
		{
	//		print_r("Assessing types");
			$this->_statement->bind_param($this->_types, ...$this->_values);
		}
		/* execute query */
		if (!$this->_statement->execute()) {
			return false;
			//trigger_error('Error executing MySQL query: ' . $statement->error);
		}

		/** if the result is not a boolean then create the rows */
		$result = $this->_statement->get_result();
		if (!is_bool($result))
		{
			while ($row = $result->fetch_assoc()) {
//				print_r($row);
				$this->_rows[] = $row;
			}
		}

		/* close statement */
		if (!$this->_statement->close())
		{
			return false;
//			trigger_error('Error closing the statement: ' . $statement->error);
		}
		return true;
	}

	function getrows(): array
	{
		return $this->_rows;
	}

	/** create a database */
	function createDatabase(string $database) : bool 
	{
		if (empty($database)) 
		{
			throw new exception ("Database name is mandatory");
		}

		if(!$this->databaseExists($database))
		{
			$sql="CREATE DATABASE {$database}";
			if ($result = $this->_mysqli->query($sql))
			{
				return $result;
			}
		}
		return false;
	}

	function dropDatabase(string $database) : bool 
	{
		if (empty($database)) 
		{
			throw new exception ("Database name is mandatory");
		}

		if($this->databaseExists($database))
		{
			$sql="DROP DATABASE {$database}";
			if ($result = $this->_mysqli->query($sql))
			{
				return $result;
			}
		}
		return false;
	}

	function databaseExists(string $database) : bool
	{
		if (empty($database)) 
		{
			throw new exception ("Database name is mandatory");
		}

		/** no need to redirect to the information schema database because the name of the database is in the query.*/
		$sql="SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '{$database}'";

		if ($result = $this->_mysqli->query($sql))
		{
			if ($obj = $result->fetch_object())
			{
				if ($database == $obj->SCHEMA_NAME)
				{
					return true;
				}
			}
			$result->close();
		}
		return false;
	}

	/** creates a tables */
	function createTable(string $database, string $table, string $sql) : bool
	{
		if (empty($sql)) 
		{
			throw new exception("Sql is mandatory");
		}

//		print_r($sql);

		if(!$this->tableExists($database, $table))
		{
			if($this->_mysqli->query($sql)){  
				return true;
			}
		}
		return false;
	}

	function dropTable(string $database, string $table) : bool
	{
		if (empty($database) || empty($table)) 
		{
			throw new exception("Database and tablenames are mandatory.");
		}

		if($this->tableExists($database, $table))
		{
			$sql="DROP TABLE {$database}.{$table}";
			if ($result = $this->_mysqli->query($sql))
			{
				return $result;
			}
		}
		return false;
	}

	private function tableExists(string $database, string $table) : bool
	{
		if (empty($database) || empty($table)) 
		{
			throw new exception("Database and tablenames are mandatory.");
		}
		$sql = "SELECT * FROM information_schema.tables	WHERE table_schema = '{$database}' AND table_name = '{$table}' LIMIT 1";
//		print_r($sql);

		if ($result = $this->_mysqli->query($sql))
		{
			if ($obj = $result->fetch_object())
			{
//				print_r($obj);
				if ($table == $obj->TABLE_NAME && $database == $obj->TABLE_SCHEMA)
				{
//					print_r("true");
					return true;
				}
			}
			$result->close();
		}
//		print_r("false");
		return false;
	}
}
?>