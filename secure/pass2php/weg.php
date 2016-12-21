<?php
if($s['weg']=="On"){
	if($s['achterdeur']!='Open'){
		sw($i['deurbel'],'On');
		telegram('Opgelet: Achterdeur open!',false,2);
	}
	if($s['raamliving']!='Closed'){
		sw($i['deurbel'],'On');
		telegram('Opgelet: Raam Living open!',false,2);
	}
	alles('Off');
	double($i['GroheRed'],'Off');
	double($i['badkamervuur'],'Off');
}
else{
	if($s['poortrf']=='Off')sw($i['poortrf'],'On','Poort RF');
}