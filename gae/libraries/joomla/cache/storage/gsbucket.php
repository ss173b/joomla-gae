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
 * Google Storage Bucket cache handler.  Quick hack of File Handler
 *
 * @package     Joomla.Platform
 * @subpackage  Cache
 * @since       11.1
 */
class JCacheStorageGsbucket extends JCacheStorageFile
{

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
	 * Get a cache_id string from an id/group pair
	 *
	 * @param   string  $id     The cache data id
	 * @param   string  $group  The cache data group
	 *
	 * @return  string   The cache_id string
	 *
	 * @since   11.1
	 */
	protected function _getCacheId($id, $group)
	{
		$name = $this->_application . '-' . $id . '-' . $this->_language;
		$this->rawname =  $name;
		$cacheId =  $name;

		return $cacheId;
	}


	/**
	 * Constructor
	 *
	 * @param   array  $options  Optional parameters
	 *
	 * @since   11.1
	 */
	public function __construct($options = array())
	{
		parent::__construct($options);
		// Override file path with bucket
		$config = JFactory::getConfig();

		$this->_root = 'gs://'.$config->get('gsbucket_name', '').'/cache';
		$expires = $this->_now + $this->_lifetime;

		$metadata = ['now' => $this->_now, 'lifetime' => $this->_lifetime];
		//$cacheControl = ['max-age=' => $this->_lifetime ];
		$cacheControl = 'no-cache';
		$options = [ "gs" => [ "Content-Type" => "application/json",
			"Cache-Control" => $cacheControl,
			"enable_cache" => true,
			"enable_optimistic_cache" => true,
			"read_cache_expiry_seconds" => $this->_lifetime,
			"metadata"=> $metadata
								]];
		$this->filectx = stream_context_create($options);

		$options = [ "gs" => [
			"enable_cache" => true,
			"enable_optimistic_cache" => true,
			"read_cache_expiry_seconds" => $this->_lifetime,
		]];
		$this->dirctx = stream_context_create($options);


		// If the folder doesn't exist try to create it
		if (!is_dir($this->_root ))
		{

			//echo "creating {$this->_root }<br/>";
			mkdir($this->_root , 777 , true, $this->dirctx);

		}
	}

	// NOTE: raw php calls are up to 100 times faster than JFile or JFolder

	/**
	 * Get cached data from a Google Storage Bucket by id and group
	 *
	 * @param   string   $id         The cache data id
	 * @param   string   $group      The cache data group
	 * @param   boolean  $checkTime  True to verify cache time expiration threshold
	 *
	 * @return  mixed  Boolean false on failure or a cached data string
	 *
	 * @since   11.1
	 */
	public function get($id, $group, $checkTime = true)
	{
		$data = false;
		$path = $this->_getFilePath($id, $group);
		if (file_exists($path))
		{
			//echo "retrieve cache to $path<br/>";
			$data =  file_get_contents($path, false, $this->filectx);
		}

		return $data;
	}


	/**
	 * Store the data to a Google Storage Bucket by id and group
	 *
	 * @param   string  $id     The cache data id
	 * @param   string  $group  The cache data group
	 * @param   string  $data   The data to store in cache
	 *
	 * @return  boolean  True on success, false otherwise
	 *
	 * @since   11.1
	 */
	public function store($id, $group, $data)
	{
		$path = $this->_getFilePath($id, $group);
		echo "storing cache to $path<br/>";
		return file_put_contents($path, $data,  0, $this->filectx);

	}



