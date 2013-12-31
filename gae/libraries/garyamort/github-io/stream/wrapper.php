<?php
/**
 * Created by PhpStorm.
 * User: gmort
 * Date: 12/30/13
 * Time: 12:38 PM
 */

namespace garyamort\github_io\stream;


abstract class wrapper
{

	public $streamHandle = false;
	public $dirHandle = false;
	public $context;
	public $streamProtocols = array();

	static public $types = array('ggsw');


	/*
	 * Methods not tied to stream methods
	 */

	static public function addType($type)
	{
		static::$types[] = $type;
	}


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

	public function matchType(\garyamort\github_io\stream\path $path, $mode)
	{
		foreach(static::$types as $type)
		{
			$streamType = $type.':';
			if (strpos($path->truePath, $streamType) === 0)
			{
				return true;
			}
		}
		return false;
	}


	/*
	 * These class methods do not map to any stream operations
	 */
	public function __construct ()
	{

		// When a stream handle is created, capture the current stream protocols assigned to this stream
		foreach(static::$types as $type)
		{
			$this->streamProtocols[] = $type;
		}
	}

	public function __destruct ()
	{

		// When a stream handle is destroyed, destroy it's underlying handle as well
		if (isset($this->streamHandle))
		{
			unset($this->streamHandle);
		}
		if (isset($this->dirHandle))
		{
			unset($this->dirHandle);
		}
	}


	public function initializeStream()
	{

		// initialize stream handles
		$this->streamHandle = false;
		$this->dirHandle = false;
		return false;
	}

	// Unknown methods....not sure what they are used for

	public function stream_cast ($cast_as )
	{
		// When a stream is cast, return the underlying stream handle.. does this even do anything?
		return $this->streamHandle;
	}



	/*
	 * Complex Stream operations
	 * These operations tend to be involved and complex in order to get them to work correctly
	 */

	// Rename logic is complex and stream wrapper specific
	public function rename (  $path_from, $path_to )
	{
		return $this->initializeStream();
	}


	public function stream_open($pathname, $mode, $options, &$opened_path)
	{

		return $this->initializeStream();
	}

	function dir_opendir ( $pathname, $options )
	{
		return $this->initializeStream();
	}

	/*
	 * Moderately complex stream operations
	 * These operations aren't dead simple, but they don't take a lot of brain power
	 */
	function stream_close()
	{
		if (is_resource($this->streamHandle))
		{
			$this->streamHandle = fclose($this->streamHandle);
		} else{
			$this->streamHandle = false;
		}
		return $this->streamHandle;
	}



	public function stream_set_option ( $option , $arg1 , $arg2 )
	{
		$return = false;
		if ($option & STREAM_OPTION_BLOCKING)
		{
			$return = stream_set_blocking($arg1, $arg2);
		}


		if ($option & STREAM_OPTION_READ_TIMEOUT)
		{
			$return = stream_set_timeout($arg1, $arg2);
		}


		if ($option & STREAM_OPTION_WRITE_BUFFER)
		{
			$return = stream_set_write_buffer($arg1, $arg2);
		}

		return $return;

	}


	public function stream_metadata( $pathname, $option, $value)
	{
		return $this->initializeStream();
	}



	public function unlink (  $pathname )
	{
		return $this->initializeStream();
	}


	public function rmdir (  $pathname, $options )
	{
		return $this->initializeStream();
	}

	public function mkdir ( $pathname , $mode , $options )
	{
		return $this->initializeStream();
	}

	public function stream_urlstat($pathname, $flags)
	{
		$this->initializeStream();
		// Stating returns an array instead of a boolean
		return array();
	}

	/*
	 * Simle stream wrapping methods
	 * These methods generally can be mapped directly to the function which called them and simply use the internal handle pointer instead of the one initially called
	 */



	function dir_closedir ( )
	{
		if ($this->dirHandle)
			 closedir($this->dirHandle);
		else
			 closedir();
	}

	function dir_readdir ( )
	{
		if ($this->dirHandle)
			return readdir($this->dirHandle);
		else
			return readdir();
	}

	function dir_rewinddir ( )
	{
		if ($this->dirHandle)
			 rewinddir($this->dirHandle);
		else
			 rewinddir();
	}

	function stream_seek ($offset ,$whence = SEEK_SET )
	{
		return fseek($this->streamHandle, $offset, $whence);
	}


	function stream_write($data)
	{
		return fwrite($this->streamHandle, $data);
	}

	function stream_tell()
	{
		return ftell($this->streamHandle);
	}

	function stream_eof()
	{
		return feof($this->streamHandle);
	}


	function stream_lock( $operation)
	{
		return flock($this->streamHandle, $operation);
	}

	function stream_stat()
	{
		return fstat($this->streamHandle);
	}

	function stream_truncate($new_size)
	{
		return ftruncate($this->streamHandle, $new_size);
	}

	function stream_flush()
	{
		return fflush($this->streamHandle);
	}

	function stream_read($count)
	{
		return fread($this->streamHandle, $count);
	}


}



