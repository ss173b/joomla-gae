<?php
/**
 * GAE request routing is a bit weird and can cause base href issues.
 * the best request uri to ensure path consistency is
 * http://domain/administrator/index.php/
 * This allows for SEF urls in admin, and pushes extra stuff to the end
 */

if ( strpos($_SERVER['REQUEST_URI'], '/administrator/index.php') !== 0)
{
	$uri = $_SERVER['REQUEST_URI'];
	$targetUri = '/administrator/index.php';
	$sourceAdmin = '/administrator';
	$sourceAdminSlash = $sourceAdmin .'/';


	// Convert administrator with slash to administrator/index.php
	if (strpos($uri, $sourceAdminSlash) === 0)
	{
		$redirectUri = $targetUri . substr($uri, strlen($sourceAdminSlash));
	} else {
		$redirectUri = $targetUri . substr($uri, strlen($sourceAdmin));
	}

	header( 'Location: '.$redirectUri ) ;
	exit();
}

// GAE requires this function call in order to load the local xml files
libxml_disable_entity_loader(false);
// Load defined constants for Joomla under GAE
require_once(__DIR__ . '/defines.php');

// Pre-empt the normal bootstrapping because we need to make a change
// Set the base directory path
define('JPATH_BASE', JOOMLACMSADMINDIR);
// Set the Joomla flag indicating that defines have been done
define('_JDEFINES', true);
// Load Joomla defines
require_once JPATH_BASE . '/includes/defines.php';
// Load and initialize Joomla CMS Admin framework
require_once JPATH_BASE . '/includes/framework.php';

// Add Installation prefix in case we have to over-ride any classes
// By setting prepend to true, we force our directory to be checked first
JLoader::registerPrefix('J', GAEJOOMLALIBS, false, true);

//Some file checks are relative to the current working directory, so set to where it would normally be
chdir (JOOMLACMSADMINDIR);

// Execute install file
require_once(JOOMLACMSADMINFILE);