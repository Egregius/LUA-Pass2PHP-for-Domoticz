<?php error_reporting(E_ALL);ini_set("display_errors","on");date_default_timezone_set('Europe/Brussels');define('time',$_SERVER['REQUEST_TIME']);$c=ex($_REQUEST['c']);
foreach($c as $device=>$status){
	if(@include '/volume1/web/secure/pass2php/'.$device.'.php'){
		if($device=='brander'){
			if($status!=apcu_fetch('sbrander'))apcu_store('t'.$device,time);
		}else{
			if(apcu_fetch('t'.$device)<time)apcu_store('t'.$device,time);
		}
		if(in_array($device,array('eettafel','zithoek','kamer','tobi','alex'))){
			if($status=='Off')apcu_store('s'.$device,'Off');
			else apcu_store('s'.$device,filter_var($status,FILTER_SANITIZE_NUMBER_INT));
		}else apcu_store('s'.$device,$status);
		$dev=$device;
		if($device=='miniliving1s'&&$status=='Off')print strftime("%Y-%m-%d %H:%M:%S",time()).'   => CRON Forced'.PHP_EOL;
		else print strftime("%Y-%m-%d %H:%M:%S",time()).'   -> '.$device.' -> '.$status.PHP_EOL;
	}
	else{if(!empty($device)&&!endswith($device,'_Utility')&&!endswith($device,'_Temperature'))print strftime("%Y-%m-%d %H:%M:%S",time()).'      '.$device.' -> '.$status.PHP_EOL;}
}
if(!isset($dev))die();
include '/volume1/web/secure/__CRON.php';
function sw($name,$action='Toggle',$comment=''){
	$msg = strftime("%Y-%m-%d %H:%M:%S",time()).'   => SWITCH '.$name.' => '.$action;
	if(!empty($comment)) $msg.=' => '.$comment;
	print $msg.PHP_EOL;
	if(apcu_exists('i'.$name)){
		file_get_contents('http://127.0.0.1:8084/json.htm?type=command&param=switchlight&idx='.apcu_fetch('i'.$name).'&switchcmd='.$action);
		//sleep(2);
	}else{
		apcu_store('s'.$name,$action);apcu_store('t'.$name,time);
	}
}
function double($name,$action,$comment='',$wait=2000000){sw($name,$action,$comment);usleep($wait);sw($name,$action,$comment.' repeat');}
function sl($name,$level,$info=''){
	$msg=strftime("%Y-%m-%d %H:%M:%S",time()).'   => SETLEVEL '.$name.' => '.$level;
	if(!empty($comment)) $msg.=' => '.$comment;
	print $msg.PHP_EOL;
	if(apcu_exists('i'.$name)){
		file_get_contents('http://127.0.0.1:8084/json.htm?type=command&param=switchlight&idx='.apcu_fetch('i'.$name).'&switchcmd=Set%20Level&level='.$level);
		//sleep(2);
	}
}
function ud($name,$nvalue,$svalue,$info=""){
	if(apcu_exists('i'.$name)){
		file_get_contents('http://127.0.0.1:8084/json.htm?type=command&param=udevice&idx='.apcu_fetch('i'.$name).'&nvalue='.$nvalue.'&svalue='.$svalue);
		//sleep(4);
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
function lg($msg){file_get_contents('http://127.0.0.1:8084/json.htm?type=command&param=addlogmessage&message='.urlencode('=> '.$msg));}
function ios($msg){
	$appledevice='1234567890/ZHxYptWlD4zoKvGC1VYH805kSRqROHYVNSUzmWV';
	$appleid='your@apple.id';
	$applepass='applepass';
	require_once('findmyiphone.php');
	$fmi=new FindMyiPhone(appleid,applepass);
	$fmi->playSound(appledevice,$msg);
	sms($msg);
}
function sms($msg){
	exit;
	$smsuser='clickatelluser';
	$smspassword='clickatellpass';
	$smsapi=1234567;
	$smstofrom=32123456789;
	file_get_contents('http://api.clickatell.com/http/sendmsg?user='.$smsuser.'&password='.$smspassword.'&api_id='.$smsapi.'&to='.$smstofrom.'&text='.urlencode($msg).'&from='.$smstofrom.'');
}
function RefreshZwave($node){
	$last=apcu_fetch('refresh'.$node);
	apcu_store('refresh'.$node,time);
	if($last<time-3600){
		$devices=json_decode(file_get_contents('http://127.0.0.1:8084/json.htm?type=openzwavenodes&idx=3',false),true);
		foreach($devices['result'] as $devozw)
			if($devozw['NodeID']==$node){
				$device=$devozw['Description'].' '.$devozw['Name'];
				break;
			}
		print strftime("%Y-%m-%d %H:%M:%S",time()).'   => Refreshing node '.$node.' '.$device.PHP_EOL;
		for($k=1;$k<=5;$k++){
			$result=file_get_contents('http://127.0.0.1:8084/ozwcp/refreshpost.html',false,stream_context_create(array('http'=>array('header'=>'Content-Type: application/x-www-form-urlencoded\r\n','method'=>'POST','content'=>http_build_query(array('fun'=>'racp','node'=>$node)),),)));
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
function Zwavecancelaction(){file_get_contents('http://127.0.0.1:8084/ozwcp/admpost.html',false,stream_context_create(array('http'=>array('header'=>'Content-Type: application/x-www-form-urlencoded\r\n','method'=>'POST','content'=>http_build_query(array('fun'=>'cancel')),),)));}
function ZwaveCommand($node,$command){$cm=array('AssignReturnRoute'=>'assrr','DeleteAllReturnRoutes'=>'delarr','NodeNeighbourUpdate'=>'reqnnu','RefreshNodeInformation'=>'refreshnode','RequestNetworkUpdate'=>'reqnu','HasNodeFailed'=>'hnf','Cancel'=>'cancel');$cm=$cm[$command];for($k=1;$k<=5;$k++){$result=file_get_contents('http://127.0.0.1:8084/ozwcp/admpost.html',false,stream_context_create(array('http'=>array('header'=>'Content-Type: application/x-www-form-urlencoded\r\n','method'=>'POST','content'=>http_build_query(array('fun'=>$cm,'node'=>'node'.$node)),),)));if($result=='OK')break;sleep(1);}return $result;}
function ControllerBusy($retries){for($k=1;$k<=$retries;$k++){$result=file_get_contents('http://127.0.0.1:8084/ozwcp/poll.xml');$p=xml_parser_create();xml_parse_into_struct($p,$result,$vals,$index);xml_parser_free($p);foreach($vals as $val){if($val['tag']=='ADMIN'){$result=$val['attributes']['ACTIVE'];break;}}if($result=='false')break;if($k==$retries){ZwaveCommand(1,'Cancel');break;}sleep(1);}}
function convertToHours($time){if($time<600)return substr(strftime('%M:%S',$time),1);elseif($time>=600&&$time<3600)return strftime('%M:%S',$time);else return strftime('%k:%M:%S',$time);}
function ex($x){
	$return=array();
	$pieces=explode('#',$x);
	foreach($pieces as $piece){
		$keyval=explode('|',$piece);
		if(count($keyval)>1)$return[$keyval[0]]=$keyval[1];
		else $return[$keyval[0]]='';
	}
	return $return;
}
function endswith($string, $test) {
    $strlen = strlen($string);
    $testlen = strlen($test);
    if ($testlen > $strlen) return false;
    return substr_compare($string, $test, $strlen - $testlen, $testlen) === 0;
}
