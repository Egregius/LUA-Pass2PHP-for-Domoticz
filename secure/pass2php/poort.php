<?php
if($status=='Open'){
	if(apcu_fetch('zon')<2000&&apcu_fetch('sgarage')=='Off')
		sw('garage','On');
	if((apcu_fetch('sweg')=='On'||apcu_fetch('sslapen')=='On')&&apcu_fetch('smeldingen')=='On'&&apcu_fetch('tweg')<time-178&&apcu_fetch('tslapen')<time-178){
		sw('sirene','On');
		telegram('Poort open om '.strftime("%k:%M:%S",time),false,3);
	}
}