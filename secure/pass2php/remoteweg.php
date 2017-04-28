<?php
if($status=="On"){
	if(apcu_fetch('sachterdeur')!='Open'){sw('deurbel','On');telegram('Opgelet: Achterdeur open!',false,2);die();}
	if(apcu_fetch('sraamliving')!='Closed'){sw('deurbel','On');telegram('Opgelet: Raam Living open!',false,2);die();}
	sw('garage','Off');
	apcu_store('Weg',2);
	apcu_store('tWeg',time);
	apcu_store('cron10',1);
	sw('GroheRed','Off');
	sw('badkamervuur','Off');
}else{
	apcu_store('Weg',0);
	apcu_store('tWeg',time);
	if(apcu_fetch('spoortrf')!='On')sw('poortrf','On');
}