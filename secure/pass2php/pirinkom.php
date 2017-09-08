<?php
if($status=="On"&&$auto){
	if(status('inkom')=='Off'&&$zon<$zoninkom){
		sw('inkom','On');
	}
	if($Weg==0&&status('hall')=='Off'&&$zon<$zoninkom){
		sw('hall','On');
	}
	if($Weg>0&&$meldingen&&timestamp('Weg')<time-178){
		sw('sirene','On');
		shell_exec('/var/www/html/secure/ios.sh "Beweging Inkom" > /dev/null 2>/dev/null &');
		telegram('Beweging inkom om '.strftime("%k:%M:%S",time),false,2);
	}
}
