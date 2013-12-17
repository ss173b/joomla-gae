<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Session
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Memcached session storage handler for PHP tweaked in order to run under GAE
 *
 * @package     Joomla.Platform
 * @subpackage  Session
 * @since       11.1
 */
class JSessionStorageGaememcached extends JSessionStorageMemcached
{
	/**
	 * Constructor
	 *
	 * @param   array  $options  Optional parameters.
	 *
	 * @since   11.1
	 * @throws  RuntimeException
	 */
	public function __construct($options = array())
	{

		if (!self::isSupported())
		{
			throw new RuntimeException('GAE Memcached Extension is not available', 404);
		}

		// GAE Memcached doesn't use these configs
		$this->_servers = array(
			array(
				'host' => 'unused',
				'port' => 0
			)
		);
	}

	/**
	 * Test to see if the SessionHandler is available.
	 *
	 * @return boolean  True on success, false otherwise.
	 *
	 * @since   12.1
	 */
	static public function isSupported()
	{
		return (ini_get('session.save_handler') == 'user');
	}
}
