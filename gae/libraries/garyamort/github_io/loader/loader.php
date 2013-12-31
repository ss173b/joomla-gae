<?php
/**
 * Created by PhpStorm.
 * User: gmort
 * Date: 12/31/13
 * Time: 1:43 AM
 */

namespace garyamort\github_io\loader;


class loader extends \JLoader {

	/**
	 * Register a namespace to the autoloader. When loaded, namespace paths are searched in a "last in, first out" order. This class merely replaces self with static for late static binding
	 *
	 * @param   string   $namespace  A case sensitive Namespace to register.
	 * @param   string   $path       A case sensitive absolute file path to the library root where classes of the given namespace can be found.
	 * @param   boolean  $reset      True to reset the namespace with only the given lookup path.
	 * @param   boolean  $prepend    If true, push the path to the beginning of the namespace lookup paths array.
	 *
	 * @return  void
	 *
	 * @throws  RuntimeException
	 *
	 * @since   12.3
	 */
	public static function registerNamespace($namespace, $path, $reset = false, $prepend = false)
	{

		// Verify the library path exists.
		if (!file_exists($path))
		{
			throw new \RuntimeException('Library path ' . $path . ' cannot be found.', 500);
		}

		// If the namespace is not yet registered or we have an explicit reset flag then set the path.
		if (!isset(static::$namespaces[$namespace]) || $reset)
		{
			static::$namespaces[$namespace] = array($path);
		}

		// Otherwise we want to simply add the path to the namespace.
		else
		{
			if ($prepend)
			{
				array_unshift(self::$namespaces[$namespace], $path);
			}
			else
			{
				static::$namespaces[$namespace][] = $path;
			}
		}
	}

	/**
	 * Method to autoload classes that are namespaced to the PSR-0 standard.
	 *
	 * @param   string  $class  The fully qualified class name to autoload.
	 *
	 * @return  boolean  True on success, false otherwise.
	 *
	 * @since   13.1
	 */
	public static function loadByPsr0($class)
	{
		// Remove the root backslash if present.
		if ($class[0] == '\\')
		{
			$class = substr($class, 1);
		}

		// Loop through registered namespaces until we find a match.
		foreach (static::$namespaces as $ns => $paths)
		{
			if (strpos($class, $ns) === 0)
			{
				$localClassname = substr($class, strlen($ns));
				$localClassname = str_replace('\\', DIRECTORY_SEPARATOR, $localClassname);



				// Loop through paths registered to this namespace until we find a match.
				foreach ($paths as $path)
				{
					$localClassPath = $path .str_replace('_', DIRECTORY_SEPARATOR, $localClassname) . '.php';

					// We check for class_exists to handle case-sensitive file systems
					if (file_exists($localClassPath) && !class_exists($class, false))
					{
						return (bool) include_once $localClassPath;
					}
				}
			}
		}

		return false;
	}


	public static function setup($enablePsr = true, $enablePrefixes = false, $enableClasses = true)
	{

		$loaderClassname = get_called_class();
		if ($enableClasses)
		{
			// Register the class map based autoloader.
			spl_autoload_register(array($loaderClassname, 'load'));
		}

		if ($enablePrefixes)
		{

			$libPath = dirname(dirname(dirname(__DIR__)));
			// Register the J prefix and base path for Joomla platform libraries.
			static::registerPrefix('J', $libPath . '/joomla');

			// Register the prefix autoloader.
			spl_autoload_register(array($loaderClassname, '_autoload'));
		}

		if ($enablePsr)
		{
			// Register the PSR-0 based autoloader.
			spl_autoload_register(array($loaderClassname, 'loadByPsr0'));
			spl_autoload_register(array($loaderClassname, 'loadByAlias'));
		}
	}
} 