<?php
if($s['pirkeuken']=="On"){
	if($s['keuken']=='Off'&&$s['wasbak']=='Off'&&$s['werkblad']=='Off'&&$s['kookplaat']=='Off'&&apcu_fetch('zon')<500){
		sw(apcu_fetch('iwasbak'),'On','wasbak');
		apcu_store('twasbak',time);
	}
	if(($s['weg']=='On'||$s['slapen']=='On')&&$s['meldingen']=='On'&&apcu_fetch('tweg')<time-178&&apcu_fetch('tslapen')<time-178){
		sw(apcu_fetch('isirene'),'On');
		$msg='Beweging keuken om '.time;
		telegram($msg,false,3);
	}
}
apcu_store('spirkeuken',$s['pirkeuken']);