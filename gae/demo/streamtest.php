<?php
/**
 * Created by PhpStorm.
 * User: gmort
 * Date: 12/30/13
 * Time: 12:41 PM
 */

$gaePath = dirname(__DIR__);
// Set the base directory path
define('SET_JPATH_BASE', 'JOOMLACMSADMINDIR');
define('_JEXEC', 1);

// Load defined constants for Joomla under GAE
require_once($gaePath . '/joomla/defines.php');

// Load and initialize Joomla CMS Admin framework
require_once JPATH_BASE . '/includes/framework.php';


require_once($gaePath.'/libraries/garyamort/github_io/loader/loader.php');

$streamNs = 'garyamort\github_io\stream';
$streamLibraryPath = $gaePath.'/libraries/garyamort/github_io/stream';
garyamort\github_io\loader\loader::registerNamespace($streamNs, $streamLibraryPath);

garyamort\github_io\loader\loader::setup();

use \garyamort\github_io\stream as gstream;
$streamType = 'ggsm';
$newRule = new gstream\mapper\replaceStart();
$newRule->pattern = $streamType;
$newRule->replacement = 'file';

gstream\morpher::addRule($newRule);


gstream\wrapper::registerWrapper();
gstream\morpher::registerWrapper();


$mypath = $streamType."://".__DIR__.'/myvar.txt';
$fp = fopen($mypath, "a");

fwrite($fp, "line8\n");
echo 'File position is '.ftell($fp);
fwrite($fp, "line9\n");
fwrite($fp, "line10\n");
echo 'File position is '.ftell($fp);

fclose($fp);
