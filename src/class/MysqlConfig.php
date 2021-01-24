<?php

class MysqlConfig
{
	private $_mysqli;
	private $_server;
	private $_user;
	private $_password;
	private $_database;

	function __construct(string $database = "test", string $server = 'localhost', string $user = 'root', string $password = null)
	{
		$this->setUser($user);
		$this->setServer($server);
		$this->setDatabase($database);

		if (!empty($password))
		{
			$this->setPassword($password);
		}

		$this->_mysqli = new Mysqli($server, $user, $password);

	}

	function getObject() : mysqli
	{
		return $this->_mysqli;
	}

	/** Password */
	function getPassword()
	{
		return $this->_password;

	}
	function setPassword($password)
	{
		$this->_password = $password;
	}

	/** Server */
	function getServer() : string
	{
		return $this->_server;
	}
	function setServer(string $server)
	{
		$this->_server = $server;
	}
	/** User */
	function getUser() : string
	{
		return $this->_user;
	}
	function setUser(string $user)
	{
		$this->_user = $user;
	}

	/** Database */
	function getDatabase() : string
	{
		return $this->_database;
	}
	function setDatabase(string $database)
	{
		$this->_database = $database;
	}
}
?>