<?php
$LogFile='/var/log/floorplanlog.log';
$users=array('username1'=>'password1','username2'=>'password2');
$cookie='nameofthecookietouse';
date_default_timezone_set('Europe/Brussels');

$timediff=0;
$Usleep=10000;
$authenticated=false;
$home=false;
$log=true;
$time=$_SERVER['REQUEST_TIME'];
$offline=$time-300;
$eendag=$time-82800;
$page=basename($_SERVER['PHP_SELF']);
$homes=array('username1','username2');
require('/var/www/html/secure/functions.php');
require('/var/www/html/secure/authentication.php');
?>