<?php
if($status=="On"&&$meldingen){
	shell_exec('/var/www/html/secure/ios.sh "Deurbel" > /dev/null 2>/dev/null &');
	if(!$weg&&!$slapen)sw('deurbel','On');
	if($zon<10)sw('voordeur','On');
	shell_exec('curl -s "http://192.168.2.11/fifo_command.php?cmd=record%20on%205%2055" > /dev/null 2>/dev/null &');
	if(file_get_contents('http://192.168.2.11/telegram.php?snapshot=true')!='OK')telegram('Deurbel',true,2);
	if(!$weg&&!$slapen){
		shell_exec('curl -s "http://127.0.0.1/secure/beep.php?usleep=150000" > /dev/null 2>/dev/null &');
	}
}