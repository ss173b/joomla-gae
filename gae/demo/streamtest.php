<?php
/**
 * Created by PhpStorm.
 * User: gmort
 * Date: 12/30/13
 * Time: 12:41 PM
 */

$rule = dirname(__DIR__).'/libraries/garyamort/github_io/stream/path.php';
require_once($rule);
$rule = dirname(__DIR__).'/libraries/garyamort/github_io/stream/mapper/rule.php';
require_once($rule);
$rule = dirname(__DIR__).'/libraries/garyamort/github_io/stream/mapper/regex.php';
require_once($rule);
$rule = dirname(__DIR__).'/libraries/garyamort/github_io/stream/mapper/replace.php';
require_once($rule);
$rule = dirname(__DIR__).'/libraries/garyamort/github_io/stream/mapper/replaceStart.php';
require_once($rule);
$wrapper = dirname(__DIR__).'/libraries/garyamort/github_io/stream/wrapper.php';
require_once($wrapper);

$wrapper = dirname(__DIR__).'/libraries/garyamort/github_io/stream/morpher.php';
require_once($wrapper);

$streamType = 'ggsm';
$newRule = new \garyamort\github_io\stream\mapper\replaceStart();
$newRule->pattern = $streamType;
$newRule->replacement = 'file';

\garyamort\github_io\stream\morpher::addRule($newRule);


\garyamort\github_io\stream\wrapper::registerWrapper();
\garyamort\github_io\stream\morpher::registerWrapper();


$mypath = $streamType."://".__DIR__.'/myvar.txt';
$fp = fopen($mypath, "a");

fwrite($fp, "line5\n");
echo 'File position is '.ftell($fp);
fwrite($fp, "line6\n");
fwrite($fp, "line7\n");
echo 'File position is '.ftell($fp);

fclose($fp);
