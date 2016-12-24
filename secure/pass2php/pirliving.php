<?php
if($s['pirliving']=='On'){
	if($s['denon']=='Off'&&$s['weg']=='Off'&&$s['slapen']=='Off'){
		if(apcu_fetch('zon')<100){
			if($s['wasbak']=='Off')sw(apcu_fetch('iwasbak'),'On','wasbak');
			if($s['bureel']=='Off')sw(apcu_fetch('ibureel'),'On','bureel');
		}
		include('pass2php/miniliving1l.php');
	}
	if(($s['weg']=='On'||$s['slapen']=='On')&&$s['meldingen']=='On'&&apcu_fetch('tweg')<time-178&&apcu_fetch('tslapen')<time-178){
		sw(apcu_fetch('isirene'),'On');
		$msg='Beweging living om '.time;
		telegram($msg,false,3);
	}
}
apcu_store('spirliving',$s['pirliving']);