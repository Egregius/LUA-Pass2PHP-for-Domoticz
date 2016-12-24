<?php
if($s['weg']=="On"){
	if($s['achterdeur']!='Open'){
		sw(apcu_fetch('ideurbel'),'On');
		telegram('Opgelet: Achterdeur open!',false,2);
	}
	if($s['raamliving']!='Closed'){
		sw(apcu_fetch('ideurbel'),'On');
		telegram('Opgelet: Raam Living open!',false,2);
	}
	$items=array('denon','bureel','tv','tvled','kristal','eettafel','zithoek','garage','terras','tuin','voordeur','keuken','werkblad','wasbak','kookplaat','sony','kamer','tobi','alex');
	foreach($items as $item)if($s[$item]!='Off'&&apcu_fetch('t'.$item)<time-$uit)sw(apcu_fetch('i'.$item),'Off',$item);
	$items=array('lichtbadkamer1','lichtbadkamer2','badkamervuur');
	foreach($items as $item)if($s[$item]!='Off'&&apcu_fetch('t'.$item)<time-$uit)sw(apcu_fetch('i'.$item),'Off',$item);

	double(apcu_fetch('iGroheRed'),'Off');
	double(apcu_fetch('ibadkamervuur'),'Off');
}
else{
	if($s['poortrf']=='Off')sw(apcu_fetch('ipoortrf'),'On','Poort RF');
}