<?php
if($status=="On"&&apcu_fetch('smeldingen')=='On'){
	if(apcu_fetch('sweg')=='Off'&&apcu_fetch('sslapen')=='Off')sw(apcu_fetch('ideurbel'),'On','deurbel');
	if(apcu_fetch('sslapen')=='Off'){
		telegram('Deurbel',false,3);
	}
	else telegram('Deurbel',true,2);
	$ctx=stream_context_create(array('http'=>array('timeout'=>3)));
	file_get_contents('http://192.168.2.11/telegram.php?snapshot=true',false,$ctx);
	file_get_contents('http://192.168.2.11/fifo_command.php?cmd=record%20on%205%2055',false,$ctx);
	if(apcu_fetch('zon')<=10)sw(apcu_fetch('ivoordeur'),'On');
}