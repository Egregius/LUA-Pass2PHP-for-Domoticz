<?php
if($status=='On'&&$auto){
	if((time<strtotime('10:00')||time>strtotime('18:30')||$zon<800)&&apcu_fetch('sgarage')=='Off'){
		sw('garage','On');
		apcu_store('tgarage',time);
	}
	if($Weg>0&&$meldingen&&apcu_fetch('tWeg')<time-178){
		sw('sirene','On');
		telegram('Beweging garage om '.strftime("%k:%M:%S",time),false,3);
	}
}