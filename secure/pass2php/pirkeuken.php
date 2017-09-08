<?php
if($status=="On"&&$auto){
	if(status('keuken')=='Off'&&status('wasbak')=='Off'&&status('werkblad')=='Off'&&status('kookplaat')=='Off'&&$zon<$zonkeuken){
		sw('keuken','On');
	}
	if($Weg>0&&$meldingen&&timestamp('Weg')<time-178){
		sw('sirene','On');
		shell_exec('/var/www/html/secure/ios.sh "Beweging Keuken" > /dev/null 2>/dev/null &');
		telegram('Beweging keuken om '.strftime("%k:%M:%S",time),false,2);
	}
}
