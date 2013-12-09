<?php
/**
 * Created by PhpStorm.
 * User: gmort
 * Date: 12/7/13
 * Time: 10:44 PM
 */

 $dbtype = 'mysqli';
 $host = '/cloudsql/overnumerousness-site:joomla';
//public $host = '173.194.110.227';
 $user = 'root';
 $password = '';
 $db = 'overnumeroussness';


$connection = mysqli_connect(null, $user, $password, null, null, $host) or die("Error " . mysqli_error($link));

echo 'Successful connection';

