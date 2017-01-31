<?php
if($status=='On'){
	if(apcu_fetch('sachterdeur')!='Open'){sw('deurbel','On');telegram('Opgelet: Achterdeur open!',false,3);die();}
	if(apcu_fetch('sraamliving')!='Closed'){sw('deurbel','On');telegram('Opgelet: Raam Living open!',false,3);die();}
	if(apcu_fetch('spoort')!='Closed'){sw('deurbel','On');telegram('Opgelet: Poort open!',false,3);die();}
}
if(apcu_fetch('sslapen')=='Off')sw('slapen','On');
if($status=='On'){
	apcu_store('cron10',1);
	double('GroheRed','Off');
	/*if(apcu_fetch('sluifel')!='Open')sw('luifel','Off','zonneluifel dicht');*/
}
if(apcu_fetch('slichten_auto')=='Off')sw('lichten_auto','On');
/*if(apcu_fetch('sluifel']!='Open')sw('luifel','Off','zonneluifel dicht');*/
