<?php
if($status=="On"&&$auto){
	if(apcu_fetch('skeuken')=='Off'&&apcu_fetch('swasbak')=='Off'&&apcu_fetch('swerkblad')=='Off'&&apcu_fetch('skookplaat')=='Off'&&$zon<100){
		sw('keuken','On');
	}
	if($Weg>0&&$meldingen&&apcu_fetch('tWeg')<time-178){
		sw('sirene','On');
		telegram('Beweging keuken om '.strftime("%k:%M:%S",time),false,3);
	}
}
