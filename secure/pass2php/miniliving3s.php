<?php
if(apcu_fetch('denon')=='On'){
	denon('MVDOWN');
	denon('MVDOWN');
	denon('MVDOWN');
	denon('MVDOWN');
	denon('MVDOWN');
	denon('MVDOWN');
}else{
	$nowplaying=@json_decode(@json_encode(@simplexml_load_string(@file_get_contents('http://192.168.2.194:8090/now_playing'))),true);
	if(!empty($nowplaying)){
		if(isset($nowplaying['@attributes']['source'])){
			if($nowplaying['@attributes']['source']!='STANDBY'){
				$volume=@json_decode(@json_encode(@simplexml_load_string(@file_get_contents('http://192.168.2.194:8090/volume'))),true);
				$cv=$volume['actualvolume'];
				if($cv>50)bosevolume($cv-5);
				elseif($cv>30)bosevolume($cv-4);
				elseif($cv>20)bosevolume($cv-3);
				elseif($cv>10)bosevolume($cv-2);
				else bosevolume($cv-1);
			}
		}
	}
}
function denon($cmd){for($x=1;$x<=10;$x++)if(denontcp($cmd,$x))break;}
function denontcp($cmd,$x){
	$sleep=102000*$x;
	$socket=@fsockopen("192.168.2.204","23",$errno,$errstr,2);
	if($socket){fputs($socket, "$cmd\r\n");fclose($socket);usleep($sleep);return true;}
	else{usleep($sleep);return false;}
}

if($Weg!=0){apcu_store('Weg',0);apcu_store('TWeg',time());}
?>
