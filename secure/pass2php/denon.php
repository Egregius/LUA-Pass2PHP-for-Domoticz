<?php
if(apcu_fetch('sdenon')d=="On"){
	$ctx=stream_context_create(array('http'=>array('timeout'=>2)));
	for($x=0;$x<=50;$x++){
		usleep(500000);
		$denon=json_decode(json_encode(simplexml_load_string(file_get_contents('http://192.168.2.4/goform/formMainZone_MainZoneXml.xml?_='.time(),false,$ctx))),TRUE);
		if($denon['ZonePower']['value']!='ON'){
			lg('Denon '.$x.' Zonepower != ON');
			file_get_contents('http://192.168.2.4/MainZone/index.put.asp?cmd0=PutZone_OnOff%2FON&cmd1=aspMainZone_WebUpdateStatus%2F',false,$ctx);
		}else{
			if($denon['InputFuncSelect']['value']!='TUNER'){
				lg('Denon '.$x.' InputFuncSelect != TUNER');
				file_get_contents('http://192.168.2.4/MainZone/index.put.asp?cmd0=PutZone_InputFunction/TUNER',false,$ctx);
			}else{
				lg('Denon '.$x.' OK');
				sleep(1);
				file_get_contents('http://192.168.2.4/MainZone/index.put.asp?cmd0=PutZone_OnOff%2FON&cmd1=aspMainZone_WebUpdateStatus%2F&ZoneName=ZONE2',false,$ctx);
				break;
			}
		}
	}
}