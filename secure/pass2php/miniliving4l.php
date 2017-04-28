<?php
$items=array('tv','tvled','kristal','denon');
foreach($items as $item)if(apcu_fetch('s'.$item)!='Off')sw($item,'Off');
if(apcu_fetch('skodi')!='Off'){
	$ctx=stream_context_create(array('http'=>array('timeout'=>5)));
	file_get_contents('http://192.168.2.7:1597/jsonrpc?request={"jsonrpc":"2.0","id":1,"method":"System.Shutdown"}',false,$ctx);
}
if($Weg!=0){apcu_store('Weg',0);apcu_store('tWeg',time);}