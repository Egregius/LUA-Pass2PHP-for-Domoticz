<?php
if($s['poort']=='Open'){
	if(apcu_fetch('zon')<1500&&$s['garage']=='Off')
		sw(apcu_fetch('igarage'),'On','garage');
	if(($s['weg']=='On'||$s['slapen']=='On')&&$s['meldingen']=='On'&&apcu_fetch('tweg')<time-178&&apcu_fetch('tslapen')<time-178){
		sw(apcu_fetch('isirene'),'On');
		$msg='Poort open om '.time;
		telegram($msg,false,3);
	}
}