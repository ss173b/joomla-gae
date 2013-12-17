<?php

// GAE requires this function call in order to load the local xml files
libxml_disable_entity_loader(false);

// Load defined constants for Joomla under GAE
define('SET_JPATH_BASE', 'JOOMLACMSINSTALLDIR');
require __DIR__ . '/defines.php';

// Init loader
require_once JPATH_PLATFORM . '/loader.php';

// Add Installation prefix in case we have to over-ride any classes
JLoader::registerPrefix('J', GAEJOOMLALIBS);

// First check for classes prefixed with Installation in GAE then in Joomla
JLoader::registerPrefix('Installation', GAEINSTALLATIONLIBS);
JLoader::registerPrefix('Installation', JPATH_INSTALLATION);

// Register installation router
JLoader::register('JRouterInstallation', JPATH_INSTALLATION.'/application/router.php');

// Some file checks are relative to the current working directory, so set to where it would normally be
chdir (JPATH_INSTALLATION);


/**
 * Constant that is checked in included files to prevent direct access.
 * define() is used in the installation folder rather than "const" to not error for PHP 5.2 and lower
 */
define('_JEXEC', 1);

// Launch the application
require_once JPATH_INSTALLATION . '/application/framework.php';

// Get the application
$app = JApplicationWeb::getInstance('InstallationApplicationWeb');

// Execute the application
$app->execute();
