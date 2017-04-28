<?php
if($status=='Open'&&$auto){
	if($zon<1000&&apcu_fetch('sgarage')=='Off')sw('garage','On');
	if($Weg>0&&$meldingen&&apcu_fetch('tWeg')<time-178){
		sw('sirene','On');
		telegram('Poort open om '.strftime("%k:%M:%S",time),false,3);
	}
}else{
	if(apcu_fetch('spoortrf')!='Off'){
		sleep(2);
		double('poortrf','Off');
	}
}