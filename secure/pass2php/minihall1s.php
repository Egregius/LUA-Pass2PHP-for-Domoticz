<?php
if($status=='On'){
	if($auto){
		if(status('achterdeur')!='Open'){sw('deurbel','On');telegram('Opgelet: Achterdeur open!',false,2);die('Opgelet: Achterdeur open!');}
		if(status('raamliving')!='Closed'){sw('deurbel','On');telegram('Opgelet: Raam Living open!',false,2);die('Opgelet: Raam Living open!');}
		if(status('poort')!='Closed'){sw('deurbel','On');telegram('Opgelet: Poort open!',false,2);die('Opgelet: Poort open!');}
	}
	if(status('bureeltobi')=='On'){sw('deurbel','On');telegram('Opgelet: bureel Tobi aan!',false,2);die('Opgelet: bureel Tobi aan!');}
	if($Weg!=1)setstatus('Weg',1);
	if(status('dimactionluifel')==0)setstatus('dimactionluifel',1);
	sw(array('slapen'),'Off');
	if(!$auto)sw('lichten_auto','On');
}
?>