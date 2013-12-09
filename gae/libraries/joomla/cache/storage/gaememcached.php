<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Cache
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Memcached cache storage handler
 *
 * @package     Joomla.Platform
 * @subpackage  Cache
 * @see         http://php.net/manual/en/book.memcached.php
 * @since       12.1
 */
class JCacheStorageGaememcached extends JCacheStorageMemcached
{

	/**
	 * Return memcached connection object
	 *
	 * @return  object   memcached connection object
	 *
	 * @since   12.1
	 * @throws  RuntimeException
	 */
	protected function getConnection()
	{
		if (class_exists('Memcached') != true)
		{
			return false;
		}

		$config = JFactory::getConfig();
		$this->_persistent = $config->get('memcache_persist', true);
		$this->_compress = $config->get('memcache_compress', false) == false ? 0 : Memcached::OPT_COMPRESSION;

		/*
		 * GAE does not require any server settings
		 *
		 */

		// Create the memcache connection
		if ($this->_persistent)
		{
			$session = JFactory::getSession();
			self::$_db = new Memcached($session->getId());
		}
		else
		{
			self::$_db = new Memcached;
		}


		self::$_db->setOption(Memcached::OPT_COMPRESSION, $this->_compress);

		// Memcached has no list keys, we do our own accounting, initialise key index
		if (self::$_db->get($this->_hash . '-index') === false)
		{
			$empty = array();
			self::$_db->set($this->_hash . '-index', $empty, 0);
		}

		return $this;
	}



	/**
	 * Test to see if the cache storage is available.
	 *
	 * @return  boolean  True on success, false otherwise.
	 *
	 * @since   12.1
	 */
	public static function isSupported()
	{
		if ( class_exists('Memcached') != true)
		{
			return false;
		}


			return true;

	}

}
