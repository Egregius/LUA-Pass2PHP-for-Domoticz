<?php
if($status=='On'&&$auto){
	if(apcu_fetch('sdenon')=='Off'&&apcu_fetch('sbureel')=='Off'){
		if($zon==0){
			if(apcu_fetch('skeuken')=='Off')sw('keuken','On');
		}
		//if(!$weg&&!$slapen)include('pass2php/miniliving1s.php');
	}
	if($Weg>0&&$meldingen&&apcu_fetch('tWeg')<time-178){
		sw('sirene','On');
		telegram('Beweging living om '.strftime("%k:%M:%S",time),false,3);
	}
}
