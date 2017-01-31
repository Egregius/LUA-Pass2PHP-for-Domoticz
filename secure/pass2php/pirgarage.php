<?php
if($status=='On'){
	if((time<strtotime('10:30')||time>strtotime('18:30')||apcu_fetch('zon')<3000)&&apcu_fetch('sgarage')=='Off'){
		sw('garage','On');
		apcu_store('tgarage',time);
	}
	if((apcu_fetch('sweg')=='On'||apcu_fetch('sslapen')=='On')&&apcu_fetch('smeldingen')=='On'&&apcu_fetch('tweg')<time-178&&apcu_fetch('tslapen')<time-178){
		sw('sirene','On');
		telegram('Beweging garage om '.strftime("%k:%M:%S",time),false,3);
	}
}