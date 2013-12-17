<?php

// GAE requires this function call in order to load the local xml files
libxml_disable_entity_loader(false);



// Load defined constants for Joomla under GAE
require_once(__DIR__ . '/defines.php');


// Set the Joomla flag indicating that defines have been done
define('_JDEFINES', true);
// Pre-empt the normal bootstrapping because we need to make a change
// Bootstrap the application
define('JPATH_BASE', JOOMLACMSSITEDIR);
require_once JPATH_BASE . '/includes/defines.php';
require_once JPATH_BASE . '/includes/framework.php';

// Add Installation prefix in case we have to over-ride any classes
// By setting prepend to true, we force our directory to be checked first
JLoader::registerPrefix('J', GAEJOOMLALIBS, false, true);

//Some file checks are relative to the current working directory, so set to where it would normally be
chdir (JOOMLACMSSITEDIR);

// Execute install file
require_once(JOOMLACMSSITEFILE);