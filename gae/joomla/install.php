<?php


// GAE requires this function call in order to load the local xml files
libxml_disable_entity_loader(false);

// Load defined constants for Joomla under GAE
require_once __DIR__ . '/defines.php';

// Pre-empt the normal bootstrapping because we need to make a change
// Bootstrap the application
require_once JOOMLACMSINSTALLDIR . '/application/bootstrap.php';

// Add Installation prefix in case we have to over-ride any classes
// By setting prepend to true, we force our directory to be checked first
JLoader::registerPrefix('Installation', GAEINSTALLATIONLIBS, false, true);

// Add Installation prefix in case we have to over-ride any classes
// By setting prepend to true, we force our directory to be checked first
JLoader::registerPrefix('J', GAEJOOMLALIBS, false, true);

//Some file checks are relative to the current working directory, so set to where it would normally be
chdir (JOOMLACMSINSTALLDIR);

// Execute install file
require_once JOOMLACMSINSTALLFILE;