<?php
//error_reporting(E_ALL);ini_set("display_errors","on");
date_default_timezone_set('Europe/Brussels');
define('time',$_SERVER['REQUEST_TIME']);
if(apcu_fetch('slichten_auto')=='On')$auto=true;else $auto=false;
$Weg=apcu_fetch('Weg');
if(apcu_fetch('smeldingen')=='On')$meldingen=true;else $meldingen=false;
if(apcu_exists('zon'))$zon=apcu_fetch('zon');else $zon=10;
$zongarage=700;
$zonkeuken=60;
$zoninkom=15;
$zonmedia=10;
$smappeeip='192.168.2.177';
if($_SERVER['REQUEST_METHOD']=='POST'){
	$device=$_POST['d'];
	$status=$_POST['s'];
	if(@include '/var/www/html/secure/pass2php/'.$device.'.php'){
		lg($device.' = '.$status);
		if(in_array($device,array('brander','badkamervuur'))){
			$prev=apcu_fetch('s'.$device);
			if($status!=$prev)apcu_store('tt'.$device,time);
		}
		if(apcu_fetch('t'.$device)<time)apcu_store('t'.$device,time);
		if(in_array($device,array('eettafel','zithoek','kamer','tobi','alex'))){
			if($status=='Off')apcu_store('s'.$device,'Off');
			else apcu_store('s'.$device,filter_var($status,FILTER_SANITIZE_NUMBER_INT));
		}else apcu_store('s'.$device,$status);
	}
}
if(apcu_fetch('cron5')<time-4){
	apcu_store('cron5',time);
	if(apcu_fetch('cron604800')<time-604790){
		apcu_store('cron604800',time);
		include('/var/www/html/secure/_cron604800.php');
	}
	if(apcu_fetch('cron28800')<time-27790){
		apcu_store('cron28800',time);
		include('/var/www/html/secure/_cron28800.php');
	}
	if(apcu_fetch('cron180')<time-136){
		apcu_store('cron180',time);
		include('/var/www/html/secure/_cron180.php');
	}
	if(apcu_fetch('cron60')<time-58){
		apcu_store('cron60',time);
		include('/var/www/html/secure/_cron60.php');
	}
	include('/var/www/html/secure/_cron5.php');
	include('/var/www/html/secure/_verwarming.php');
}
function sw($name,$action='Toggle',$comment=''){
	if(is_array($name)){
		foreach($name as $i){
			if($i=='media')sw(array('tv','denon','tvled','kristal'),$action);
			elseif($i=='lichtenbeneden')sw(array('pirgarage','pirkeuken','pirliving','pirinkom','eettafel','zithoek','tvled','kristal','bureel','garage','keuken','werkblad','wasbook','kookplaat','inkom','zolderg'),$action);
			elseif($i=='lichtenboven')sw(array('pirhall','lichtbadkamer1','lichtbadkamer2','kamer','tobi','alex','hall','zolder'),$action);
			elseif($i=='slapen')sw(array('pirhall','hall','lichtenbeneden','poortrf','dampkap','GroheRed'),$action);
			elseif($i=='weg')sw(array('slapen','lichtenbeneden','lichtenboven'),$action);
			else{if(apcu_fetch('s'.$i)!=$action)sw($i,$action);}
		}
	}else{
		$msg = 'SWITCH '.$name.' => '.$action;
		if(!empty($comment)) $msg.=' => '.$comment;
		lg($msg);
		if(apcu_exists('i'.$name))file_get_contents('http://192.168.2.2:8080/json.htm?type=command&param=switchlight&idx='.apcu_fetch('i'.$name).'&switchcmd='.$action);
		else{apcu_store('s'.$name,$action);apcu_store('t'.$name,time);}
		usleep(50000);
	}
}
function double($name,$action,$comment='',$wait=2000000){sw($name,$action,$comment);usleep($wait);sw($name,$action,$comment.' repeat');}
function sl($name,$level,$info=''){
	$msg='SETLEVEL '.$name.' => '.$level;
	if(!empty($comment)) $msg.=' => '.$comment;
	lg($msg);
	if(apcu_exists('i'.$name))file_get_contents('http://192.168.2.2:8080/json.htm?type=command&param=switchlight&idx='.apcu_fetch('i'.$name).'&switchcmd=Set%20Level&level='.$level);
}
function ud($name,$nvalue,$svalue,$comment=""){
	$msg = 'SWITCH '.$name.' => '.$nvalue.' '.$svalue;
	if(!empty($comment)) $msg.=' => '.$comment;
	lg($msg);
	if(apcu_exists('i'.$name)){
		file_get_contents('http://192.168.2.2:8080/json.htm?type=command&param=udevice&idx='.apcu_fetch('i'.$name).'&nvalue='.$nvalue.'&svalue='.$svalue);
	}else{
		apcu_store('s'.$name,$svalue);apcu_store('t'.$name,time);
	}
}
function telegram($msg,$silent=true,$to=1){
	$telegrambot='123456789:ABCD-xCRhO-RBfUqICiJs8q9A_3YIr9irxI';
	$telegramchatid=123456789;
	$telegramchatid2=234567890;
	for($x=1;$x<=100;$x++){
		$result=json_decode(file_get_contents('https://api.telegram.org/bot'.$telegrambot.'/sendMessage?chat_id='.$telegramchatid.'&text='.urlencode($msg).'&disable_notification='.$silent));
		if(isset($result->ok))
			if($result->ok===true){lg('telegram sent to 1: '.$msg);break;}
			else lg('telegram sent failed');sleep($x*3);
		global $actions;$actions=$actions+1;
	}
	if($to>=2)
		for($x=1;$x<=100;$x++){
			$result=json_decode(file_get_contents('https://api.telegram.org/bot'.$telegrambot.'/sendMessage?chat_id='.$telegramchatid2.'&text='.urlencode($msg).'&disable_notification='.$silent));
			if(isset($result->ok))
				if($result->ok===true){lg('telegram sent to 2: '.$msg);break;}
				else lg('telegram sent failed');sleep($x*3);
			global $actions;$actions=$actions+1;
		}
	elseif($to==3){ios($msg);global $actions;$actions=$actions+1;}

}
function lg($msg){
	$time    = microtime(true);
	$dFormat = "Y-m-d H:i:s";
	$mSecs   =  $time - floor($time);
	$mSecs   =  substr(number_format($mSecs,3),1);
	$fp = fopen('/var/log/floorplanlog.log',"a+");
	fwrite($fp, sprintf("%s%s %s \n", date($dFormat), $mSecs, $msg));
	fclose($fp);
}
function RefreshZwave($node){
	$last=apcu_fetch('refresh'.$node);
	apcu_store('refresh'.$node,time);
	if($last<time-3600){
		$devices=json_decode(file_get_contents('http://192.168.2.2:8080/json.htm?type=openzwavenodes&idx=3',false),true);
		foreach($devices['result'] as $devozw)
			if($devozw['NodeID']==$node){
				$device=$devozw['Description'].' '.$devozw['Name'];
				break;
			}
		print strftime("%Y-%m-%d %H:%M:%S",time()).'   => Refreshing node '.$node.' '.$device.PHP_EOL;
		for($k=1;$k<=5;$k++){
			$result=file_get_contents('http://192.168.2.2:8080/ozwcp/refreshpost.html',false,stream_context_create(array('http'=>array('header'=>'Content-Type: application/x-www-form-urlencoded\r\n','method'=>'POST','content'=>http_build_query(array('fun'=>'racp','node'=>$node)),),)));
			if($result==='OK')break;
			sleep(1);
		}
		if(apcu_fetch('timedeadnodes')<time-298){
			apcu_store('timedeadnodes',time);
			foreach($devices as $node=>$data){
				if($node=="result"){
					foreach($data as $index=>$eltsNode){
						if($eltsNode["State"]=="Dead"&&!in_array($eltsNode['NodeID'],array(57))){
							telegram('Node '.$eltsNode['NodeID'].' '.$eltsNode['Description'].' ('.$eltsNode['Name'].') marked as dead, reviving '.ZwaveCommand($eltsNode['NodeID'],'HasNodeFailed'));
							ControllerBusy(10);
							ZwaveCommand(1,'Cancel');
						}
					}
				}
			}
		}
	}
}
function Zwavecancelaction(){file_get_contents('http://192.168.2.2:8080/ozwcp/admpost.html',false,stream_context_create(array('http'=>array('header'=>'Content-Type: application/x-www-form-urlencoded\r\n','method'=>'POST','content'=>http_build_query(array('fun'=>'cancel')),),)));}
function ZwaveCommand($node,$command){$cm=array('AssignReturnRoute'=>'assrr','DeleteAllReturnRoutes'=>'delarr','NodeNeighbourUpdate'=>'reqnnu','RefreshNodeInformation'=>'refreshnode','RequestNetworkUpdate'=>'reqnu','HasNodeFailed'=>'hnf','Cancel'=>'cancel');$cm=$cm[$command];for($k=1;$k<=5;$k++){$result=file_get_contents('http://192.168.2.2:8080/ozwcp/admpost.html',false,stream_context_create(array('http'=>array('header'=>'Content-Type: application/x-www-form-urlencoded\r\n','method'=>'POST','content'=>http_build_query(array('fun'=>$cm,'node'=>'node'.$node)),),)));if($result=='OK')break;sleep(1);}return $result;}
function ControllerBusy($retries){for($k=1;$k<=$retries;$k++){$result=file_get_contents('http://192.168.2.2:8080/ozwcp/poll.xml');$p=xml_parser_create();xml_parse_into_struct($p,$result,$vals,$index);xml_parser_free($p);foreach($vals as $val){if($val['tag']=='ADMIN'){$result=$val['attributes']['ACTIVE'];break;}}if($result=='false')break;if($k==$retries){ZwaveCommand(1,'Cancel');break;}sleep(1);}}
function convertToHours($time){if($time<600)return substr(strftime('%M:%S',$time),1);elseif($time>=600&&$time<3600)return strftime('%M:%S',$time);else return strftime('%k:%M:%S',$time);}
function endswith($string,$test){
    $strlen=strlen($string);
    $testlen=strlen($test);
    if($testlen>$strlen) return false;
    return substr_compare($string,$test,$strlen-$testlen,$testlen)===0;
}
function checkport($ip,$port){if(pingport($ip,$port)==1){$prevcheck=apcu_fetch($ip.':'.$port);if($prevcheck>=3)telegram($ip.':'.$port.' online',true);if($prevcheck>0)apcu_store($ip.':'.$port,0);}else{$check=apcu_fetch($ip.':'.$port)+1;if($check>0)apcu_store($ip.':'.$port,$check);if($check==3)telegram($ip.':'.$port.' Offline',true);if($check%12==0)telegram($ip.':'.$port.' nog steeds Offline',true);}}
function pingport($ip,$port){$file=fsockopen($ip,$port,$errno,$errstr,10);$status=0;if(!$file)$status=-1;else{fclose($file);$status=1;}return $status;}
