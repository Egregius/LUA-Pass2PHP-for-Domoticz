<?php
if($s['achterdeur']!="Open"){
	if(($s['weg']=='On'||$s['slapen']=='On')&&$s['meldingen']=='On'){
		sw(apcu_fetch('isirene'),'On');
		$msg='Achterdeur open om '.apcu_fetch('tachterdeur');
		telegram($msg,false,3);
	}
}