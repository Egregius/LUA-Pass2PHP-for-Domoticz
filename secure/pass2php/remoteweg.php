<?php
if($status=="On"){
	if(status('achterdeur')!=='Open'){sw('deurbel','On');sw('poortrf','Off');telegram('Opgelet: Achterdeur open!',false,2);die('achterdeur open');}
	if(status('raamliving')!=='Closed'){sw('deurbel','On');sw('poortrf','Off');telegram('Opgelet: Raam Living open!',false,2);die('raam living open');}
	sw('garage','Off');
	setstatus('Weg',2);
	sw('GroheRed','Off');
	sw('badkamervuur','Off');
	if(status('dimactionluifel')==0)setstatus('dimactionluifel',1);
}else{
	setstatus('Weg',0);
	if(status('poortrf')!=='On')sw('poortrf','On');
	if(status('sirene')!='Group Off')double('sirene','Off');
}
?>