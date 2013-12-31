<?php
/**
 * Created by PhpStorm.
 * User: gmort
 * Date: 12/30/13
 * Time: 12:38 PM
 */

namespace garyamort\github_io\stream;


class morpher extends wrapper
{

	public $streamHandle = null;
	public $dirHandle = null;

	/*
	 * @var mapper/rule[]
	 */
	static public $rules = array();

	static public $types = array('ggsm');


	/*
	 * Methods not tied to stream methods
	 */

	static public function addRule(mapper\rule $rule)
	{
		static::$rules[] = $rule;
	}

	public function applyRules(\garyamort\github_io\stream\path $path, $mode)
	{
		foreach (static::$rules as $rule) {
			/* @var $rule mapper\rule */
			$path = $rule->apply($path, $mode);
		}

		return $path;
	}


	public function processPathname($pathname, $mode)
	{

		$this->path = false;
		$path = new \garyamort\github_io\stream\path($pathname);

		if ($this->matchType($path, $mode)) {
			$path = $this->applyRules($path, $mode);
		}
		$this->path = $path;

		return true;
	}

	public function checkRecursiveRules($path, $flags)
	{
		if ($this->matchType($path, false)) {
			if ($flags & STREAM_URL_STAT_QUIET) {
				trigger_error("Stream {$path->originalPath} morphs to {$path->truePath}, recursive path match in  " . __CLASS__, E_USER_ERROR);
			}
			return true;
		}

		return false;

	}

	/*
	 * Unknown stream methods
	 */
	public function __construct()
	{
		// At this point, the only thing set is $this->context
		// Thus $context can be used during construct
	}



	// Can't imagine this ever working
	// Probably should limit this to matching path types?
	public function rename($path_from, $path_to)
	{

		$pathFrom = $this->applyRules($path_from, 'w+');
		$pathTo = $this->applyRules($path_to, 'w');

		// Recursive streams won't work
		if ($this->matchType($pathFrom->truePath, 'w+')) {
			return false;
		}

		// Recursive streams won't work
		if ($this->matchType($pathTo->truePath, 'w+')) {
			return false;
		}

		// Can't copy over self!
		if ($pathTo->truePath === $pathFrom->truePath) {
			return false;
		}

		list($toStreamType) = explode($pathTo->truePath, ':');
		list($fromStreamType) = explode($pathFrom->truePath, ':');

		// Simple optimization, streams are of the same type
		if ($toStreamType === $fromStreamType) {
			return rename($pathFrom->truePath, $pathTo->truePath);
		}

		//TODO: Implement logic to allow for rename emulation using copy then delete
		return false;
		$sourceData = file_get_contents($path_from);
		$sourceLength = strlen($sourceData);

		if ($sourceLength == 0) {
			return false;
		}


		$outputStream = fopen($path_to, 'w');

		if (!$outputStream) {
			return false;
		}

		$bytesCopied = fwrite($outputStream, $sourceData);
		fclose($outputStream);

		if ($bytesCopied !== $sourceLength) {
			unlink($path_to);
			return false;
		}

		unlink($path_from);
		return true;
	}

	/*
	 * Complex Stream operations
	 */

	public function stream_open($pathname, $mode, $options, &$opened_path)
	{

		// initialize variables on open
		$return = parent::stream_open($pathname, $mode, $options, $opened_path);


		if ($this->processPathname($pathname, $mode)) {
			if (!$this->checkRecursiveRules($this->path, STREAM_URL_STAT_QUIET)) {

				$use_include_path = $options & STREAM_USE_PATH;

				$handle = fopen($this->path->truePath, $mode, $use_include_path);
				if (is_resource($handle)) {
					$this->streamHandle = $handle;
					$return = true;
				}
			}
		}
		return $return;
	}

	public function dir_opendir($pathname, $options)
	{

		$return = parent::dir_opendir($pathname, $options);
		if ($this->processPathname($pathname, null)) {
			if (!$this->checkRecursiveRules($this->path, STREAM_URL_STAT_QUIET)) {
				$handle = opendir($this->path->truePath, $this->context);
				if (is_resource($handle)) {
					// Both handles being set here so directory and stream function identically
					$this->dirHandle = $handle;
					$this->streamHandle &= $this->dirHandle;
					$return = true;
				}
			}

		}
		return $return;
	}


	/*
	 * Moderately complex stream operations
	 */


	public function stream_metadata($pathname, $option, $value)
	{

		$return = parent::stream_metadata($pathname, $option, $value);
		if ($this->processPathname($pathname, null)) {
			if (!$this->checkRecursiveRules($this->path, 0)) {


				if ($option & STREAM_META_TOUCH) {
					if (is_array($value)) {
						if (count($value) >= 2) {
							$atime = $value[1];
							$time = $value[0];
							$return = touch($this->path->truePath, $time, $atime);
						}

						if (count($value) == 1) {
							$time = $value[0];
							$return = touch($this->path->truePath, $time);
						}
					}


					$return = touch($this->path->truePath);
				}


				if ($option & STREAM_META_OWNER_NAME) {
					$return = chown($this->path->truePath, $value);
				}


				if ($option & STREAM_META_OWNER) {
					$return = chown($this->path->truePath, $value);
				}


				if ($option & STREAM_META_GROUP_NAME) {
					$return = chgrp($this->path->truePath, $value);
				}


				if ($option & STREAM_META_GROUP) {
					$return = chgrp($this->path->truePath, $value);
				}


				if ($option & STREAM_META_ACCESS) {
					$return = chmod($this->path->truePath, $value);
				}
			}
		}
		return $return;
	}


	public function unlink($pathname)
	{
		$return = parent::unlink($pathname);
		if ($this->processPathname($pathname, null)) {
			if (!$this->checkRecursiveRules($this->path, 0)) {

				$return = unlink($this->path->truePath, $this->context);
			}
		}
		return $return;

	}


	public function rmdir($pathname, $options)
	{
		$return = parent::rmdir($pathname, $options);
		if ($this->processPathname($pathname, null)) {
			if (!$this->checkRecursiveRules($this->path, 0)) {
				$return = rmdir($this->path->truePath, $this->context);
			}
		}
		return $return;

	}

	function mkdir($pathname, $mode, $options)
	{

		$return = parent::mkdir($pathname, $mode, $options);
		if ($this->processPathname($pathname, null)) {
			if (!$this->checkRecursiveRules($this->path, 0)) {
				$recursive = false;
				if (STREAM_MKDIR_RECURSIVE & $options) {
					$recursive = true;
				}

				$return = mkdir($this->path->truePath, $mode, $recursive, $options);
			}
		}
		return $return;

	}

	public function stream_urlstat($pathname, $flags)
	{

		$return = parent::stream_urlstat($pathname, $flags);
		if ($this->processPathname($pathname, null)) {
			if (!$this->checkRecursiveRules($this->path, 0)) {
				$return = stat($this->path->truePath, $flags);

			}
		}

		if ((!$return) && ($flags & STREAM_URL_STAT_QUIET)) {
			trigger_error("Stream type not configured for " . __CLASS__, E_USER_ERROR);
		}

		return $return;
	}

	/*
	 * Simle stream wrapping methods
	 */


}



