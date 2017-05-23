<?php
$spirgarage=apcu_fetch('spirgarage');
if($spirgarage=='Off'&&apcu_fetch('tpirgarage')<time-150&&apcu_fetch('tpoort')<time-150&&apcu_fetch('tgarage')<time-150&&apcu_fetch('sgarage')=='On'&&$auto)sw('garage','Off');
elseif($spirgarage=='On'&&apcu_fetch('sgarage')=='Off'&&$auto&&$zon<$zongarage)sw('garage','On');

$spirinkom=apcu_fetch('spirinkom');
$spirhall=apcu_fetch('spirhall');
if($spirinkom=='Off'&&apcu_fetch('tpirinkom')<time-60&&$spirhall=='Off'&&apcu_fetch('tpirhall')<time-60&&apcu_fetch('tinkom')<time-90&&apcu_fetch('thall')<time-90&&$auto){if(apcu_fetch('sinkom')=='On')sw('inkom','Off');if(apcu_fetch('shall')=='On')sw('hall','Off');}
elseif(($spirinkom=='On'||$spirhall=='On')&&$zon<$zoninkom&&$auto){if(apcu_fetch('sinkom')=='Off')sw('inkom','On');if(apcu_fetch('shall')=='Off'&&$Weg==0)sw('hall','On');}

$spirkeuken=apcu_fetch('spirkeuken');
if(apcu_fetch('tpirkeuken')<time-60&&apcu_fetch('tkeuken')<time-80&&$spirkeuken=='Off'&&apcu_fetch('swasbak')=='Off'&&apcu_fetch('skeuken')=='On'&&apcu_fetch('skookplaat')=='Off'&&apcu_fetch('swerkblad')=='Off'&&$auto)sw('keuken','Off');
elseif($spirkeuken=='On'&&$zon<$zonkeuken&&apcu_fetch('skeuken')=='Off'&&apcu_fetch('swasbak')=='Off'&&apcu_fetch('skookplaat')=='Off'&&apcu_fetch('swerkblad')=='Off'&&$auto)sw('keuken','On');

$smappee=json_decode(file_get_contents('http://'.$smappeeip.'/gateway/apipublic/reportInstantaneousValues'),true);
if(!empty($smappee['report'])){
	preg_match_all("/ activePower=(\\d*.\\d*)/",$smappee['report'],$matches);
	if(!empty($matches[1][1])){
		$zon=round($matches[1][1],0);
		apcu_store('zon',$zon);
		if(!empty($matches[1][2])){
			$consumption=round($matches[1][2],0);
			apcu_store('consumption',$consumption);
			$timestamp=strftime("%Y-%m-%d %H:%M:%S",time);
			$query="INSERT INTO `smappee` (`timestamp`,`consumption`) VALUES ('$timestamp','$consumption');";
			$db=new mysqli('server','user','password','database');if($db->connect_errno>0)die('Unable to connect to database [' . $db->connect_error . ']');if(!$result=$db->query($query))die('There was an error running the query ['.$query .' - ' . $db->error . ']');$db->close();
			if($consumption>8000){
				if(apcu_fetch('notify_power')<time-3600){
					apcu_store('notify_power',time);
					telegram('Power usage: '.$consumption.' W!',false);
				}
			}
		}
	}
}else{
	if(shell_exec('curl -H "Content-Type: application/json" -X POST -d "admin" http://'.$smappeeip.'/gateway/apipublic/logon')!='{"success":"Logon successful!","header":"Logon to the monitor portal successful..."}')exit;
}
