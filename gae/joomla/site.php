<?php

// GAE requires this function call in order to load the local xml files
libxml_disable_entity_loader(false);

// Load defined constants for Joomla under GAE
require_once(__DIR__ . '/defines.php');

// Execute install file
require_once(JOOMLACMSSITEFILE);