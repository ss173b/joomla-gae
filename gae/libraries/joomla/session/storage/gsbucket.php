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
 * Google Storage Bucket session storage handler for PHP
 *
 * @package     Joomla.Platform
 * @subpackage  Session
 * @since       11.1
 */
class JSessionStorageGsbucket extends JSessionStorage
{


	/**
	 * @var    datetime  Now
	 * @since  11.1
	 */
	public $now;

	/**
	 * @var    integer  Caching lifetime for intermediate session caches
	 * @since  11.1
	 */
	public $lifetime;


	/**
	 * Stream identifier
	 *
	 * @var    string
	 *
	 */
	protected $streamId = 'gs://';

	/**
	 * Bucket Name
	 *
	 * @var    string
	 *
	 */
	protected $bucketName;

	/**
	 * Object Name Prefix
	 *
	 * @var    string
	 *
	 */
	protected $objectPrefix;

	/**
	 * Stream Path
	 *
	 * @var    string
	 *
	 */
	protected $streamPath;


	/**
	 * Google Storage Stream Context for files
	 *
	 * @var    string
	 *
	 */
	protected $filectx;

	/**
	 * Google Storage Stream Context for directories
	 *
	 * @var    string
	 *
	 */
	protected $dirctx;

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
			throw new RuntimeException('Google Cloud Storage is not available', 404);
		}

		parent::__construct($options);

		// If configuration is not passed as an option, get the default application config
		$config = (isset($options['config'])) ? $options['config'] : JFactory::getConfig();

		$this->lifetime = (isset($options['lifetime'])) ? $options['lifetime'] * 60 : $config->get('session_gsbucket_cachetime') * 60;
		$this->now = (isset($options['now'])) ? $options['now'] : time();

		// If the lifetime is not set, default to 600 (0 is BAD)
		if (empty($this->lifetime))
		{
			$this->lifetime = 600;
		}


		$metadata = ['now' => $this->now, 'lifetime' => $this->lifetime, 'expires' => ($this->now + $this->lifetime)];
		//$cacheControl = ['max-age=' => $this->_lifetime ];
		$cacheControl = 'no-cache';
		$options = [ "gs" => [ "Content-Type" => "application/json",
			"Cache-Control" => $cacheControl,
			"enable_cache" => true,
			"enable_optimistic_cache" => true,
			"read_cache_expiry_seconds" => $this->lifetime,
			"metadata"=> $metadata
		]];
		$this->filectx = stream_context_create($options);

		$options = [ "gs" => [
			"enable_cache" => true,
			"enable_optimistic_cache" => true,
			"read_cache_expiry_seconds" => $this->lifetime,
		]];
		$this->dirctx = stream_context_create($options);

		// If configuration is not passed as an option, get the default application config
		$config = (isset($options['config'])) ? $options['config'] : JFactory::getConfig();

		$this->bucketName = (isset($options['bucket_name'])) ? $options['bucket_name'] : $config->get('session_gsbucket_name', '');
		$this->objectPrefix = (isset($options['object_prefix'])) ? $options['object_prefix'] : $config->get('session_gsobject_prefix', 'session');

		$this->streamPath = $this->streamId . $this->bucketName . '/' . $this->objectPrefix;

		// If the folder doesn't exist try to create it
		if (!is_dir($this->streamPath ))
		{

			//echo "creating {$this->_root }<br/>";
			mkdir($this->streamPath , null , true, $this->dirctx);

		}


	}


	/**
	 * Test to see if the session save handler is available.
	 *
	 * @return  boolean  True on success, false otherwise.
	 *
	 * @since   12.1
	 */
	public static function isSupported()
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
				return true;
			}
		}

		return false;
	}

	/**
	 * Buckets can garbage collect themselves for really old sessions
	 *
	 * @param   integer  $maxlifetime  The maximum age of a session.
	 *
	 * @return  boolean  True on success, false otherwise.
	 *
	 * @since   11.1
	 */
	public function gc($maxlifetime = null)
	{
		return true;
	}
}
