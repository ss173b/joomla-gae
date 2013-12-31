<?php
/**
 * Created by PhpStorm.
 * User: gmort
 * Date: 12/30/13
 * Time: 12:41 PM
 */


/**
 * Determine if the code is executing on the development server.
 *
 * @return bool True if running in the developement server, false otherwise.
 */

use \garyamort\github_io\stream as gstream;


function isDevelServer() {
	$server_software = getenv("SERVER_SOFTWARE");
	$key = "Development";
	return strncmp($server_software, $key, strlen($key)) === 0;
}

function setupStreamMapping($pattern, $replacement)
{

	$newRule = new gstream\mapper\replaceStart();
	$newRule->pattern = $pattern;
	$newRule->replacement = $replacement;
	gstream\morpher::addRule($newRule);
}
function setupDevMappings()
{

	$devPath = 'file://'.__DIR__;
	$logPath = $devPath . '/log/';

	$cachePath = $devPath . '/cache/';
	$sessionPath = $devPath . '/session/';
	$tmpPath = $devPath . '/tmp/';
	$defaultPath = $devPath . '/default/';
	// log files
	setupStreamMapping('ggsm://log/', $logPath);

	// cache files
	setupStreamMapping('ggsm://cache/', $cachePath);

	// session rule
	setupStreamMapping('ggsm://session/', $sessionPath);

	// tmp files rule
	setupStreamMapping('ggsm://tmp/', $tmpPath);

	// default rule
	setupStreamMapping('ggsm:', $defaultPath);

}


function setupCloudMappings()
{


	$devPath = 'gggs://overnumerousness-site-tmp';
	$logPath = 'ggslog://';

	$cachePath = $devPath . '/cache/';
	$sessionPath = $devPath . '/session/';
	$tmpPath = $devPath . '/tmp/';
	$defaultPath = $devPath . '/default/';
	// log files
	setupStreamMapping('ggsm://log/', $logPath);

	// cache files
	setupStreamMapping('ggsm://cache/', $cachePath);

	// session rule
	setupStreamMapping('ggsm://session/', $sessionPath);

	// tmp files rule
	setupStreamMapping('ggsm://tmp/', $tmpPath);

	// default rule
	setupStreamMapping('ggsm://', $defaultPath);

}

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
garyamort\github_io\loader\loader::registerPsr4NamespacePrefix($streamNs, $streamLibraryPath);

garyamort\github_io\loader\loader::setup();


$streamType = 'ggsm';

ini_set('display_errors', '1');
error_reporting(E_ALL);

if (isDevelServer())
{
	setupDevMappings();
} else {
	setupCloudMappings();
}

gstream\wrapper::registerWrapper();
gstream\morpher::registerWrapper();
gstream\syslog::registerWrapper();
gstream\googlestorage::registerWrapper();

$mypaths = array();
$mypaths[] = $streamType.'://cache/mycache.txt';
$mypaths[] = $streamType.'://log/mylog.txt';
$mypaths[] = $streamType.'://session/mysession.txt';
$mypaths[] = $streamType.'://tmp/mytmp.txt';
$mypaths[] = $streamType.'://mydef.txt';



$line = 1;
foreach ($mypaths as $path)
{
	echo "testing path $path<br/>";
	$fp = fopen($path, "a");
	if (!$fp)
	{
		continue;
	}
	$now = time();
	$msg = 'line '.$line++." was added at $now \n";
	fwrite($fp, $msg);
	$msg = 'line '.$line++." was added at $now \n";
	fwrite($fp, $msg);
	fclose($fp);
}

foreach ($mypaths as $path)
{
	echo "Reading path $path<br/>";
	$fileContents = file_get_contents($path);
	if (!$fileContents)
	{
		continue;
	}
	$msg = "Contents of $path are ".$fileContents.'<hr/>';
	echo $msg;;
}


foreach ($mypaths as $path)
{
	echo "Statting path $path<br/>";
	var_dump(stat($path));
}
