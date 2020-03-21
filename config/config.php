<?php
/* Database credentials. Assuming you are running MySQL
server with default setting (user 'root' with no password) */
switch ($_SERVER['SERVER_NAME']){
	case "localhost":
		/* localhost */
		if (!defined('NMSERVER')) define('NMSERVER', 'localhost');
		if (!defined('NMUSER'))define('NMUSER', 'root');
		if (!defined('NMPASSWORD'))define('NMPASSWORD', 'somePassword');
		if (!defined('NMDATABASE'))define('NMDATABASE', 'someDatabase');
		break;

?>