	/**
	 * Garbage collect expired cache data
	 *
	 * @return  boolean  True on success, false otherwise.
	 *
	 * @since   11.1
	 */
	public function gc()
	{
		return true;

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
	 * Lock cached item
	 *
	 * @param   string   $id        The cache data id
	 * @param   string   $group     The cache data group
	 * @param   integer  $locktime  Cached item max lock time
	 *
	 * @return  boolean  True on success, false otherwise.
	 *
	 * @since   11.1
	 */
	public function lock($id, $group, $locktime)
	{

		$returning = new stdClass;
		$returning->locklooped = false;
		$returning->locked = true;
		return $returning;
	}

	/**
	 * Unlock cached item
	 *
	 * @param   string  $id     The cache data id
	 * @param   string  $group  The cache data group
	 *
	 * @return  boolean  True on success, false otherwise.
	 *
	 * @since   11.1
	 */
	public function unlock($id, $group = null)
	{
		return true;
	}

	/**
	 * Check to make sure cache is still valid, if not, delete it.
	 *
	 * @param   string  $id     Cache key to expire.
	 * @param   string  $group  The cache data group.
	 *
	 * @return  boolean  False if not valid
	 *
	 * @since   11.1
	 */
	protected function _checkExpire($id, $group)
	{
		$path = $this->_getFilePath($id, $group);
		return file_exists($path);
	}

	/**
	 * Get a cache file path from an id/group pair
	 *
	 * @param   string  $id     The cache data id
	 * @param   string  $group  The cache data group
	 *
	 * @return  string   The cache file path
	 *
	 * @since   11.1
	 */
	protected function _getFilePath($id, $group)
	{
		$name = $this->_getCacheId($id, $group);
		$dir = $this->_root . '/' . $group;

		// If the folder doesn't exist try to create it
		if (!is_dir($dir))
		{

		//	echo "creating $dir<br/>";
			mkdir($dir, 700 , true, $this->dirctx);
		}

		return $dir . '/' . $name . '.json' ;
	}

	/**
	 * Quickly delete a folder of files
	 *
	 * @param   string  $path  The path to the folder to delete.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   11.1
	 */
	protected function _deleteFolder($path)
	{
		// Sanity check
		if (!$path  || empty($this->_root))
		{
			// Bad programmer! Bad Bad programmer!
			JLog::add(__METHOD__ . JText::_('JLIB_FILESYSTEM_ERROR_DELETE_BASE_DIRECTORY'), JLog::WARNING, 'jerror');
			return false;
		}

		$path = $this->_cleanPath($path);

		// Check to make sure path is inside cache folder, we do not want to delete Joomla root!
		$pos = strpos($path, $this->_cleanPath($this->_root));

		if ($pos === false || $pos > 0)
		{
			JLog::add(__METHOD__. JText::sprintf('JLIB_FILESYSTEM_ERROR_PATH_IS_NOT_A_FOLDER', $path), JLog::WARNING, 'jerror');
			return false;
		}

		if (is_dir($path))
		{
			return rmdir($path, $this->dirctx);
		}

		if (is_file($path))
		{
			return unlink($path, $this->filectx);
		}

			JLog::add(__METHOD__ . JText::sprintf('JLIB_FILESYSTEM_ERROR_FOLDER_DELETE', $path), JLog::WARNING, 'jerror');
		return false;

	}

	/**
	 * Function to strip additional / or \ in a path name
	 *
	 * @param   string  $path  The path to clean
	 * @param   string  $ds    Directory separator (optional)
	 *
	 * @return  string  The cleaned path
	 *
	 * @since   11.1
	 */
	protected function _cleanPath($path, $ds = DIRECTORY_SEPARATOR)
	{
		$path = trim($path);

		if (empty($path))
		{
			$path = $this->_root;
		}

		return $path;
	}

	/**
	 * Utility function to quickly read the files in a folder.
	 *
	 * @param   string   $path           The path of the folder to read.
	 * @param   string   $filter         A filter for file names.
	 * @param   mixed    $recurse        True to recursively search into sub-folders, or an
	 *                                   integer to specify the maximum depth.
	 * @param   boolean  $fullpath       True to return the full path to the file.
	 * @param   array    $exclude        Array with names of files which should not be shown in
	 *                                   the result.
	 * @param   array    $excludefilter  Array of folder names to exclude
	 *
	 * @return  array    Files in the given folder.
	 *
	 * @since   11.1
	 */
	protected function _filesInFolder($path, $filter = '.', $recurse = false, $fullpath = false
		, $exclude = null, $excludefilter = null)
	{
		$arr = array();

		// Check to make sure the path valid and clean
		$path = $this->_cleanPath($path);
		//echo "checking for files in $path<br/>";

		// Is the path a folder?
		if (!is_dir($path))
		{
			JLog::add(__METHOD__. JText::sprintf('JLIB_FILESYSTEM_ERROR_PATH_IS_NOT_A_FOLDER', $path), JLog::WARNING, 'jerror');
			return false;
		}

		// Read the source directory.
		if (!($handle = @opendir($path)))
		{
			return $arr;
		}


		while (($file = readdir($handle)) !== false)
		{

			//echo "processing file $file <br/>";

				$dir = $path . '/' . $file;
				$isDir = is_dir($dir);
				if ($isDir)
				{
					if ($recurse)
					{
						if (is_int($recurse))
						{
							$arr2 = $this->_filesInFolder($dir, $filter, $recurse - 1, $fullpath);
						}
						else
						{
							$arr2 = $this->_filesInFolder($dir, $filter, $recurse, $fullpath);
						}

						$arr = array_merge($arr, $arr2);
					}
				}
				else
				{
					if (preg_match("/$filter/", $file))
					{
						if ($fullpath)
						{
							$arr[] = $path . '/' . $file;
						}
						else
						{
							$arr[] = $file;
						}
					}
				}
		}

		closedir($handle);

		return $arr;
	}

	/**
	 * Utility function to read the folders in a folder.
	 *
	 * @param   string   $path           The path of the folder to read.
	 * @param   string   $filter         A filter for folder names.
	 * @param   mixed    $recurse        True to recursively search into sub-folders, or an integer to specify the maximum depth.
	 * @param   boolean  $fullpath       True to return the full path to the folders.
	 * @param   array    $exclude        Array with names of folders which should not be shown in the result.
	 * @param   array    $excludefilter  Array with regular expressions matching folders which should not be shown in the result.
	 *
	 * @return  array  Folders in the given folder.
	 *
	 * @since   11.1
	 */
	protected function _folders($path, $filter = '.', $recurse = false, $fullpath = false
		, $exclude = null, $excludefilter = null)
	{
		$arr = array();

		echo "checking path <br/>";
		echo $path;
		var_dump(stat($path));
		echo '<hr/>';
		// Check to make sure the path valid and clean
		$path = $this->_cleanPath($path);

		//echo "examinging $path <br/>";
		// Is the path a folder?
		if (!is_dir($path))
		{
			JLog::add(__METHOD__ . JText::sprintf(' Path is not a folder', $path), JLog::WARNING, 'jerror');
			return false;
		}

		// Read the source directory
		if (!($handle = @opendir($path)))
		{
			return $arr;
		}


		while (($file = readdir($handle)) !== false)
		{
				$dir = $path . '/' . $file;
				$isDir = is_dir($dir);
				if ($isDir)
				{
					// Removes filtered directories
					if (preg_match("/$filter/", $file))
					{
						if ($fullpath)
						{
							$arr[] = $dir;
						}
						else
						{
							$arr[] = $file;
						}
					}
					if ($recurse)
					{
						if (is_int($recurse))
						{
							$arr2 = $this->_folders($dir, $filter, $recurse - 1, $fullpath, $exclude, $excludefilter);
						}
						else
						{
							$arr2 = $this->_folders($dir, $filter, $recurse, $fullpath, $exclude, $excludefilter);
						}

						$arr = array_merge($arr, $arr2);
					}
				}

		}
		closedir($handle);

		return $arr;

	}
}
