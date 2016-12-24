<?php $start=microtime(true);error_reporting(E_ALL);ini_set("display_errors","on");date_default_timezone_set('Europe/Brussels');
define('time',$_SERVER['REQUEST_TIME']);$actions=0;
$c=ex($_REQUEST['c']);$s=ex($_REQUEST['s']);
foreach($c as $device=>$status)
	if(@include '/volume1/web/secure/pass2php/'.$device.'.php'){apcu_store('t'.$device,time);$dev=$device;}
$split=microtime(true);
if(!isset($dev))die();
include '/volume1/web/secure/pass2php/__CRON.php';
function sw($idx,$action='',$info=''){
	//lg('SWITCH '.$idx.' '.$action.' '.$info);
	if(empty($action))file_get_contents('http://127.0.0.1:8084/json.htm?type=command&param=switchlight&idx='.$idx.'&switchcmd=Toggle');
	else file_get_contents('http://127.0.0.1:8084/json.htm?type=command&param=switchlight&idx='.$idx.'&switchcmd='.$action);
	global $actions;$actions=$actions+1;
}
function double($idx,$action,$comment='',$wait=1500000){
	sw($idx,$action,$comment);
	usleep($wait);
	sw($idx,$action,$comment.' repeat',0);
	global $actions;$actions=$actions+2;
}
function sl($idx,$level,$info=''){
	//lg('SETLEVEL '.$idx.' '.$level.' '.$info);
	file_get_contents('http://127.0.0.1:8084/json.htm?type=command&param=switchlight&idx='.$idx.'&switchcmd=Set%20Level&level='.$level);
}
function ud($idx,$nvalue,$svalue,$info=""){
	if(!in_array($idx, array(395,532,534)))lg("UPDATE ".$idx." ".$nvalue." ".$svalue." ".$info);
	file_get_contents('http://127.0.0.1:8084/json.htm?type=command&param=udevice&idx='.$idx.'&nvalue='.$nvalue.'&svalue='.$svalue);
	global $actions;$actions=$actions+1;
}
function telegram($msg,$silent=true,$to=1){
	$telegrambot='123456789:AAEZ-xCRhO-ABCDEFCiJs8q9A_3YIr9irxI';
	$telegramchatid=123456789;
	$telegramchatid2=123456789;
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
function lg($msg){file_get_contents('http://127.0.0.1:8084/json.htm?type=command&param=addlogmessage&message='.urlencode('--->> '.$msg));}
function ios($msg){
	$appledevice='1234567890AZERTYUIOP/ZHxYptWlD4zoKvGC1VYH805kSRqROHYVNSUzmWV';
	$appleid='you@me.com';
	$applepass='myP@sw0rd';
	require_once('findmyiphone.php');
	$fmi=new FindMyiPhone(appleid,applepass);
	$fmi->playSound(appledevice,$msg);
	sms($msg);
}
function sms($msg){
	exit;
	$smsuser='clickatelluser';
	$smspassword='clickatellpassword';
	$smsapi=123456789;
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
		//lg(' > Refreshing node '.$node.' '.$device);
		for($k=1;$k<=5;$k++){
			$result=file_get_contents('http://127.0.0.1:8084/ozwcp/refreshpost.html',false,stream_context_create(array('http'=>array('header'=>'Content-Type: application/x-www-form-urlencoded\r\n','method'=>'POST','content'=>http_build_query(array('fun'=>'racp','node'=>$node)),),)));
			if($result==='OK')break;
			sleep(1);
		}
		/*if(apcu_fetch('timedeadnodes')<time-298){apcu_store('timedeadnodes',time);foreach($devices as $node=>$data){if($node=="result"){foreach($data as $index=>$eltsNode){if($eltsNode["State"]=="Dead"&&!in_array($eltsNode['NodeID'],array(57))){telegram('Node '.$eltsNode['NodeID'].' '.$eltsNode['Description'].' ('.$eltsNode['Name'].') marked as dead, reviving '.ZwaveCommand($eltsNode['NodeID'],'HasNodeFailed'));ControllerBusy(10);ZwaveCommand(1,'Cancel');}}}}}*/
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
//$msg='execution '.$dev.' = '.number_format((($split-$start)*1000),3,'.','').' + '.number_format(((microtime(true)-$split)*1000),3,'.','').' msec for cron'.$cron;if($actions>0)$msg.=' + '.$actions.' actions';lg($msg);
