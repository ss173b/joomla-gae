<?php
/**
 * Created by PhpStorm.
 * User: gmort
 * Date: 12/30/13
 * Time: 12:38 PM
 */

namespace garyamort\github_io\stream;

/**
 * Class syslog
 * @package garyamort\github_io\stream
 *
 * Extremely stripped down stream wrapper class.  Does not extend wrapper because we only support a limited number of calls, open, close, write, and of course registerWrapper for self declaration
 */
class syslog
{


	public $path = null;

	private static $valid_write_modes = ['w', 'wb', 'wt', 'a'];


	static public $types = array('ggslog');

	static public $priority = LOG_NOTICE;


	static public function registerWrapper($types = false, $force = false)
	{
		if (!$types)
		{
			$types = static::$types;
		}

		$streamProtocols = stream_get_wrappers();

		foreach ($types as $type)
		{
			if (in_array($type, $streamProtocols))
			{
				// If force is set, unregister existing wraooer
				if ($force)
				{
					//todo: error checking and message set
					stream_wrapper_unregister($type);
				} else
				{
					// otherwise skip stream processing
					continue;
				}
			}
			$streamWrapperClass = get_called_class();

			stream_wrapper_register($type, $streamWrapperClass)
			or die("Failed to register protocol $type using $streamWrapperClass");
		}
	}


	/*
	 * Unknown stream methods
	 */
	public function __construct()
	{
		// At this point, the only thing set is $this->context
		// Thus $context can be used during construct
	}




	/*
	 * Complex Stream operations
	 */

	public function stream_open($pathname, $mode, $options, &$opened_path)
	{

		if (in_array($mode, self::$valid_write_modes)) {

		$this->path = $pathname;
		return \syslog(LOG_INFO, "Opened $pathname for logging");
		}

		if (($options & STREAM_REPORT_ERRORS) != 0) {
			trigger_error(sprintf("Invalid mode: %s", $mode), E_USER_ERROR);
		}

		return false;
	}

	public function stream_close()
	{
		$pathname = $this->path;

		return \syslog(LOG_INFO, "Closed $pathname for logging");
	}
	/*
	 * Simle stream wrapping methods
	 */

	public function stream_write($data)
	{
		$return = true;
		$msgs = explode("\n", $data);
		foreach ($msgs as $m)
		{
			$ret = \syslog(static::$priority, $m);

			if (!$ret)
			{
				$return = $ret;
			}
		}

		return strlen($data);
	}

	function stream_flush()
	{
	}

	function stream_eof()
	{
		return true;
	}
}



