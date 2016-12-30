<?php
if($status=='On'){
	if(apcu_fetch('sdenon')=='Off'){
		if(apcu_fetch('zon')<100){
			if(apcu_fetch('swasbak')=='Off')sw(apcu_fetch('iwasbak'),'On','wasbak');
			if(apcu_fetch('sbureel')=='Off')sw(apcu_fetch('ibureel'),'On','bureel');
		}
		if(apcu_fetch('sweg')=='Off'&&apcu_fetch('sslapen')=='Off')include('pass2php/miniliving1l.php');
	}
	if((apcu_fetch('sweg')=='On'||apcu_fetch('sslapen')=='On')&&apcu_fetch('smeldingen')=='On'&&apcu_fetch('tweg')<time-178&&apcu_fetch('tslapen')<time-178){
		sw(apcu_fetch('isirene'),'On');
		telegram('Beweging living om '.strftime("%k:%M:%S",time),false,3);
	}
}
