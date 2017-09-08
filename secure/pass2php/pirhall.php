<?php
if($status=='On'&&$auto){
	if($Weg==0&&status('hall')=='Off'&&(time<strtotime('7:00')||$zon<$zoninkom)){
		sw('hall','On');
	}
	if(status('inkom')=='Off'&&(time<strtotime('7:00')||$zon<$zoninkom)){
		sw('inkom','On');
	}
	if($Weg==2&&$meldingen&&timestamp('Weg')<time-178){
		sw('sirene','On');
		shell_exec('/var/www/html/secure/ios.sh "Beweging Hall" > /dev/null 2>/dev/null &');
		telegram('Beweging hall om '.strftime("%k:%M:%S",time),false,2);
	}
}
