<?php
if($s['raamliving']=='Open'){
	if(($s['weg']=='On'||$s['slapen']=='On')&&$s['meldingen']=='On'&&apcu_fetch('tweg')<time-178&&apcu_fetch('tslapen')<time-178){
		sw(apcu_fetch('isirene'),'On');
		$msg='Raam living open om '.time;
		telegram($msg,false,3);
	}
}