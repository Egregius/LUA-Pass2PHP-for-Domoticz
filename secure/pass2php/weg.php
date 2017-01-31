<?php
if($status=="On"){
	if(apcu_fetch('sachterdeur')!='Open'){sw('deurbel','On');telegram('Opgelet: Achterdeur open!',false,2);die();}
	if(apcu_fetch('sraamliving')!='Closed'){sw('deurbel','On');telegram('Opgelet: Raam Living open!',false,2);die();}
	apcu_store('cron10',1);
	double('GroheRed','Off');
	double('badkamervuur','Off');
}else{
	if(apcu_fetch('spoortrf')=='Off')sw('poortrf','On');
}