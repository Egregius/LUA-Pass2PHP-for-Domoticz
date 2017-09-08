<?php
if($status=='On'&&$auto){
	if((time<strtotime('9:30')||time>strtotime('18:30')||$zon<$zongarage)&&status('garage')=='Off'){
		sw('garage','On');
	}
	if($Weg>0&&$meldingen&&timestamp('Weg')<time-178){
		sw('sirene','On');
		shell_exec('/var/www/html/secure/ios.sh "Beweging Garage" > /dev/null 2>/dev/null &');
		telegram('Beweging garage om '.strftime("%k:%M:%S",time),false,2);
	}
}