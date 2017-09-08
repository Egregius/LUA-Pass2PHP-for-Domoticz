<?php
if($status=='On'&&$auto){
	if(status('denon')=='Off'&&status('bureel')=='Off'&&status('eettafel')=='Off'){
		if($zon==0){
			if(status('keuken')=='Off')sw('keuken','On');
		}
		//if(!$weg&&!$slapen)include('pass2php/miniliving1s.php');
	}
	if($Weg>0&&$meldingen&&timestamp('Weg')<time-178){
		sw('sirene','On');
		shell_exec('/var/www/html/secure/ios.sh "Beweging Living" > /dev/null 2>/dev/null &');
		telegram('Beweging living om '.strftime("%k:%M:%S",time),false,2);
	}
}
