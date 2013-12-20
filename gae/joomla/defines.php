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


