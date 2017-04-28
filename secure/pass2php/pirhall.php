<?php
if($status=='On'&&$auto){
	if($Weg==0&&apcu_fetch('shall')=='Off'&&(time<strtotime('8:00')||$zon<50)){
		sw('hall','On');
		apcu_store('thall',time);
	}
	if(apcu_fetch('sinkom')=='Off'&&(time<strtotime('8:00')||$zon<50)){
		sw('inkom','On');
		apcu_store('tinkom',time);
	}
	if($Weg==2&&$meldingen&&apcu_fetch('tWeg')<time-178){
		sw('sirene','On');
		telegram('Beweging hall om '.strftime("%k:%M:%S",time),false,3);
	}
}
