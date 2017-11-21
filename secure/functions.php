<?php
function Blinds($name){
	${'i'.$name}=idx($name);
	${'s'.$name}=apcu_fetch($name);
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
	$stat=apcu_fetch($name);
	echo '
	<form method="POST">
		<a href="floorplan.php?setdimmer='.$name.'">
		<div class="fix z '.$name.'">
			<input type="hidden" name="setdimmer" value="'.$name.'"/>';
	if($stat==0|$stat=='')echo '
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
function idx($name){return apcu_fetch('i'.$name);}
function Schakelaar($name,$kind){
	$stat=apcu_fetch($name);
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
	global $eendag;
	$stat=apcu_fetch(''.$name);
	$idx=idx(''.$name);
	echo '<div class="fix z1 center '.$name.'" style="width:70px;">
	<form method="POST"><input type="hidden" name="Schakel" value="'.$idx.'">';
	echo $stat=='Off'?'<input type="hidden" name="Actie" value="On"><input type="hidden" name="Naam" value="'.$name.'"><input type="image" src="/images/'.$kind.'_Off.png" class="i40"/>'
				   :'<input type="hidden" name="Actie" value="Off"><input type="hidden" name="Naam" value="'.$name.'"><input type="image" src="/images/'.$kind.'_On.png" class="i40"/>';
	echo '<br/>'.$name;
	$timestamp=timestamp($name);
	if($timestamp>$eendag)echo '<div class="fix center" style="top:52px;left:0px;width:70px;">'.strftime("%H:%M",$timestamp).'</div>';
	echo '</form></div>';
}
function setidx($name,$value){apcu_store('i'.$name,$value);}
function setstatus($name,$value){apcu_store($name,$value);apcu_store('T'.$name,time());}
function settimestamp($name){apcu_store('T'.$name,time());}
function sl($name,$level,$type='Dimmer',$check=false){
	global $user;
	$idx=idx($name);
	lg($user.' =>	'.$type.' '.$name.'	'.$level);
	if($type=='Dimmer'){
		if($level>0&&$level<100)
			$level=$level+1;
	}
	if($idx>0){
		if($check==false)file_get_contents('http://127.0.0.1:8080/json.htm?type=command&param=switchlight&idx='.$idx.'&switchcmd=Set%20Level&level='.$level);
		else{
			if(apcu_fetch($name)!=$$level)file_get_contents('http://127.0.0.1:8080/json.htm?type=command&param=switchlight&idx='.$idx.'&switchcmd=Set%20Level&level='.$level);
		}
	}else{
		setstatus($name,$level);
	}
}
function status($name){return apcu_fetch($name);}
function sw($name,$action='Toggle',$comment='',$check=false){
	global $user;
	if(is_array($name)){
		$check=true;
		foreach($name as $i){
			if($i=='media')sw(array('tv','denon','tvled','kristal'),$action);
			elseif($i=='lichtenbeneden')sw(array('garage','pirgarage','pirkeuken','pirliving','pirinkom','eettafel','zithoek','tvled','kristal','bureel','terras','tuin','keuken','werkblad','wasbak','kookplaat','inkom','zolderg','voordeur'),$action);
			elseif($i=='lichtenboven')sw(array('pirhall','lichtbadkamer1','lichtbadkamer2','kamer','tobi','alex','hall','zolder'),$action);
			elseif($i=='slapen')sw(array('pirhall','hall','lichtenbeneden','dampkap','GroheRed'),$action);
			elseif($i=='weg')sw(array('slapen','lichtenbeneden','lichtenboven'),$action);
			else{if(status($i)!=$action)sw($i,$action);}
		}
	}else{
		$idx=idx($name);
		$msg=$user.' => '.$name.' => '.$action;
		if(!empty($comment))$msg.=' | '.$comment;
		lg($msg);
		if($idx>0){
			if($check==false)file_get_contents('http://127.0.0.1:8080/json.htm?type=command&param=switchlight&idx='.$idx.'&switchcmd='.$action);
			else{
				if(status($name)!=$action)file_get_contents('http://127.0.0.1:8080/json.htm?type=command&param=switchlight&idx='.$idx.'&switchcmd='.$action);
			}
		}else{
			setstatus($name,$action);
		}
	}
}
function timestamp($name){return apcu_fetch('T'.$name);}
function Thermometer($name){
	$temp=apcu_fetch($name);
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
function thermostaat($name,$top,$left){
	$stat=status($name.'_set');
	$dif=status($name.'_temp')-$stat;
	if($dif>0)$circle='hot';
	elseif($dif<0)$circle='cold';
	else $circle='grey';
	if($stat>20.5)$centre='red';
	elseif($stat>19)$centre='orange';
	elseif($stat>13)$centre='grey';
	else $centre='blue';
	echo '<a href=\'javascript:navigator_Go("floorplan.heating.php?SetSetpoint='.$name.'");\'>
		<div class="fix" style="top:'.$top.'px;left:'.$left.'px;">
			<img src="/images/thermo'.$circle.$centre.'.png" class="i48"/>
		<div class="fix center" style="top:32px;left:11px;width:26px;">'.$stat.'</div>
	</div>
	</a>
';
}
function ud($name,$nvalue,$svalue){
	$idx=idx($name);
	if($idx>0){
		file_get_contents('http://127.0.0.1:8080/json.htm?type=command&param=udevice&idx='.$idx.'&nvalue='.$nvalue.'&svalue='.$svalue);
	}else{
		setstatus($name,$svalue);
	}
}
function showTimestamp($name,$draai){
	$stamp=timestamp(''.$name);
	if(empty($stamp))return;
	echo '<div class="fix stamp r'.$draai.' t'.$name.'">'.strftime("%k:%M",$stamp).'</div>
	';
}
function Secured($name){echo '<div class="fix secured '.$name.'"></div>';}
function Motion($name){echo '<div class="fix motion '.$name.'"></div>';}
function Zwavecancelaction(){file_get_contents('http://127.0.0.1:8080/ozwcp/admpost.html',false,stream_context_create(array('http'=>array('header'=>'Content-Type: application/x-www-form-urlencoded\r\n','method'=>'POST','content'=>http_build_query(array('fun'=>'cancel')),),)));}
function ZwaveCommand($node,$command){$cm=array('AssignReturnRoute'=>'assrr','DeleteAllReturnRoutes'=>'delarr','NodeNeighbourUpdate'=>'reqnnu','RefreshNodeInformation'=>'refreshnode','RequestNetworkUpdate'=>'reqnu','HasNodeFailed'=>'hnf','Cancel'=>'cancel');$cm=$cm[$command];for($k=1;$k<=5;$k++){$result=file_get_contents('http://192.168.2.2:8080/ozwcp/admpost.html',false,stream_context_create(array('http'=>array('header'=>'Content-Type: application/x-www-form-urlencoded\r\n','method'=>'POST','content'=>http_build_query(array('fun'=>$cm,'node'=>'node'.$node)),),)));if($result=='OK')break;sleep(1);}return $result;}
function ControllerBusy($retries){for($k=1;$k<=$retries;$k++){$result=file_get_contents('http://127.0.0.1:8080/ozwcp/poll.xml');$p=xml_parser_create();xml_parse_into_struct($p,$result,$vals,$index);xml_parser_free($p);foreach($vals as $val){if($val['tag']=='ADMIN'){$result=$val['attributes']['ACTIVE'];break;}}if($result=='false')break;if($k==$retries){ZwaveCommand(1,'Cancel');break;}sleep(1);}}
function convertToHours($time){
	if($time<600)return substr(strftime('%k:%M:%S',$time-3600),1);
	elseif($time>=600&&$time<3600)return strftime('%k:%M:%S',$time-3600);
	else return strftime('%k:%M:%S',$time-3600);}
function endswith($string,$test){$strlen=strlen($string);$testlen=strlen($test);if($testlen>$strlen) return false;return substr_compare($string,$test,$strlen-$testlen,$testlen)===0;}
function checkport($ip,$port){
	if(pingport($ip,$port)==1){
		$prevcheck=apcu_fetch($ip.':'.$port);
		if($prevcheck>=5)telegram($ip.':'.$port.' online',true);
		if($prevcheck>0)setstatus($ip.':'.$port,0);
		return 1;
	}else{
		$check=apcu_fetch($ip.':'.$port)+1;
		if($check>0)setstatus($ip.':'.$port,$check);
		if($check==5)telegram($ip.':'.$port.' Offline',true);
		if($check%120==0)telegram($ip.':'.$port.' nog steeds Offline',true);
		return 0;
	}
}
function pingport($ip,$port){$file=@fsockopen($ip,$port,$errno,$errstr,10);$status=0;if(!$file)$status=-1;else{fclose($file);$status=1;}return $status;}
function double($name,$action,$comment='',$wait=2000000){sw($name,$action,$comment);usleep($wait);sw($name,$action,$comment.' repeat');}
function RefreshZwave($node){
	$last=timestamp('refresh'.$node);
	settimestamp('refresh'.$node);
	if($last<time-3600){
		$devices=json_decode(file_get_contents('http://127.0.0.1:8080/json.htm?type=openzwavenodes&idx=3',false),true);
		foreach($devices['result'] as $devozw)
			if($devozw['NodeID']==$node){
				$device=$devozw['Description'].' '.$devozw['Name'];
				break;
			}
		if(!isset($device))exit;
		for($k=1;$k<=5;$k++){
			$result=file_get_contents('http://127.0.0.1:8080/ozwcp/refreshpost.html',false,stream_context_create(array('http'=>array('header'=>'Content-Type: application/x-www-form-urlencoded\r\n','method'=>'POST','content'=>http_build_query(array('fun'=>'racp','node'=>$node)),),)));
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
			$result=json_decode(file_get_contents('https://api.telegram.org/bot'.$telegrambot.'/sendMessage?chat_id='.$telegramchatid2.'&text='.$msg.'&disable_notification='.$silent),true);
			if(isset($result['ok']))
				if($result['ok']===true){lg('telegram sent to 2: '.$msg);break;}
				else lg('telegram sent failed');sleep($x*3);
		}
}
function Luifel($name,$stat){
	echo '
	<form method="POST">
		<a href=\'javascript:navigator_Go("floorplan.heating.php?Rollers='.$name.'");\'>
		<div class="fix z '.$name.'">
			<input type="hidden" name="Luifel" value="'.$name.'"/>';
	if($stat==100)echo '<input type="image" src="/images/arrowgreenup.png" class="i60"/>';
	elseif($stat==0)echo '<input type="image" src="/images/arrowgreendown.png" class="i60"/>';
	else echo'
			<input type="image" src="/images/arrowdown.png" class="i60"/>
			<div class="fix center dimmerlevel" style="position:absolute;top:10px;left:-2px;width:70px;letter-spacing:4;" onclick="location.href=\'floorplan.heating.php?Rollers='.$name.'\';"><font size="5" color="#CCC">
				'. (100 - $stat) .'</font>
			</div>';
	echo '
		</div>
		</a>
	</form>';
}
function Rollers($name,$stat){
	echo '
	<form method="POST">
		<a href=\'javascript:navigator_Go("floorplan.heating.php?Rollers='.$name.'");\'>
		<div class="fix z '.$name.'">
			<input type="hidden" name="Rollers" value="'.$name.'"/>';
	if($stat==100)echo '<input type="image" src="/images/arrowgreenup.png" class="i60"/>';
	elseif($stat==0)echo '<input type="image" src="/images/arrowgreendown.png" class="i60"/>';
	else echo'
			<input type="image" src="/images/arrowdown.png" class="i60"/>
			<div class="fix center dimmerlevel" style="position:absolute;top:10px;left:-2px;width:70px;letter-spacing:4;" onclick="location.href=\'floorplan.heating.php?Rollers='.$name.'\';"><font size="5" color="#CCC">
				'. (100 - $stat) .'</font>
			</div>';
	echo '
		</div>
		</a>
	</form>';
}
function Rollery($name,$stat,$top,$left,$size,$rotation){
	$stat=100-$stat;
	if($stat>0)$perc=($stat/100);
	else $perc=0;
	if($rotation=='P'){
		if($stat==100){$nsize=0;$top=$top;}
		elseif($stat>0){$nsize=($size*$perc)+4;if($nsize>$size)$nsize=$size;$top=$top+($size-$nsize);}
		else{$nsize=$size;}
		echo '<div class="fix yellow" style="top:'.$top.'px;left:'.$left.'px;width:7px;height:'.$nsize.'px;"></div>
		';
	}elseif($rotation=='PL'){
		if($stat==100){$nsize=0;$top=$top;}
		elseif($stat>0){$nsize=($size*$perc)+4;if($nsize>$size)$nsize=$size;$top=$top+($size-$nsize);}
		else{$nsize=$size;}
		echo '<div class="fix yellow" style="top:'.$top.'px;left:'.$left.'px;width:7px;height:'.$nsize.'px;"></div>
		';
	}elseif($rotation=='L'){
		if($stat==100){$nsize=0;}
		elseif($stat>0){$nsize=($size*$perc)+4;if($nsize>$size)$nsize=$size;}
		else{$nsize=$size;}
		echo '<div class="fix yellow" style="top:'.$top.'px;left:'.$left.'px;width:'.$nsize.'px;height:7px;"></div>
		';
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
