<?php
if($status=="On"&&apcu_fetch('smeldingen')=='On'){
	if(apcu_fetch('sweg')=='Off'&&apcu_fetch('sslapen')=='Off')sw('deurbel','On');
	if(apcu_fetch('zon')<=10)sw('voordeur','On');
	file_get_contents('http://192.168.2.11/telegram.php?snapshot=true');
	file_get_contents('http://192.168.2.11/fifo_command.php?cmd=record%20on%205%2055');
	if(apcu_fetch('sslapen')=='Off')telegram('Deurbel',false,3);
	else telegram('Deurbel',true,2);
}