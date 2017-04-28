<?php
if($status=='On'){
	if(apcu_fetch('sachterdeur')!='Open'){sw('deurbel','On');telegram('Opgelet: Achterdeur open!',false,2);die();}
	if(apcu_fetch('sraamliving')!='Closed'){sw('deurbel','On');telegram('Opgelet: Raam Living open!',false,2);die();}
	if(apcu_fetch('spoort')!='Closed'){sw('deurbel','On');telegram('Opgelet: Poort open!',false,2);die();}
	if(apcu_fetch('sbureeltobi')=='On'){sw('deurbel','On');telegram('Opgelet: bureel Tobi aan!',false,2);die();}
	if($Weg!=1){apcu_store('Weg',1);apcu_store('tWeg',time);}
	if(apcu_fetch('sGroheRed')=='On')double('GroheRed','Off');
	if(apcu_fetch('sdampkap')=='On')double('dampkap','Off');
	/*if(apcu_fetch('sluifel')!='Open')sw('luifel','Off','zonneluifel dicht');*/
	if(!$auto)sw('lichten_auto','On');
	if(apcu_fetch('spoortrf')=='On')sw('poortrf','Off');
}