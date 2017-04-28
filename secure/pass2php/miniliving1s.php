<?php
$ctx=stream_context_create(array('http'=>array('timeout'=>2)));
for($k=1;$k<=200;$k++){
	if(apcu_fetch('sdenon')!='On'){
		sw('denon','On');
		usleep(500000);
	}else break;
}
for($k=1;$k<=200;$k++){
	$denon="";
	$denon=json_decode(json_encode(simplexml_load_string(file_get_contents('http://192.168.2.4/goform/formMainZone_MainZoneXml.xml?_='.time(),false,$ctx))),true);
	if($denon['ZonePower']['value']!='ON'){
		file_get_contents('http://192.168.2.4/MainZone/index.put.asp?cmd0=PutZone_OnOff%2FON&cmd1=aspMainZone_WebUpdateStatus%2F',false,$ctx);
		usleep(500000);
	}else break;
}
for($k=1;$k<=200;$k++){
	$denon="";
	$denon=json_decode(json_encode(simplexml_load_string(file_get_contents('http://192.168.2.4/goform/formMainZone_MainZoneXml.xml?_='.time().'&ZoneName=ZONE2',false,$ctx))),true);
	if($denon['ZonePower']['value']!='ON'){
		file_get_contents('http://192.168.2.4/MainZone/index.put.asp?cmd0=PutZone_OnOff%2FON&cmd1=aspMainZone_WebUpdateStatus%2F&ZoneName=ZONE2',false,$ctx);
		usleep(500000);
	}else break;
}
for($k=1;$k<=200;$k++){
	$denon="";
	$denon=json_decode(json_encode(simplexml_load_string(file_get_contents('http://192.168.2.4/goform/formMainZone_MainZoneXml.xml?_='.time(),false,$ctx))),TRUE);
	if($denon['InputFuncSelect']['value']!='TUNER'){
		file_get_contents('http://192.168.2.4/MainZone/index.put.asp?cmd0=PutZone_InputFunction/TUNER&cmd1=aspMainZone_WebUpdateStatus%2F');
		usleep(500000);
	}else break;
}
if(apcu_fetch('tv')!='Off')sw('tv','Off');
if(apcu_fetch('tvled')!='Off')sw('tvled','Off');
if($Weg!=0){apcu_store('Weg',0);apcu_store('tWeg',time);}