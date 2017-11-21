<?php
require('/var/www/html/secure/settings.php');
error_reporting(E_ALL);ini_set("display_errors","on");
define('time',$_SERVER['REQUEST_TIME']);
if(apcu_fetch('lichten_auto')=='On')$auto=true;else $auto=false;
$Weg=apcu_fetch('Weg');
if(apcu_fetch('meldingen')=='On')$meldingen=true;else $meldingen=false;
$zon=apcu_fetch('zon');
if($_SERVER['REQUEST_METHOD']=='POST'){
		$device=$_POST['d'];
		$status=$_POST['s'];
		if(@include '/var/www/html/secure/pass2php/'.$device.'.php'){
			lg($device.' = '.$status);
			if(in_array($device,array('brander','badkamervuur'))){
				$prev=apcu_fetch($device);
				if($status!=$prev)setstatus('tt'.$device,time);
			}
			if(in_array($device,array('eettafel','zithoek','kamer','tobi','alex'))){
				if($status=='Off'){
					setstatus($device,0);
				}elseif($status=='On'){
					setstatus($device,100);
				}else{
					$status=filter_var($status,FILTER_SANITIZE_NUMBER_INT);
					setstatus($device,$status);
				}
			}elseif($device=='luifel'){
				if($status=='Closed'){
					setstatus($device,100);
				}elseif($status=='Open'){
					setstatus($device,0);
				}else{
					$status=filter_var($status,FILTER_SANITIZE_NUMBER_INT);
					setstatus($device,$status);				}
			}else setstatus($device,$status);
		}
}
if(apcu_fetch('cron5')<time()-4){
	apcu_store('cron5',time());
	if(apcu_fetch('cron604800')<time()-604790){
		apcu_store('cron604800',time());
		include('/var/www/html/secure/_cron604800.php');
	}
	if(apcu_fetch('cron120')<time()-118){
		apcu_store('cron120'time());
		include('/var/www/html/secure/_cron120.php');
		if(apcu_fetch('cron28800')<time()-27790){
			apcu_store('cron28800',time());
			include('/var/www/html/secure/_cron28800.php');
		}
	}
	if(apcu_fetch('cron60')<time()-58){
		apcu_store('cron60',time());
		include('/var/www/html/secure/_cron60.php');
	}
	include('/var/www/html/secure/_cron5.php');
	include('/var/www/html/secure/_verwarming.php');
}
?>
