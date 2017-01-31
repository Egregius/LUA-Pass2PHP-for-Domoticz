<?php
if($status=="On"){
	if(apcu_fetch('sinkom')=='Off'&&(time<strtotime('8:00')||apcu_fetch('zon')<200)){
		sw('inkom','On');
		apcu_store('tinkom',time);
	}
	if(apcu_fetch('sslapen')=='Off'&&apcu_fetch('shall')=='Off'&&(time<strtotime('8:00')||apcu_fetch('zon')<200)){
		sw('hall','On');
		apcu_store('thall',time);
	}
	if((apcu_fetch('sweg')=='On'||apcu_fetch('sslapen')=='On')&&apcu_fetch('smeldingen')=='On'&&apcu_fetch('tweg')<time-178&&apcu_fetch('tslapen')<time-178){
		sw('sirene','On');
		telegram('Beweging inkom om '.strftime("%k:%M:%S",time),false,3);
	}
}
