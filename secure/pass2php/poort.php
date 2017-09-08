<?php
if($status=='Open'){
	if($zon<$zongarage&&status('garage')=='Off')sw('garage','On');
	if($Weg>0&&$meldingen&&timestamp('Weg')<time-178){
		sw('sirene','On');
		shell_exec('/var/www/html/secure/ios.sh "Poort open" > /dev/null 2>/dev/null &');
		telegram('Poort open om '.strftime("%k:%M:%S",time),false,2);
	}
}else{
	if(status('poortrf')!='Off'){
		shell_exec("curl -s 'http://192.168.2.2/secure/domoticz.php?cmd=sw&name=poortrf&after=120&action=Off' > /dev/null 2>/dev/null &");
//		sleep(120);
//		double('poortrf','Off');
	}
}