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
		parent::__construct($options);

		$config = JFactory::getConfig();

		// GAE Memcached doesn't use these configs
		$this->_servers = array(
			array(
				'host' => 'unused',
				'port' => 0
			)
		);
	}

	/**
	 * Register the functions of this class with PHP's session handler
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	public function register()
	{
		ini_set('session.save_handler', 'memcached');
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
		/**
		 *  Google App Engine Server Software is either
		 * Development or
		 * Google App Engine
		 */
		if ( isset($_SERVER['SERVER_SOFTWARE']) )
		{
			$beginsWith = substr($_SERVER['SERVER_SOFTWARE'],0,11);
			if ( ($beginsWith == 'Development') ||
				( $beginsWith == 'Google App '))
			{
				// Gaememcached is supported if the Memcached class exists
				return class_exists('Memcached');
			}
		}
		return false;

	}
}
