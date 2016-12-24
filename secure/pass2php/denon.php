<?php
if($s['denon']=="On"){
	$denon='http://192.168.2.4/';
	$ctx=stream_context_create(array('http'=>array('timeout'=>3)));

	for($x=0;$x<=20;$x++){
		sleep(1);
		$denon=json_decode(json_encode(simplexml_load_string(file_get_contents($denon.'goform/formMainZone_MainZoneXml.xml?_='.time(),false,$ctx))),TRUE);
		if($denon['ZonePower']['value']!='ON'){
			file_get_contents($denon.'MainZone/index.put.asp?cmd0=PutZone_OnOff%2FON&cmd1=aspMainZone_WebUpdateStatus%2F',false,$ctx);
			sleep(1);
			file_get_contents($denon.'MainZone/index.put.asp?cmd0=PutZone_InputFunction/TUNER',false,$ctx);
			sleep(1);
			file_get_contents($denon.'MainZone/index.put.asp?cmd0=PutZone_OnOff%2FON&cmd1=aspMainZone_WebUpdateStatus%2F&ZoneName=ZONE2',false,$ctx);
		}else break;
	}
}