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


// Set Jexec
define('_JEXEC', 1);


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

