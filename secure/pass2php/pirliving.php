<?php
if($status=='On'){
	if(apcu_fetch('sdenon')=='Off'){
		if(apcu_fetch('zon')<200){
			if(apcu_fetch('swasbak')=='Off')sw('wasbak','On');
			if(apcu_fetch('sbureel')=='Off')sw('bureel','On');
			if(apcu_fetch('zon')<50){
				if(apcu_fetch('seettafel')=='Off')sl('eettafel',3);
				if(apcu_fetch('szithoek')=='Off')sl('zithoek',2);
			}
		}

		if(apcu_fetch('sweg')=='Off'&&apcu_fetch('sslapen')=='Off')include('pass2php/miniliving1l.php');
	}
	if((apcu_fetch('sweg')=='On'||apcu_fetch('sslapen')=='On')&&apcu_fetch('smeldingen')=='On'&&apcu_fetch('tweg')<time-178&&apcu_fetch('tslapen')<time-178){
		sw('sirene','On');
		telegram('Beweging living om '.strftime("%k:%M:%S",time),false,3);
	}
}
