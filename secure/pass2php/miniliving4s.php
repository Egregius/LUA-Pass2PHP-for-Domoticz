<?php
/*$ctx=stream_context_create(array('http'=>array('timeout'=>3)));
$denon=json_decode(json_encode(simplexml_load_string(file_get_contents('http://192.168.2.4/goform/formMainZone_MainZoneXml.xml?_='.time,false,$ctx))),TRUE);
if($denon){
	$denon['MasterVolume']['value']=='--'
		?$setvalue=-55
		:$setvalue=$denon['MasterVolume']['value'];
	$setvalue=$setvalue+3;if($setvalue>-10)$setvalue=-10;if($setvalue<-80)$setvalue=-80;$volume=80+$setvalue;
	usleep(100000);
	file_get_contents('http://192.168.2.4/MainZone/index.put.asp?cmd0=PutMasterVolumeSet/'.$setvalue.'.0',false,$ctx);
}
*/
denon('MVUP');
denon('MVUP');
denon('MVUP');
denon('MVUP');
denon('MVUP');
denon('MVUP');

function denon($cmd){for($x=1;$x<=10;$x++)if(denontcp($cmd,$x))break;}
function denontcp($cmd,$x){
	$sleep=102000*$x;
	$socket=fsockopen("192.168.2.4","23",$errno,$errstr,2);
	if($socket){fputs($socket, "$cmd\r\n");fclose($socket);usleep($sleep);return true;}
	else{usleep($sleep);echo 'sleeping '.$sleep.'<br>';return false;}
}