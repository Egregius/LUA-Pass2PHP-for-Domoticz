<?php
function Blinds($name){
	${'i'.$name}=idx($name);
	${'s'.$name}=status($name);
	echo '
	<div class="fix z '.$name.'">
		<form method="POST">
			<input type="hidden" name="Schakel" value="'.${'i'.$name}.'"/>
			<input type="hidden" name="Naam" value="'.$name.'"/>
			<input type="hidden" name="Actie" value="Off"/>';
	echo ${'s'.$name}=='Closed'
		?'<input type="image" src="/images/arrowgreenup.png" class="i48"/>'
		:'<input type="image" src="/images/arrowup.png" class="i48"/>';
	echo '
		</form><br/>
	<form method="POST">
		<input type="hidden" name="Schakel" value="'.${'i'.$name}.'"/>
		<input type="hidden" name="Naam" value="'.$name.'"/>
		<input type="hidden" name="Actie" value="On"/>';
	echo ${'s'.$name}=='Open'
		?'<input type="image" src="/images/arrowgreendown.png" class="i48"/>'
		:'<input type="image" src="/images/arrowdown.png" class="i48"/>';
	echo '
	</form>
</div>';
}
function Dimmer($name){
	$stat=status($name);
	echo '
	<form method="POST">
		<a href="floorplan.php?setdimmer='.$name.'">
		<div class="fix z '.$name.'">
			<input type="hidden" name="setdimmer" value="'.$name.'"/>';
	if($stat=='Off'|$stat=='')echo '
			<input type="image" src="/images/Light_Off.png" class="i70"/>';
	else echo'
			<input type="image" src="/images/Light_On.png" class="i70"/>
			<div class="fix center dimmerlevel">
				'.$stat.'
			</div>';
	echo '
		</div>
		</a>
	</form>';
}
function idx($name){if(file_exists('/var/log/cache/i'.$name.'.cache'))return file_get_contents('/var/log/cache/i'.$name.'.cache');else return 0;}
function Schakelaar($name,$kind){
	$stat=status($name);
	echo '
	<div class="fix '.$name.'">
		<form method="POST">
			<input type="hidden" name="Naam" value="'.$name.'"/>';
	echo $stat=='Off'?'
			<input type="hidden" name="Actie" value="On"/>
			<input type="image" src="/images/'.$kind.'_Off.png" id="'.$name.'"/>'
		:'
			<input type="hidden" name="Actie" value="Off">
			<input type="image" src="/images/'.$kind.'_On.png" id="'.$name.'"/>';
	echo '
		</form>
	</div>';
}
function Schakelaar2($name,$kind){
	$stat=status(''.$name);
	$idx=idx(''.$name);
	echo '<div class="fix z1 center '.$name.'">
	<form method="POST"><input type="hidden" name="Schakel" value="'.$idx.'">';
	echo $stat=='Off'?'<input type="hidden" name="Actie" value="On"><input type="hidden" name="Naam" value="'.$name.'"><input type="image" src="/images/'.$kind.'_Off.png" class="i48"/>'
				   :'<input type="hidden" name="Actie" value="Off"><input type="hidden" name="Naam" value="'.$name.'"><input type="image" src="/images/'.$kind.'_On.png" class="i48"/>';
	echo '<br/>'.$name.'</form></div>';
}
function setidx($name,$value){file_put_contents('/var/log/cache/i'.$name.'.cache',$value);}
function setstatus($name,$value){file_put_contents('/var/log/cache/s'.$name.'.cache',$value);}
function settimestamp($name,$stamp=time){touch('/var/log/cache/s'.$name.'.cache',$stamp);}
function sl($name,$level){
	global $user;
	lg($user.' =>	Dimmer '.$name.'	'.$level);
	if($level>0&&$level<100)
		$level=$level+1;
	file_get_contents('http://127.0.0.1:8080/json.htm?type=command&param=switchlight&idx='.idx($name).'&switchcmd=Set%20Level&level='.$level);
	usleep(125000);
}
function status($name){if(file_exists('/var/log/cache/s'.$name.'.cache'))return file_get_contents('/var/log/cache/s'.$name.'.cache');else return 0;}
function sw($name,$action='Toggle',$comment=''){
	global $user;

	if(is_array($name)){
		foreach($name as $i){
			if($i=='media')sw(array('tv','denon','tvled','kristal'),$action);
			elseif($i=='lichtenbeneden')sw(array('pirgarage','pirkeuken','pirliving','pirinkom','eettafel','zithoek','tvled','kristal','bureel','garage','terras','tuin','keuken','werkblad','wasbook','kookplaat','inkom','zolderg'),$action);
			elseif($i=='lichtenboven')sw(array('pirhall','lichtbadkamer1','lichtbadkamer2','kamer','tobi','alex','hall','zolder'),$action);
			elseif($i=='slapen')sw(array('pirhall','hall','lichtenbeneden','dampkap','GroheRed'),$action);
			elseif($i=='weg')sw(array('slapen','lichtenbeneden','lichtenboven'),$action);
			else{if(status(''.$i)!=$action)sw($i,$action);}
		}
	}else{
		$idx=idx($name);
		$msg=$user.' => '.$name.' => '.$action;
		if(!empty($comment))$msg.=' | '.$comment;
		lg($msg);
		if($idx>0){
			file_get_contents('http://127.0.0.1:8080/json.htm?type=command&param=switchlight&idx='.$idx.'&switchcmd='.$action);
			usleep(125000);
		}else{
			setstatus($name,$action);
		}
	}
}
function timestamp($name){if(file_exists('/var/log/cache/s'.$name.'.cache'))return filemtime('/var/log/cache/s'.$name.'.cache');else return 0;}
function Thermometer($name){
	$temp=status($name);
	$hoogte=$temp*3;
	if($hoogte>88)$hoogte=88;
	elseif($hoogte<20)$hoogte=20;
	$top=88-$hoogte;
	if($top<0)$top=0;
	$top=$top+5;
	if($temp>=22){$tcolor='F00';$dcolor='55F';}
	elseif($temp>=20){$tcolor='D12';$dcolor='44F';}
	elseif($temp>=18){$tcolor='B24';$dcolor='33F';}
	elseif($temp>=15){$tcolor='93B';$dcolor='22F';}
	elseif($temp>=10){$tcolor='64D';$dcolor='11F';}
	else{$tcolor='55F';$dcolor='00F';}

	echo '
	<a href=\'javascript:navigator_Go("temp.php?sensor=998");\'>
		<div class="fix '.$name.'" >
			<div class="fix tmpbg" style="top:'.number_format($top,0).'px;left:8px;height:'.number_format($hoogte,0).'px;background:linear-gradient(to bottom, #'.$tcolor.', #'.$dcolor.');">
			</div>
			<img src="/images/temp.png" height="100px" width="auto"/>
			<div class="fix center" style="top:73px;left:5px;width:30px;">
				'.number_format($temp,1).'
			</div>
		</div>
	</a>';
}
function ud($name,$nvalue,$svalue,$info=""){
	$idx=idx($name);
	if($idx>0){
		file_get_contents('http://127.0.0.1:8080/json.htm?type=command&param=udevice&idx='.$idx.'&nvalue='.$nvalue.'&svalue='.$svalue);
		usleep(125000);
	}else{
		setstatus($name,$svalue);
	}
}



