<?php
if(apcu_fetch('sdenon')=='Off')sw(apcu_fetch('idenon'),'On','Denon');
if(apcu_fetch('stv')=='Off')sw(apcu_fetch('itv'),'On','TV');
if(apcu_fetch('zon')<100&&apcu_fetch('stvled')=='Off')sw(apcu_fetch('itvled'),'On','tvled');
$ctx=stream_context_create(array('http'=>array('timeout'=>3)));
file_get_contents('http://192.168.2.4/MainZone/index.put.asp?cmd0=PutZone_InputFunction/SAT/CBL',false,$ctx);
usleep(800000);
file_get_contents('http://192.168.2.4/MainZone/index.put.asp?cmd0=PutMasterVolumeSet/-42.0',false,$ctx);