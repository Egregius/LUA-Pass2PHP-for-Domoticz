<?php
if($status=='Open'){
	if(apcu_fetch('zon')<1500&&apcu_fetch('sgarage')=='Off')
		sw(apcu_fetch('igarage'),'On','garage');
	if((apcu_fetch('sweg')=='On'||apcu_fetch('sslapen')=='On')&&apcu_fetch('smeldingen')=='On'&&apcu_fetch('tweg')<time-178&&apcu_fetch('tslapen')<time-178){
		sw(apcu_fetch('isirene'),'On');
		telegram('Poort open om '.strftime("%k:%M:%S",time),false,3);
	}
}