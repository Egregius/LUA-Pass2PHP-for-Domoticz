<?php
if(apcu_fetch('skodi')=='On'){
	$ctx=stream_context_create(array('http'=>array('timeout'=>2)));
	file_get_contents('http://192.168.2.7:1597/jsonrpc?request={"jsonrpc":"2.0","id":1,"method":"Player.PlayPause","params":{"playerid":1}}',false,$ctx);
	$properties=json_decode(file_get_contents('http://192.168.2.7:1597/jsonrpc?request={"jsonrpc":"2.0","method":"Player.GetProperties","id":1,"params":{"playerid":1,"properties":["playlistid","speed","position","totaltime","time","audiostreams","currentaudiostream","subtitleenabled","subtitles","currentsubtitle"]}}',false,$ctx),true);
	if(!empty($properties['result'])){
		$prop=$properties['result'];
		if($prop['speed']==0)sw(apcu_fetch('iwasbak'),'On');
		else ud(apcu_fetch('iminiliving4l'),0,'On');
	}
}else{
	if(apcu_fetch('swasbak')=='On'){
		ud(apcu_fetch('iminiliving4l'),0,'On');
	}elseif(apcu_fetch('swasbak')=='Off'){
		sw(apcu_fetch('iwasbak'),'On');
	}
}