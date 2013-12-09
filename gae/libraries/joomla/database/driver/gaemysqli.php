<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Database
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * MySQLi database driver
 *
 * @package     Joomla.Platform
 * @subpackage  Database
 * @see         http://php.net/manual/en/book.mysqli.php
 * @since       12.1
 */
class JDatabaseDriverGaemysqli extends JDatabaseDriverMysqli
{
	/**
	 * The name of the database driver.
	 *
	 * @var    string
	 * @since  12.1
	 */
	public $name = 'mysqli';

	/**
	 * Connects to the database if needed.
	 *
	 * @return  void  Returns void if the database connected successfully.
	 *
	 * @since   12.1
	 * @throws  RuntimeException
	 */
	public function connect()
	{
		if ($this->connection)
		{
			return;
		}

		/**
		 *
		 * For Google Cloud SQL, we use sockets with odd names, for example: /google/mygaeapp-id:dbid
		 * While traditionally, host and port would be attached via a colon, ie localhost:3306
		 * This makes traditional logic FAIL, so instead we use a different format
		 * ipaddress|port|socket
		 *
		 *
		 */

		// Seperate out the host, port, and socket from the configuration
		$hostOptions = explode('|',$this->options['host']);
		list($h, $p, $s) = $hostOptions;

		if(isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'],'Google App Engine') !== false) {

			$host = null;
			$port = null;
			$socket = $s;
		}else{
			$host = $h;
			$port = $p;
			$socket = null;
		}
		// Make sure the MySQLi extension for PHP is installed and enabled.
		if (!function_exists('mysqli_connect'))
		{
			throw new RuntimeException('The MySQL adapter mysqli is not available');
		}

		$this->connection = @mysqli_connect(
			$host, $this->options['user'], $this->options['password'], null, $port, $socket
		);

		// Attempt to connect to the server.
		if (!$this->connection)
		{
			throw new RuntimeException('Could not connect to MySQL.');
		}

		// Set sql_mode to non_strict mode
		mysqli_query($this->connection, "SET @@SESSION.sql_mode = '';");

		// If auto-select is enabled select the given database.
		if ($this->options['select'] && !empty($this->options['database']))
		{
			$this->select($this->options['database']);
		}

		// Set charactersets (needed for MySQL 4.1.2+).
		$this->setUTF();

		// Turn MySQL profiling ON in debug mode:
		if ($this->debug && $this->hasProfiling())
		{
			mysqli_query($this->connection, "SET profiling_history_size = 100;");
			mysqli_query($this->connection, "SET profiling = 1;");
		}
	}


}
