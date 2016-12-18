<?php
if($s['slapen']=='On'){
	if($s['achterdeur']!='Open'){sw($i['deurbel'],'On');telegram('Opgelet: Achterdeur open!',false,2);}
	if($s['raamliving']!='Closed'){sw($i['deurbel'],'On');telegram('Opgelet: Raam Living open!',false,2);}
	if($s['poort']!='Closed'){sw($i['deurbel'],'On');telegram('Opgelet: Poort open!',false,2);}
	alles('Slapen');
	double($i['GroheRed'],'Off');
	/*if($s['luifel']!='Open')sw($i['luifel'],'Off','zonneluifel dicht');*/
}
if($s['lichten_auto']=='Off')sw($i['lichten_auto'],'On','lichten auto aan');