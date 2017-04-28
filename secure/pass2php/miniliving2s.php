<?php
$kodi=apcu_fetch('skodi');
$keuken=apcu_fetch('skeuken');
if($kodi=='On'){
	$ctx=stream_context_create(array('http'=>array('timeout'=>2)));
	file_get_contents('http://192.168.2.7:1597/jsonrpc?request={"jsonrpc":"2.0","id":1,"method":"Player.PlayPause","params":{"playerid":1}}',false,$ctx);
	$properties=json_decode(file_get_contents('http://192.168.2.7:1597/jsonrpc?request={"jsonrpc":"2.0","method":"Player.GetProperties","id":1,"params":{"playerid":1,"properties":["playlistid","speed","position","totaltime","time","audiostreams","currentaudiostream","subtitleenabled","subtitles","currentsubtitle"]}}',false,$ctx),true);
	if(!empty($properties['result'])){
		$prop=$properties['result'];
		if($prop['speed']==0){
			if($zon<100){
				if($keuken=='Off')sw('keuken','On');
			}
		}
		else andereuit();
	}
}else{
	if($keuken=='On')andereuit();
	else{
		if($zon<100){
			if($keuken=='Off')sw('keuken','On');
		}
	}
}

function andereuit(){
	$items=array('pirkeuken','pirgarage','pirinkom','pirhall');
	foreach($items as $item)
		if(apcu_fetch('s'.$item)!='Off')
			ud($item,0,'Off');
	$items=array('eettafel','zithoek','garage','inkom','hall','keuken','werkblad','wasbak','kookplaat');
	foreach($items as $item)
		if(apcu_fetch('s'.$item)!='Off')
			sw($item,'Off');
}
if($Weg!=0){apcu_store('Weg',0);apcu_store('tWeg',time);}