<?php
if($status=="On"&&$meldingen){
	if(!$weg&&!$slapen)sw('deurbel','On');
	if($zon<=10)sw('voordeur','On');
	shell_exec('curl -s "http://192.168.2.11/fifo_command.php?cmd=record%20on%205%2055" &');
	if(file_get_contents('http://192.168.2.11/telegram.php?snapshot=true')!='OK')telegram('Deurbel',true,2);
	ios('Deurbel');
}