function showTimestamp($name,$draai){
	$stamp=timestamp(''.$name);
	if(empty($stamp))return;
	echo '<div class="fix stamp r'.$draai.' t'.$name.'">'.strftime("%k:%M",$stamp).'</div>';
}
function Secured($name){echo '<div class="fix secured '.$name.'"></div>';}
function Motion($name){echo '<div class="fix motion '.$name.'"></div>';}

function Luifel($name){
	$stat=status('luifel');
	echo '
	<form method="POST">
		<a href=\'javascript:navigator_Go("floorplan.php?luifel=true");\'>
		<div class="fix z '.$name.'">
			<input type="hidden" name="luifel" value="true"/>';
	echo $stat==100?'
			<input type="image" src="/images/arrowup.png" class="i70"/>'
	:'
			<input type="image" src="/images/arrowdown.png" class="i70"/>
			<div class="fix center dimmerlevel" style="position:absolute;top:10px;left:3px;width:70px;letter-spacing:4;"><font size="5" color="#CCC">
				'. (100 - $stat) .'</font>
			</div>';
	echo '
		</div>
		</a>
	</form>';
}
function Zwavecancelaction(){file_get_contents('http://192.168.2.2:8080/ozwcp/admpost.html',false,stream_context_create(array('http'=>array('header'=>'Content-Type: application/x-www-form-urlencoded\r\n','method'=>'POST','content'=>http_build_query(array('fun'=>'cancel')),),)));}
function ZwaveCommand($node,$command){$cm=array('AssignReturnRoute'=>'assrr','DeleteAllReturnRoutes'=>'delarr','NodeNeighbourUpdate'=>'reqnnu','RefreshNodeInformation'=>'refreshnode','RequestNetworkUpdate'=>'reqnu','HasNodeFailed'=>'hnf','Cancel'=>'cancel');$cm=$cm[$command];for($k=1;$k<=5;$k++){$result=file_get_contents('http://192.168.2.2:8080/ozwcp/admpost.html',false,stream_context_create(array('http'=>array('header'=>'Content-Type: application/x-www-form-urlencoded\r\n','method'=>'POST','content'=>http_build_query(array('fun'=>$cm,'node'=>'node'.$node)),),)));if($result=='OK')break;sleep(1);}return $result;}
function ControllerBusy($retries){for($k=1;$k<=$retries;$k++){$result=file_get_contents('http://192.168.2.2:8080/ozwcp/poll.xml');$p=xml_parser_create();xml_parse_into_struct($p,$result,$vals,$index);xml_parser_free($p);foreach($vals as $val){if($val['tag']=='ADMIN'){$result=$val['attributes']['ACTIVE'];break;}}if($result=='false')break;if($k==$retries){ZwaveCommand(1,'Cancel');break;}sleep(1);}}
function convertToHours($time){if($time<600)return substr(strftime('%M:%S',$time),1);elseif($time>=600&&$time<3600)return strftime('%M:%S',$time);else return strftime('%k:%M:%S',$time);}
function endswith($string,$test){$strlen=strlen($string);$testlen=strlen($test);if($testlen>$strlen) return false;return substr_compare($string,$test,$strlen-$testlen,$testlen)===0;}
function checkport($ip,$port){
	if(pingport($ip,$port)==1){
		$prevcheck=status($ip.':'.$port);
		if($prevcheck>=3)telegram($ip.':'.$port.' online',true);
		if($prevcheck>0)setstatus($ip.':'.$port,0);
	}else{
		$check=status($ip.':'.$port)+1;
		if($check>0)setstatus($ip.':'.$port,$check);
		if($check==3)telegram($ip.':'.$port.' Offline',true);
		if($check%120==0)telegram($ip.':'.$port.' nog steeds Offline',true);
	}
}
function pingport($ip,$port){$file=@fsockopen($ip,$port,$errno,$errstr,10);$status=0;if(!$file)$status=-1;else{fclose($file);$status=1;}return $status;}
function double($name,$action,$comment='',$wait=2000000){sw($name,$action,$comment);usleep($wait);sw($name,$action,$comment.' repeat');}
function RefreshZwave($node){
	$last=timestamp('refresh'.$node);
	settimestamp('refresh'.$node);
	if($last<time-3600){
		$devices=json_decode(file_get_contents('http://192.168.2.2:8080/json.htm?type=openzwavenodes&idx=3',false),true);
		foreach($devices['result'] as $devozw)
			if($devozw['NodeID']==$node){
				$device=$devozw['Description'].' '.$devozw['Name'];
				break;
			}
		if(!isset($device))exit;
		for($k=1;$k<=5;$k++){
			$result=file_get_contents('http://192.168.2.2:8080/ozwcp/refreshpost.html',false,stream_context_create(array('http'=>array('header'=>'Content-Type: application/x-www-form-urlencoded\r\n','method'=>'POST','content'=>http_build_query(array('fun'=>'racp','node'=>$node)),),)));
			if($result==='OK')break;
			sleep(1);
		}
		/*if(timestamp('timedeadnodes')<time-298){
			settimestamp('timedeadnodes');
			foreach($devices as $node=>$data){
				if($node=="result"){
					foreach($data as $index=>$eltsNode){
						if($eltsNode["State"]=="Dead"&&!in_array($eltsNode['NodeID'],array(57))){
							$msg='Node '.$eltsNode['NodeID'].' '.$eltsNode['Description'].' ('.$eltsNode['Name'].') marked as dead, reviving '.ZwaveCommand($eltsNode['NodeID'],'HasNodeFailed');
							lg($msg);
							telegram($msg);
							ControllerBusy(10);
							ZwaveCommand(1,'Cancel');
						}
					}
				}
			}
		}*/
	}
}
function koekje($user,$expirytime){
	setcookie("HomeEgregius2",$user,$expirytime,'/');
}
function telegram($msg,$silent=true,$to=1){
	$msg=str_replace('__',PHP_EOL,$msg);
	global $telegrambot,$telegramchatid,$telegramchatid2;
	for($x=1;$x<=10;$x++){
		$result=json_decode(file_get_contents('https://api.telegram.org/bot'.$telegrambot.'/sendMessage?chat_id='.$telegramchatid.'&text='.urlencode($msg).'&disable_notification='.$silent),true);
		if(isset($result['ok']))
			if($result['ok']===true){lg('telegram sent to 1: '.$msg);break;}
			else {lg('telegram sent failed');sleep($x*3);}
	}
	if($to==2)
		for($x=1;$x<=10;$x++){
			$result=json_decode(file_get_contents('https://api.telegram.org/bot'.$telegrambot.'/sendMessage?chat_id='.$telegramchatid2.'&text='.$msg.'&disable_notification='.$silent,true));
			if(isset($result['ok']))
				if($result['ok']===true){lg('telegram sent to 2: '.$msg);break;}
				else lg('telegram sent failed');sleep($x*3);
		}
}
function lg($msg){
	$fp=fopen('/var/log/floorplanlog.log',"a+");
	$time=microtime(true);
	$dFormat="Y-m-d H:i:s";
	$mSecs=$time-floor($time);
	$mSecs=substr(number_format($mSecs,3),1);
	fwrite($fp,sprintf("%s%s %s\n",date($dFormat),$mSecs,$msg));
	fclose($fp);
}
function logwrite($msg,$msg2=NULL){
	global $LogFile;
	$time=microtime(true);
	$dFormat="Y-m-d H:i:s";
	$mSecs=$time-floor($time);
	$mSecs=substr(number_format($mSecs,3),1);
	$fp=fopen($LogFile,"a+");
	fwrite($fp,sprintf("%s%s %s %s\n",date($dFormat),$mSecs,$msg,$msg2));
	fclose($fp);
}
function fail2ban($ip){
	$time=microtime(true);$dFormat="Y-m-d H:i:s";$mSecs=$time-floor($time);$mSecs=substr(number_format($mSecs,3),1);
	$fp=fopen('/var/log/home2ban.log',"a+");
	fwrite($fp,sprintf("%s %s\n",date($dFormat),$ip));
	fclose($fp);
}
function pingDomain($domain,$port){$file=&fsockopen($domain,$port,$errno,$errstr,1);$status=0;if(!$file)$status=-1;else{fclose($file);$status=1;}return $status;}
function startsWith($haystack,$needle){return $needle===""||strrpos($haystack,$needle,-strlen($haystack))!==false;}
?>
