<?php
/**
 * Created by PhpStorm.
 * User: gmort
 * Date: 12/6/13
 * Time: 7:02 PM
 */

// Calculate all our paths relative to this files path
define('JOOMLADEFINEDIR',__DIR__);
define('GAEDIR', dirname(JOOMLADEFINEDIR));
define('GAELIBS', GAEDIR . '/libraries');
define('GAEINSTALLATIONLIBS', GAELIBS . '/installation');
define('GAEJOOMLALIBS', GAELIBS . '/joomla');
define('GAEAPPDIR', dirname(GAEDIR));
define('JOOMLACMSSITEDIR', GAEAPPDIR.'/joomla-cms');
define('JOOMLACMSADMINDIR', JOOMLACMSSITEDIR.'/administrator');
define('JOOMLACMSINSTALLDIR', JOOMLACMSSITEDIR.'/installation');
define('JOOMLACMSSITEFILE', JOOMLACMSSITEDIR.'/index.php');
define('JOOMLACMSADMINFILE', JOOMLACMSADMINDIR.'/index.php');
define('JOOMLACMSINSTALLFILE', JOOMLACMSINSTALLDIR.'/index.php');


// Prevent joomla from loading standards defines file and allows for overriding JPATH_BASE
define('_JDEFINES', 1);

// Register custom JPATH_BASE
if (defined('SET_JPATH_BASE'))
	define('JPATH_BASE', constant(SET_JPATH_BASE));
else
	define('JPATH_BASE', realpath(JOOMLACMSSITEDIR));

// Global definitions
$parts = explode(DIRECTORY_SEPARATOR, JOOMLACMSSITEDIR);

//Defines.
define('JPATH_ROOT',          implode(DIRECTORY_SEPARATOR, $parts));
define('JPATH_SITE',          JPATH_ROOT);
define('JPATH_CONFIGURATION', JPATH_ROOT);
define('JPATH_ADMINISTRATOR', JPATH_ROOT . '/administrator');
define('JPATH_LIBRARIES',     JPATH_ROOT . '/libraries');
define('JPATH_PLUGINS',       JPATH_ROOT . '/plugins');
define('JPATH_INSTALLATION',  JPATH_ROOT . '/installation');
define('JPATH_CACHE',         JPATH_BASE . '/cache');
define('JPATH_MANIFESTS',     JPATH_ADMINISTRATOR . '/manifests');

// Register custom JPATH_THEMES
if (defined('SET_JPATH_THEMES'))
	define('JPATH_THEMES',    constant(SET_JPATH_THEMES));
else
	define('JPATH_THEMES',    JPATH_BASE . '/templates');

// Loader requires JPATH_PLATFORM
define('JPATH_PLATFORM',      JPATH_LIBRARIES);
// Setup new Google Storage stream wrapper to handle append mode
// Setup the GS stream wrapper
require_once(GAELIBS.'/gmort/appengine/ext/cloud_storage_streams/CloudStorageStreamWrapper.php');

$url_flags = STREAM_IS_URL;
// Clear the existing GS wrapper
$existed = in_array("gs", stream_get_wrappers());
if ($existed) {
	stream_wrapper_unregister("gs");
}

// Replace it with our own wrapper
stream_wrapper_register('gs',
	'\gmort\appengine\ext\cloud_storage_streams\CloudStorageStreamWrapper',
	$url_flags);

