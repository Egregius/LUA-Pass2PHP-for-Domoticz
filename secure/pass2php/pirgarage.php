<?php
if($s['pirgarage']=='On'){
	if((time<strtotime('10:30')||time>strtotime('18:30')||apcu_fetch('zon')<1500)&&$s['garage']=='Off'){
		sw(apcu_fetch('igarage'),'On','garage');
		apcu_store('tgarage',time);
	}
	if(($s['weg']=='On'||$s['slapen']=='On')&&$s['meldingen']=='On'&&apcu_fetch('tweg')<time-178&&apcu_fetch('tslapen')<time-178){
		sw(apcu_fetch('isirene'),'On');
		$msg='Achterdeur open om '.apcu_fetch('tachterdeur');
		telegram($msg,false,3);
	}
}