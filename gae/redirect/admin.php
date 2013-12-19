<?php
/**
 * GAE request routing is a bit weird so we insure all entree points
 * go to index.php
 */

$redirectUri = '/administrator/index.php';
header( 'Location: '.$redirectUri ) ;

