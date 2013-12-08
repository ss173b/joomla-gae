<?php

// GAE requires this function call in order to load the local xml files
libxml_disable_entity_loader(false);

// Load defined constants for Joomla under GAE
require_once(__DIR__ . '/defines.php');

//Some file checks are relative to the current working directory, so set to where it would normally be
chdir (JOOMLACMSSITEDIR);

// Execute install file
require_once(JOOMLACMSSITEFILE);