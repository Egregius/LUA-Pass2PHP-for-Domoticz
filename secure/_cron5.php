<?php
$spirgarage=status('pirgarage');
if($spirgarage=='Off'&&timestamp('pirgarage')<time-150&&timestamp('poort')<time-150&&timestamp('garage')<time-150&&status('garage')=='On'&&$auto)sw('garage','Off');
elseif($spirgarage=='On'&&status('garage')=='Off'&&$auto&&$zon<$zongarage)sw('garage','On');

$spirinkom=status('pirinkom');
$spirhall=status('pirhall');
if($spirinkom=='Off'&&timestamp('pirinkom')<time-60&&$spirhall=='Off'&&timestamp('pirhall')<time-60&&timestamp('inkom')<time-90&&timestamp('hall')<time-90&&$auto){if(status('inkom')=='On')sw('inkom','Off');if(status('hall')=='On')sw('hall','Off');}
elseif(($spirinkom=='On'||$spirhall=='On')&&$zon<$zoninkom&&$auto){if(status('inkom')=='Off')sw('inkom','On');if(status('hall')=='Off'&&$Weg==0)sw('hall','On');}

$spirkeuken=status('pirkeuken');
if(timestamp('pirkeuken')<time-60&&timestamp('keuken')<time-80&&$spirkeuken=='Off'&&status('wasbak')=='Off'&&status('keuken')=='On'&&status('kookplaat')=='Off'&&status('werkblad')=='Off'&&$auto)sw('keuken','Off');
//elseif($spirkeuken=='On'&&$zon<$zonkeuken&&status('keuken')=='Off'&&status('wasbak')=='Off'&&status('kookplaat')=='Off'&&status('werkblad')=='Off'&&$auto)sw('keuken','On');

$smappee=json_decode(file_get_contents('http://'.$smappeeip.'/gateway/apipublic/reportInstantaneousValues'),true);
if(!empty($smappee['report'])){
	preg_match_all("/ activePower=(\\d*.\\d*)/",$smappee['report'],$matches);
	if(!empty($matches[1][1])){
		$zon=round($matches[1][1],0);
		setstatus('zon',$zon);
		if(!empty($matches[1][2])){
			$consumption=round($matches[1][2],0);
			setstatus('consumption',$consumption);
			$timestamp=strftime("%Y-%m-%d %H:%M:%S",time);
			$query="INSERT INTO `smappee` (`timestamp`,`consumption`) VALUES ('$timestamp','$consumption');";
			$db=new mysqli('server','user','password','database');if($db->connect_errno>0)die('Unable to connect to database [' . $db->connect_error . ']');if(!$result=$db->query($query))die('There was an error running the query ['.$query .' - ' . $db->error . ']');$db->close();
			if($consumption>8000){
				if(timestamp('notify_power')<time-3600){
					settimestamp('notify_power',time);
					telegram('Power usage: '.$consumption.' W!',false);
				}
			}
		}
	}
}else{
	if(shell_exec('curl -H "Content-Type: application/json" -X POST -d "" http://'.$smappeeip.'/gateway/apipublic/logon')!='{"success":"Logon successful!","header":"Logon to the monitor portal successful..."}')exit;
}
?>
