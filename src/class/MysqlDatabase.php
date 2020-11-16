<?php
/* for testing purposes */
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'On');  //On or Off

class MysqlDatabase{
	var $_debug = true;
	var $_mysqli;

	var $_configFile;

	var $database;
	var $_connection;

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
		/* get username and password */
		if ($this->_configFile)
		{
			include $this->_configFile;
		} else {
			if (!defined('NMSERVER')) define('NMSERVER', 'localhost');
			if (!defined('NMUSER'))define('NMUSER', 'root');
			if (!defined('NMPASSWORD'))define('NMPASSWORD', 'Marjilde');
			if (!defined('NMDATABASE'))define('NMDATABASE', 'museum');
		}

		/* connect to the database */
		$this->_mysqli = new Mysqli(NMSERVER, NMUSER, NMPASSWORD);
//		print_r($this->_mysqli);
		$this->_connection = mysqli_connect(NMSERVER, NMUSER, NMPASSWORD)
			or die("Kan geen verbinding maken met server \n" . mysqli_error($this->_connection));

		/* change character set to utf8 as read in: https://www.toptal.com/php/a-utf-8-primer-for-php-and-mysql */
		if (!mysqli_set_charset($this->_connection, "utf8")) {
			print_r("Error loading character set utf8: " . mysqli_error($this->_connection) . "\n");
		} else {
//			print_r("Current character set: " . mysqli_character_set_name($this->connection) . "\n");
		}

