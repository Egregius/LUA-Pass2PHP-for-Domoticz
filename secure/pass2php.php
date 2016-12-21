<?php
error_reporting(E_ALL);ini_set("display_errors","on");
//CONFIG
define('domoticz','http://127.0.0.1:8084/');
define('denon','http://192.168.2.4/');
define('kodi','http://192.168.2.7:1597/');
define('appledevice','123FIdWFh123REeBN1nZk0sD/ZHxYptWlD4zoKvGC1VYH123kSRqABCDEFGHzmWV');
define('appleid','your@applelogin.com');
define('applepass','Your-Apple-id-Password');
define('smsactive',false);
define('smsuser','Clickateluser');
define('smspassword','Clickatelpassword');
define('smsapi',1234567);
define('smstofrom',32123456798);
define('telegrambot','123456789:ABCD-xCRhO-abcdefghi_3YIr9irxI');
define('telegramchatid',12345678);
define('telegramchatid2',23456789);
define('cache','apcu');//apcu apc memcached
define('logging',false);//apcu apc memcached
$ctx=stream_context_create(array('http'=>array('timeout'=>3)));
$weer=unserialize(cget('weer'));
//Set time, decode and call pass2php/'updateddevice'.php
date_default_timezone_set('Europe/Brussels');define('time',$_SERVER['REQUEST_TIME']);
$c=ex($_REQUEST['c']);
$s=ex($_REQUEST['s']);
$i=ex($_REQUEST['i']);
$t=ex($_REQUEST['t']);
if(file_exists('pass2php/'.key($c).'.php'))include 'pass2php/'.key($c).'.php';
//USER FUNCTIONS
function alarm($naam,$slapen=true){global $s,$i,$t;if(($s['weg']=='On'||($s['slapen']=='On'&&$slapen==true))&&$s['meldingen']=='On'&&strtotime($t['weg'])<time-178&&strtotime($t['slapen'])<time-178){if(cget('timealert'.$naam)<time-57){cset('timealert'.$naam,time);sw($i['sirene'],'On');$msg='Beweging '.$naam.' om '.strftime("%H:%M:%S",time);telegram($msg,false);ios($msg);}}}
function alles($action,$uit=0){global $s,$i,$t;if($action=='On'){$items=array('eettafel','zithoek','kamer','tobi');foreach($items as $item)if($s[$item]!='On')sl($i[$item],100,$item);$items=array('bureel','tvled','kristal','wasbak','keuken','kookplaat','werkblad','inkom','hall','lichtbadkamer1');foreach($items as $item)if($s[$item]!='On')sw($i[$item],'On',$item);}elseif($action=='Off'){$items=array('denon','bureel','tv','tvled','kristal','eettafel','zithoek','garage','terras','tuin','voordeur','keuken','werkblad','wasbak','kookplaat','sony','kamer','tobi','alex');foreach($items as $item)if($s[$item]!='Off'&&strtotime($t[$item])<time-$uit)sw($i[$item],'Off',$item);$items=array('lichtbadkamer1','lichtbadkamer2','badkamervuur');foreach($items as $item)if($s[$item]!='Off'&&strtotime($t[$item])<time-$uit)sw($i[$item],'Off',$item);}elseif($action=='Slapen'){$items=array('hall','bureel','denon','tv','tvled','kristal','eettafel','zithoek','garage','terras','tuin','voordeur','keuken','werkblad','wasbak','kookplaat');foreach($items as $item)if($s[$item]!='Off')sw($i[$item],'Off',$item);$items=array('pirkeuken','pirgarage','pirinkom','pirhall');foreach($items as $item)if($s[$item]!='Off')ud($i[$item],0,'Off');}}
function SD($naam){global $i;$msg='Rook gedecteerd bij '.$naam.'!';telegram($msg,false,'Kirby');ios($msg);resetsecurity($i[$naam],$naam);}
//GLOBAL FUNCTIONS
function sw($idx,$action='',$info=''){
	if(logging==true)lg('SWITCH '.$action.' '.$info);
	if(empty($action))curl(domoticz.'json.htm?type=command&param=switchlight&idx='.$idx.'&switchcmd=Toggle');
	else curl(domoticz.'json.htm?type=command&param=switchlight&idx='.$idx.'&switchcmd='.$action);
}
function sl($idx,$level,$info=''){
	if(logging==true)lg('SETLEVEL '.$level.' '.$info);
	curl(domoticz.'json.htm?type=command&param=switchlight&idx='.$idx.'&switchcmd=Set%20Level&level='.$level);
}
function ud($idx,$nvalue,$svalue,$info=""){
date_default_timezone_set('Europe/Brussels');define('time',$_SERVER['REQUEST_TIME']);
$c=ex($_REQUEST['c']);
$s=ex($_REQUEST['s']);
$i=ex($_REQUEST['i']);
$t=ex($_REQUEST['t']);
foreach($c as $device=>$status)
	if(!empty($status))
		if(false!==stream_resolve_include_path('pass2php/'.$device.'.php'))include 'pass2php/'.$device.'.php';
include 'pass2php/__CRON.php';

//USER FUNCTIONS
function alarm($naam,$slapen=true){
	global $s,$i,$t;
	if(($s['weg']=='On'||($s['slapen']=='On'&&$slapen==true))&&$s['meldingen']=='On'&&strtotime($t['weg'])<time-178&&strtotime($t['slapen'])<time-178){
		if(cget('timealert'.$naam)<time-57){
			cset('timealert'.$naam,time);
			sw($i['sirene'],'On');
			$msg='Beweging '.$naam.' om '.strftime("%H:%M:%S",time);
			telegram($msg,false);
			ios($msg);
		}
	}
}
function alles($action,$uit=0){
	global $s,$i,$t;
	if($action=='On'){
		$items=array('eettafel','zithoek','kamer','tobi');foreach($items as $item)if($s[$item]!='On')sl($i[$item],100,$item);
		$items=array('bureel','tvled','kristal','wasbak','keuken','kookplaat','werkblad','inkom','hall','lichtbadkamer1');foreach($items as $item)if($s[$item]!='On')sw($i[$item],'On',$item);
	}elseif($action=='Off'){
		$items=array('denon','bureel','tv','tvled','kristal','eettafel','zithoek','garage','terras','tuin','voordeur','keuken','werkblad','wasbak','kookplaat','sony','kamer','tobi','alex');foreach($items as $item)if($s[$item]!='Off'&&strtotime($t[$item])<time-$uit)sw($i[$item],'Off',$item);
		$items=array('lichtbadkamer1','lichtbadkamer2','badkamervuur');foreach($items as $item)if($s[$item]!='Off'&&strtotime($t[$item])<time-$uit)sw($i[$item],'Off',$item);
	}elseif($action=='Slapen'){
		$items=array('hall','bureel','denon','tv','tvled','kristal','eettafel','zithoek','garage','terras','tuin','voordeur','keuken','werkblad','wasbak','kookplaat');foreach($items as $item)if($s[$item]!='Off')sw($i[$item],'Off',$item);
		$items=array('pirkeuken','pirgarage','pirinkom','pirhall');foreach($items as $item)if($s[$item]!='Off')ud($i[$item],0,'Off');
	}
}
function SD($naam){
	global $i;
	$msg='Rook gedecteerd bij '.$naam.'!';
	telegram($msg,false,2);
	ios($msg);
	resetsecurity($i[$naam],$naam);
}
//GLOBAL FUNCTIONS
function sw($idx,$action='',$info=''){
	if(logging==true)lg('SWITCH '.$action.' '.$info);
	if(empty($action))curl(domoticz.'json.htm?type=command&param=switchlight&idx='.$idx.'&switchcmd=Toggle');
	else curl(domoticz.'json.htm?type=command&param=switchlight&idx='.$idx.'&switchcmd='.$action);
}
function sl($idx,$level,$info=''){
	if(logging==true)lg('SETLEVEL '.$level.' '.$info);
	curl(domoticz.'json.htm?type=command&param=switchlight&idx='.$idx.'&switchcmd=Set%20Level&level='.$level);
}
function ud($idx,$nvalue,$svalue,$info=""){
	if(logging==true)if(!in_array($idx, array(395,532,534)))lg("UPDATE ".$nvalue." ".$svalue." ".$info);
	curl(domoticz.'json.htm?type=command&param=udevice&idx='.$idx.'&nvalue='.$nvalue.'&svalue='.$svalue);
}
function setradiator($name,$dif,$koudst=false,$set){
	$setpoint=$set-ceil($dif*4);
	if($koudst==true)$setpoint=28.0;
	if($setpoint>28)$setpoint=28.0;elseif($setpoint<4)$setpoint=4.0;
	return round($setpoint,0).".0";
}
function double($idx,$action,$comment='',$wait=1500000){
	sw($idx,$action,$comment);
	usleep($wait);
	sw($idx,$action,$comment.' repeat',0);
}
function telegram($msg,$silent=true,$to=1){
	for($x=1;$x<=100;$x++){
		$result=json_decode(curl('https://api.telegram.org/bot'.telegrambot.'/sendMessage?chat_id='.telegramchatid.'&text='.$msg.'&disable_notification='.$silent,true));
		if(isset($result->ok))
			if($result->ok===true){lg('telegram sent to 1: '.$msg);break;}
			else lg('telegram sent failed');sleep($x*3);
	}
	if($to==2)
		for($x=1;$x<=100;$x++){
			$result=json_decode(curl('https://api.telegram.org/bot'.telegrambot.'/sendMessage?chat_id='.telegramchatid2.'&text='.$msg.'&disable_notification='.$silent,true));
			if(isset($result->ok))
				if($result->ok===true){lg('telegram sent to 2: '.$msg);break;}
				else lg('telegram sent failed');sleep($x*3);
		}
}
function lg($msg){curl(domoticz.'json.htm?type=command&param=addlogmessage&message='.urlencode('--->> '.$msg));}
function ios($msg){require_once('findmyiphone.php');$fmi=new FindMyiPhone(appleid,applepass);$fmi->playSound(appledevice,$msg);sms($msg);}
function sms($msg){if(smsactive===true){curl('http://api.clickatell.com/http/sendmsg?user='.smsuser.'&password='.smspassword.'&api_id='.smsapi.'&to='.smstofrom.'&text='.urlencode($msg).'&from='.smstofrom.'');}}
function checkport($ip,$port){if(pingport($ip,$port)==1){$prevcheck=cget('check'.$ip.':'.$port);if($prevcheck>=3)telegram($ip.':'.$port.' online',true);if($prevcheck>0)cset('check'.$ip.':'.$port,0);}else{$check=cget('check'.$ip.':'.$port)+1;if($check>0)cset('check'.$ip.':'.$port,$check);if($check==3)telegram($ip.':'.$port.' Offline',true);if($check%12==0)telegram($ip.':'.$port.' nog steeds Offline',true);}}
function pingport($ip,$port){$file=fsockopen($ip,$port,$errno,$errstr,10);$status=0;if(!$file)$status=-1;else{fclose($file);$status=1;}return $status;}
function RefreshZwave($node){
	$last=cget('refresh'.$node);
	cset('refresh'.$node,time);
	if($last<time-3600){
		$devices=json_decode(file_get_contents(domoticz.'json.htm?type=openzwavenodes&idx=3'),true);
		foreach($devices['result'] as $devozw)
			if($devozw['NodeID']==$node){
				$device=$devozw['Description'].' '.$devozw['Name'];
				break;
			}
		lg(' > Refreshing node '.$node.' '.$device);
		for($k=1;$k<=5;$k++){
			$result=file_get_contents(domoticz.'ozwcp/refreshpost.html',false,stream_context_create(array('http'=>array('header'=>'Content-Type: application/x-www-form-urlencoded\r\n','method'=>'POST','content'=>http_build_query(array('fun'=>'racp','node'=>$node)),),)));
			if($result==='OK')break;
			sleep(1);
		}
		/*if(cget('timedeadnodes')<time-298){cset('timedeadnodes',time);foreach($devices as $node=>$data){if($node=="result"){foreach($data as $index=>$eltsNode){if($eltsNode["State"]=="Dead"&&!in_array($eltsNode['NodeID'],array(57))){telegram('Node '.$eltsNode['NodeID'].' '.$eltsNode['Description'].' ('.$eltsNode['Name'].') marked as dead, reviving '.ZwaveCommand($eltsNode['NodeID'],'HasNodeFailed'));ControllerBusy(10);ZwaveCommand(1,'Cancel');}}}}}*/
	}
}
function Zwavecancelaction(){file_get_contents(domoticz.'ozwcp/admpost.html',false,stream_context_create(array('http'=>array('header'=>'Content-Type: application/x-www-form-urlencoded\r\n','method'=>'POST','content'=>http_build_query(array('fun'=>'cancel')),),)));}
function ZwaveCommand($node,$command){$cm=array('AssignReturnRoute'=>'assrr','DeleteAllReturnRoutes'=>'delarr','NodeNeighbourUpdate'=>'reqnnu','RefreshNodeInformation'=>'refreshnode','RequestNetworkUpdate'=>'reqnu','HasNodeFailed'=>'hnf','Cancel'=>'cancel');$cm=$cm[$command];for($k=1;$k<=5;$k++){$result=file_get_contents(domoticz.'ozwcp/admpost.html',false,stream_context_create(array('http'=>array('header'=>'Content-Type: application/x-www-form-urlencoded\r\n','method'=>'POST','content'=>http_build_query(array('fun'=>$cm,'node'=>'node'.$node)),),)));if($result=='OK')break;sleep(1);}return $result;}
function ControllerBusy($retries){for($k=1;$k<=$retries;$k++){$result=file_get_contents(domoticz.'ozwcp/poll.xml');$p=xml_parser_create();xml_parse_into_struct($p,$result,$vals,$index);xml_parser_free($p);foreach($vals as $val){if($val['tag']=='ADMIN'){$result=$val['attributes']['ACTIVE'];break;}}if($result=='false')break;if($k==$retries){ZwaveCommand(1,'Cancel');break;}sleep(1);}}
function convertToHours($time){if($time<600)return substr(strftime('%M:%S',$time),1);elseif($time>=600&&$time<3600)return strftime('%M:%S',$time);else return strftime('%k:%M:%S',$time);}
function curl($url){$headers=array('Content-Type: application/json');$ch=curl_init();curl_setopt($ch,CURLOPT_URL,$url);curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);curl_setopt($ch,CURLOPT_FRESH_CONNECT,TRUE);curl_setopt($ch,CURLOPT_TIMEOUT,5);$data=curl_exec($ch);curl_close($ch);return $data;}
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
function cset($key,$value){
	if(cache=='apcu'){
		apcu_store($key,$value);
	}elseif(cache=='apc'){
		apc_store($key,$value);
	}elseif(cache=='memcached'){
		if(!$m=xsMemcached::Connect('127.0.0.1',11211)){return;}$m->Set($key,$value);
	}
}
function cget($key){
	if(cache=='apcu'){
		return apcu_fetch($key);
	}elseif(cache=='apc'){
		return apc_fetch($key);
	}elseif(cache=='memcached'){
		if(!$m=xsMemcached::Connect('127.0.0.1',11211)){return 0;}return $m->Get($key);
	}
}
class xsMemcached{private $Host;private $Port;private $Handle;public static function Connect($Host,$Port,$Timeout=5){$Ret=new self();$Ret->Host=$Host;$Ret->Port=$Port;$ErrNo=$ErrMsg=NULL;if(!$Ret->Handle=@fsockopen($Ret->Host,$Ret->Port,$ErrNo,$ErrMsg,$Timeout))return false;return $Ret;}public function Set($Key,$Value,$TTL=0){return $this->SetOp($Key,$Value,$TTL,'set');}public function Get($Key){$this->WriteLine('get '.$Key);$Ret='';$Header=$this->ReadLine();if($Header=='END'){$Ret=0;$this->SetOp($Key,0,0,'set');return $Ret;}while(($Line=$this->ReadLine())!='END')$Ret.=$Line;if($Ret=='')return false;$Header=explode(' ',$Header);if($Header[0]!='VALUE'||$Header[1]!=$Key)throw new Exception('unexcpected response format');$Meta=$Header[2];$Len=$Header[3];return $Ret;}public function Quit(){$this->WriteLine('quit');}private function SetOp($Key,$Value,$TTL,$Op){$this->WriteLine($Op.' '.$Key.' 0 '.$TTL.' '.strlen($Value));$this->WriteLine($Value);return $this->ReadLine()=='STORED';}private function WriteLine($Command,$Response=false){fwrite($this->Handle,$Command."\r\n");if($Response)return $this->ReadLine();return true;}private function ReadLine(){return rtrim(fgets($this->Handle),"\r\n");}private function __construct(){}}
