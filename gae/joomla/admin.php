<?php

// GAE requires this function call in order to load the local xml files
libxml_disable_entity_loader(false);

// Set the base directory path
define('SET_JPATH_BASE', 'JOOMLACMSADMINDIR');

// Set the Joomla flag indicating that defines have been done
define('_JDEFINES', true);

// Load defined constants for Joomla under GAE
require __DIR__ . '/defines.php';

// Init loader
require_once JPATH_PLATFORM . '/loader.php';

// Add Installation prefix in case we have to over-ride any classes
// By setting prepend to true, we force our directory to be checked first
JLoader::registerPrefix('J', GAEJOOMLALIBS, false, true);

// Some file checks are relative to the current working directory, so set to where it would normally be
chdir (JPATH_BASE);

// Execute main index.php
require JOOMLACMSADMINFILE;
