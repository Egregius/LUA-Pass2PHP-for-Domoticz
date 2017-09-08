<?php
require('/var/www/html/secure/settings.php');
error_reporting(E_ALL);ini_set("display_errors","on");
define('time',$_SERVER['REQUEST_TIME']);
if(status('lichten_auto')=='On')$auto=true;else $auto=false;
$Weg=status('Weg');
if(status('meldingen')=='On')$meldingen=true;else $meldingen=false;
$zon=status('zon');
$zongarage=700;
$zonkeuken=60;
$zoninkom=15;
$zonmedia=20;
$smappeeip='192.168.2.177';
if($_SERVER['REQUEST_METHOD']=='POST'){
		$device=$_POST['d'];
		$status=$_POST['s'];
		if(@include '/var/www/html/secure/pass2php/'.$device.'.php'){
			lg($device.' = '.$status);
			if(in_array($device,array('brander','badkamervuur'))){
				$prev=status($device);
				if($status!=$prev)setstatus('tt'.$device,time);
			}
			if(in_array($device,array('eettafel','zithoek','kamer','tobi','alex'))){
				if($status=='Off'){
					setstatus($device,'Off');
				}
				else{lg('else'.$status); setstatus($device,filter_var($status,FILTER_SANITIZE_NUMBER_INT));}
			}
			else setstatus($device,$status);
		}
}
if(timestamp('cron5')<time-4){
	settimestamp('cron5');
	if(timestamp('cron604800')<time-604790){
		settimestamp('cron604800');
		include('/var/www/html/secure/_cron604800.php');
	}
	if(timestamp('cron28800')<time-27790){
		settimestamp('cron28800');
		include('/var/www/html/secure/_cron28800.php');
	}
	if(timestamp('cron180')<time-130){
		settimestamp('cron180');
		include('/var/www/html/secure/_cron180.php');
	}
	if(timestamp('cron60')<time-58){
		settimestamp('cron60');
		include('/var/www/html/secure/_cron60.php');
	}
	include('/var/www/html/secure/_cron5.php');
	include('/var/www/html/secure/_verwarming.php');
}
?>
