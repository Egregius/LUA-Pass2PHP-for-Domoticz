<?php
shell_exec('/home/pi/wakenas.sh > /dev/null 2>/dev/null &');
$ctx=stream_context_create(array('http'=>array('timeout'=>2)));
if(status('denon')!='On')sw('denon','On');
if($zon<$zonmedia){
	if(status('tvled')!='On')sw('tvled','On');
	if(status('kristal')!='On')sw('kristal','On');
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
	if($denon['InputFuncSelect']['value']!='KODI'){
		file_get_contents('http://192.168.2.4/MainZone/index.put.asp?cmd0=PutZone_InputFunction/DVD&cmd1=aspMainZone_WebUpdateStatus%2F');
		usleep(500000);
	}else break;
}
if(status('tv')!='On')sw('tv','On');
if(status('kodi')!='On')sw('kodi','On');
if($Weg!=0)setstatus('Weg',0);
?>