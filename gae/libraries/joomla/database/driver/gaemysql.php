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
class JDatabaseDriverGaemysql extends JDatabaseDriverMysql
{
	/**
	 * The name of the database driver.
	 *
	 * @var    string
	 * @since  12.1
	 */
	public $name = 'mysql';

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

		// Make sure the MySQL extension for PHP is installed and enabled.
		if (!function_exists('mysql_connect'))
		{
			throw new RuntimeException('Could not connect to MySQL.');
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

			$host = $s;
		}else{
			$host ="$h:$p";
		}

		// Attempt to connect to the server.
		if (!($this->connection = @ mysql_connect($host, $this->options['user'], $this->options['password'], true)))
		{
			throw new RuntimeException('Could not connect to MySQL.');
		}

		// Set sql_mode to non_strict mode
		mysql_query("SET @@SESSION.sql_mode = '';", $this->connection);

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
			mysql_query("SET profiling = 1;", $this->connection);
		}


	}


}
