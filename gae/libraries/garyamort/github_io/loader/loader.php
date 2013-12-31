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
	 * Container for psr4 namespace prefixes => path map.
	 *
	 * @var    array
	 * @since  never
	 */
	protected static $psr4NamespacePrefixes = array();

	/**
	 * Register a namespace to the autoloader. When loaded, namespace paths are searched in a "last in, first out" order.
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
	public static function registerPsr4NamespacePrefix($namespace, $path, $reset = false, $prepend = false)
	{

		// Verify the library path exists.
		if (!file_exists($path))
		{
			throw new \RuntimeException('Library path ' . $path . ' cannot be found.', 500);
		}

		// If the namespace is not yet registered or we have an explicit reset flag then set the path.
		if (!isset(static::$psr4NamespacePrefixes[$namespace]) || $reset)
		{
			static::$psr4NamespacePrefixes[$namespace] = array($path);
		}

		// Otherwise we want to simply add the path to the namespace.
		else
		{
			if ($prepend)
			{
				array_unshift(static::$psr4NamespacePrefixes[$namespace], $path);
			}
			else
			{
				static::$psr4NamespacePrefixes[$namespace][] = $path;
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
	public static function loadByPsr4($class)
	{
		// Remove the root backslash if present.
		if ($class[0] == '\\')
		{
			$class = substr($class, 1);
		}

		// Loop through registered namespaces until we find a match.
		foreach (static::$psr4NamespacePrefixes as $nsPrefix => $prefixPaths)
		{
			if (strpos($class, $nsPrefix) === 0)
			{


				// Strip the namespace prefix
				$localClassName = substr($class, strlen($nsPrefix));

				// Convert namespace slashes to directory slashes
				$localClassPath = str_replace('\\', DIRECTORY_SEPARATOR, $localClassName);



				// Loop through paths registered to this namespace until we find a match.
				foreach ($prefixPaths as $prefixPath)
				{
					$fullClassPath = $prefixPath . $localClassPath . '.php';

					// We check for class_exists to handle case-sensitive file systems
					if (file_exists($fullClassPath) && !class_exists($class, false))
					{
						return (bool) include_once $fullClassPath;
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
			// Register the PSR-4 based autoloader.
			spl_autoload_register(array($loaderClassname, 'loadByPsr4'));
		}
	}
} 