		// select the database
		$this->database  = mysqli_select_db($this->_connection, NMDATABASE)
			or die("Kan geen database selecteren\n");
	}

	function executeCommand($query){
		// laat een query los op de database en toon de resultaten
		$this->result = mysqli_query($this->_connection, $query)
		or die("Fout " . mysqli_errno($this->_connection) . ": "
		. mysqli_error($this->_connection) . " bij uitvoeren query in querydb");
	}



	function queryDb_6($ftfieldlist, $ftfrom, $ftwhere, $ftorderby, $nrstartrows, $nrendrows){
		/* check the required values */

		/* if one of the parameters is null then we use the instance variables if they are  empty we skip them */
		if ($ftfieldlist 	=== null){$ftfieldlist = $this->ftfieldlist;}
		if ($ftfrom 		=== null){$ftfrom = $this->ftfrom;}
		if ($ftwhere	 	=== null){$ftwhere = $this->ftwhere;}
		if ($nrstartrows 	=== null){$nrstartrows = $this->nrstartrows;}
		if ($nrendrows 		=== null){$nrendrows = $this->nrendrows;}

		if (!isset($from) or $from ==""){
			//return;
		}

		if ($ftfieldlist !=""){
			$sql = "SELECT {$ftfieldlist} FROM {$ftfrom}";
		} else {
			$sql = "SELECT * FROM {$ftfrom}";
		}// endif

		if (isset($ftwhere) and $ftwhere !=""){
			$sql .= " WHERE {$ftwhere}";
		}
		if (isset($ftorderby) and $ftorderby !=""){
			$sql .= " ORDER BY {$ftorderby}";
		}
		if (isset($nrstartrows) and $nrstartrows !="" and isset($nrendrows) and $nrendrows !="" ){
			$sql .= "LIMIT $nrstartrows, $nrendrows";
		}

		// retrieve the data
		$data = $this->queryDb($sql);

		return $data;
	}//queryDb_6

	function queryDb($query){
		// laat een query los op de database en toon de resultaten
		$this->result = mysqli_query($this->_connection, $query)
			or die("Fout " . mysqli_errno($this->_connection) . ": "
			. mysqli_error($this->_connection) . " bij uitvoeren query in querydb");

		// if only one record strip it.
		if (mysqli_affected_rows($this->_connection) == 1){
				$this->result = mysqli_fetch_array($this->result, MYSQLI_ASSOC);
		$this->result = array(0=>$this->result);
		} else {
			// put all the variables in an array
			$x = 0;

			$output=array();
			while ($row = mysqli_fetch_array($this->result, MYSQLI_ASSOC)) {
				$output[$x] = $row;
				$x++;
			}//endwhile
			$this->result = $output;
		}
		return $this->result;
	}

	function deleteRecord($entname, $id){
		$sql= "DELETE FROM {$entname} WHERE id{$entname} = {$id}";
		$this->updatedb($sql);
	}

	function getData($select, $from, $where, $orderby, $limit){
		// check the required values
		if (!isset($from) or $from ==""){
			//return;
		}

		if ($select !=""){
			$sql = "SELECT {$select} FROM {$from}";
		} else {
			$sql = "SELECT *  FROM {$from}";
		}// endif

		if (isset($where) and $where !=""){
			$sql .= " WHERE {$where}";
		}
		if (isset($orderby) and $orderby !=""){
			$sql .= " ORDER BY {$orderby}";
		}
		if (isset($limit) and $limit !=""){
			$sql .= " LIMIT {$limit}";
		}

		// retrieve the data
		$data = $this->queryDb($sql);

		return $data;
	}//function GetData

	function updateDb($query){
		// deze functie voert een insert of update uit op de database
		$result = mysqli_query($this->_connection, $query)
		or die("Fout " . mysqli_errno($this->connection) . ": "
		. mysqli_error($this->_connection) . " bij uitvoeren update/insert in de database");
	}

	function insertRecord($query){
		// deze functie voert een insert of update uit op de database
		$result = mysqli_query($this->_connection, $query)
		or die("Fout " . mysqli_errno($this->_connection) . ": "
		. mysqli_error($this->_connection) . " bij uitvoeren update/insert in de database");

		return mysqli_insert_id($this->_connection);
	}

	function dropTable($table){
		echo "dropping table...<br>";
		$this->result = mysqli_query($this->_connection, "drop table {$table}")
		or die("Fout bij uitvoeren DROP TABLE commando");
	}

	function createTable($query){
		// laat een query los op de database en toon de resultaten
		$this->result = mysqli_query($this->_connection, $query)
		or die("Fout bij uitvoeren CREATE TABLE commando");
	}

	/**************************************************
	functions specific to creating the sql query string
	***************************************************/
	function createWhere($nmtable, $ftfieldlist, $ftvaluelist, $nmoperator){
		/* init */
		$query = "SELECT column_name, data_type, is_nullable, character_maximum_length FROM information_schema.columns WHERE table_name = '$nmtable'";
		$ftfieldproperties	= $this->queryDb($query);
		
		if (empty($nmoperator)){
			$nmoperator = "AND";
		}

		if (!is_array($ftfieldlist)){$ftfieldlist = array($ftfieldlist);}
		if (!is_array($ftvaluelist)){$ftvaluelist = array($ftvaluelist);}

		/* create the where clause */
		$ftwhere = "";

		/* process each field */
		foreach ($ftvaluelist as $ftvalue){
			$tempWhere = "";
			foreach ($ftfieldlist as $field){
				/* get the properties */
				foreach($ftfieldproperties as $property){

					if ($property['column_name'] == $field){
						$nmdatatype = $property['data_type'];
					}
				}

				/* process the value */
				if (is_numeric($ftvalue)){
					$tmpvalue = $ftvalue;

				} else {
					$tmpvalue = "'%". strtoupper($ftvalue) . "%'";
				}

				/* create part of the where string */
				switch ($nmdatatype){
					case "int":
					case "tinyint":
					case "year":
						$ftpart = "$field = $tmpvalue";
						break;

					case "date":
						$ftpart = "$field = $tmpvalue";
						break;

					case "text":
					case "longtext":
					case "varchar":
						$ftpart = "UCASE($field) LIKE $tmpvalue";
						break;
					default:
						print_r($nmdatatype . "</br>");
				}

				/* create the string */
				if (empty($tempWhere)){
					$tempWhere = $ftpart;
				} else {
					$tempWhere .= " OR $ftpart";
				}
			}
			if (empty($ftwhere)){
				$ftwhere = " (" . $tempWhere . ")";
			} else {
				$ftwhere .= " $nmoperator (" . $tempWhere . ")";
			}
		}
		$this->ftwhere = $ftwhere;
	}

	function createOrderBy($ftFieldList, $sortorder){
		/* create the orderby clause based on an array */

		$orderBy = "";
		if (!empty($ftFieldList)){
			for ($i = 0; $i < count($ftFieldList); $i++){
				if (empty($orderBy)){
					$orderBy = " `". $ftFieldList[$i] . "` $sortorder ";;
				} else {
					$orderBy .= ", `". $ftFieldList[$i] . "` $sortorder ";
				}
			}
		} else {
			$orderBy = "";
		}
		$this->ftorderby = $orderBy;
	}

	function stringToXml($ftvalue){
		/* replace the string values to an xml equivalent */
		/* do the ampersand first */
		$ftlist = array();
		$ftlist["&"] = "&amp;";
		$ftlist["'"] = "&apos;";
		$ftlist["\""] = "&quot;";
		$ftlist["<"] = "&lt;";
		$ftlist[">"] = "&gt;";

		foreach ($ftlist as $key => $value){
			$ftvalue = str_replace($key, $value, $ftvalue);
		}
		return $ftvalue;
	}

	function xmlToString($ftvalue){
		/* replace the xml equivalent values with the string value */
		/* do the ampersand last  */
		$ftlist = array();
		$ftlist["&apos;"] = "'";
		$ftlist["&quot;"] = "\"";
		$ftlist["&lt;"] = "<";
		$ftlist["&gt;"] = ">";

		$ftlist["&amp;"] = "&";

		foreach ($ftlist as $key => $value){
			$ftvalue = str_replace($key, $value, $ftvalue);
		}
		return $ftvalue;
	}

	function getRecordById(String $database, String $table, Int $id)
	{
		if ($this->_debug) 
		{
			print_r("start getRecordById");
		}

		if (empty($database) || empty($table) || empty($id) || $id < 1){
			throw new InvalidArgumentException();
		}
print_r("ddddddddd<br/>");
//		$pk = $this->_getPrimaryKeyField($database, $table);

		$district = "";
		if ($statement = $this->_mysqli->prepare("SELECT * FROM museum.clubs WHERE idclub = ?"))
		{
			print_r($statement);
			$statement->bind_param("s", array("idclub" => 1));
			$statement->execute();

			$statement->bind_result($district);

			$statement->fetch();
		}
		return $district;	
	}

	private function _getPrimaryKeyField(String $database, String $table) : String
	{
		if ($this->_debug) print_r("start _getPrimaryKeyField");

		$sql = "SELECT k.column_name ";
		$sql .= "FROM information_schema.table_constraints t ";
		$sql .= "JOIN information_schema.key_column_usage k ";
		$sql .= "USING(constraint_name,table_schema,table_name) ";
		$sql .= "WHERE t.constraint_type='PRIMARY KEY' ";
		$sql .= "  AND t.table_schema = ? ";
		$sql .= "  AND t.table_name= ?";

		if ($statement = $this->_mysqli->prepare($sql))
		{
			$statement->bind_param("s", array("table_schema" => "{$database}", "table_name" => "{$table}"));
			$statement->execute();

			$statement->bind_result($row);

			$statement->fetch();
		}

		return $row;
	}


	function getDatabase() : string{
		return NMDATABASE;
	}

	/******************
	getters and setters
	*******************/
	function setWhere($ftwhere){
		$this->ftwhere = $ftwhere;
	}
	function getWhere(){
		return $this->ftwhere;
	}
	function setOrderby($ftorderby){
		$this->ftorderby = $ftorderby;
	}
	function setFieldlist($ftfieldlist){
		$this->ftfieldlist = $ftfieldlist;
	}
	function setFrom($ftfrom){
		$this->ftfrom = $ftfrom;
	}
	function setStartLimit($nrlimit){
		$this->nrstartlimit = $nrlimit;
	}
	function setEndLimit($nrlimit){
		$this->nrendlimit = $nrlimit;
	}

	function getDatabaseName(){
		return constant("NMDATABASE");
	}
	function getConnection(){
		/* for use of the mysqli_real_escape_string */
		return $this->_connection;
	}

	function getDatabases() : array
	{
		$sql = "SELECT DISTINCT(TABLE_SCHEMA) 
			FROM information_schema.columns 
			GROUP BY TABLE_SCHEMA";
		return $this->queryDb($sql);
	}

	function getTables(string $database) : array
	{
		$sql = "SELECT DISTINCT(TABLE_NAME) 
			FROM information_schema.columns 
			WHERE table_schema = '{$database}' 
			GROUP BY TABLE_NAME";
		return $this->queryDb($sql);
	}

	function getColumns(string $database, string $table) : array
	{
		$sql = "SELECT column_name, data_type, is_nullable, character_maximum_length 
			FROM information_schema.columns 
			WHERE table_schema = '{$database}' AND table_name = '{$table}'";
		return $this->queryDb($sql);
	}
}
?>