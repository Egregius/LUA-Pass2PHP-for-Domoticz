<?php
$ctx=stream_context_create(array('http'=>array('timeout'=>3)));
file_get_contents('http://192.168.2.7:1597/jsonrpc?request={"jsonrpc":"2.0","id":1,"method":"Player.PlayPause","params":{"playerid":1}}',false,$ctx);
if($s['wasbak']=='On') ud(apcu_fetch('iminiliving4l'),0,'On');
else sw(apcu_fetch('iwasbak'),'On');