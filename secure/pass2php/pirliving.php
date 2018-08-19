<?php
if($status=='On'&&$auto){
	if($Weg==0&&apcu_fetch('denon')=='Off'&&apcu_fetch('bureel')=='Off'&&apcu_fetch('eettafel')==0){
		if($zon==0){
			if(apcu_fetch('keuken')=='Off')sw('keuken','On');
			if(apcu_fetch('bureel')=='Off')sw('bureel','On');
		}
		if(time<strtotime('19:00')){
			$nowplaying=@json_decode(@json_encode(@simplexml_load_string(@file_get_contents('http://192.168.2.194:8090/now_playing'))),true);
			if(!empty($nowplaying)){
				if(isset($nowplaying['@attributes']['source'])){
					if($nowplaying['@attributes']['source']=='STANDBY'){
						bosekey("POWER");
						bosepreset(rand(1,6));
						if(time<strtotime('6:00'))bosevolume(8);
						elseif(time<strtotime('7:00'))bosevolume(10);
						elseif(time<strtotime('8:00'))bosevolume(14);
						else bosevolume(18);
					}
				}
			}
		}
	}
	if($Weg>0&&$meldingen&&past('Weg')>178){
		sw('sirene','On');
		shell_exec('/var/www/html/secure/ios.sh "Beweging Living" > /dev/null 2>/dev/null &');
		telegram('Beweging living om '.strftime("%k:%M:%S",time),false,2);
	}
	if(apcu_fetch('jbl')=='Off'&&$zon<10)sw('jbl','On');
}
?>
