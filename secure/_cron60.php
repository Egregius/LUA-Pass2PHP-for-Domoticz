<?php
$items=array('eettafel','zithoek','tobi','kamer','alex');
foreach($items as $item){
	$stat=apcu_fetch('s'.$item);
	if($stat!='Off'){
		$action=apcu_fetch('dimaction'.$item);
		if($action==1){
			$level=floor($stat*0.95);
			if($level<2)$level=0;
			if($level==20)$level=19;
			sl($item,$level);
			if($level==0)apcu_store('dimaction'.$item,0);
		}elseif($action==2){
			$level=$stat+2;
			if($level==20)$level=21;
			if($level>60)$level=60;
			sl($item,$level);
			if($level==60)apcu_store('dimaction'.$item,0);
		}
	}
}

$buiten_temp=apcu_fetch('sbuiten_temp');
$stamp=sprintf("%s",date("Y-m-d H:i"));
$living=apcu_fetch('sliving_temp');
$badkamer=apcu_fetch('sbadkamer_temp');
$kamer=apcu_fetch('skamer_temp');
$tobi=apcu_fetch('stobi_temp');
$alex=apcu_fetch('salex_temp');
$zolder=apcu_fetch('szolder_temp');
if($living>0&&$badkamer>0){
	$query="INSERT IGNORE INTO `temp` (`stamp`,`buiten`,`living`,`badkamer`,`kamer`,`tobi`,`alex`,`zolder`) VALUES ('$stamp','$buiten_temp','$living','$badkamer','$kamer','$tobi','$alex','$zolder');";
	$db=new mysqli('server','user','password','database');if($db->connect_errno>0)die('Unable to connect to database [' . $db->connect_error . ']');if(!$result=$db->query($query))die('There was an error running the query ['.$query .' - ' . $db->error . ']');$db->close();
}
if($Weg==0){
	$brander=apcu_fetch('sbrander');
	if($living>22&&$brander==1){
		if(apcu_fetch('telegramtempliving')<time-3600){
			apcu_store('telegramtempliving',time);
			telegram('Te warm in living, '.$living.' °C. Controleer verwarming',false,2);
		}
	}
	if(time>strtotime('16:00')){
		if(apcu_fetch('sraamalex')=='Open'&&$alex<16){
			if(apcu_fetch('telegramraamalex')<time-1800){
				apcu_store('telegramraamalex',time);
				telegram('Raam Alex dicht doen, '.$alex.' °C.',false,2);
			}
		}
	}
}

$tdiepvries=apcu_fetch('sdiepvries_temp');
$diepvries=apcu_fetch('sdiepvries');
$diepvries_set=apcu_fetch('sdiepvries_set');
$timediepvries=apcu_fetch('tdiepvries');
if($diepvries=='Off'&&$tdiepvries>$diepvries_set&&$timediepvries<time-1800)sw('diepvries','On','Diepvries On '.$tdiepvries.'°C');
elseif($diepvries=='On'&&$tdiepvries<=$diepvries_set&&$timediepvries<time-300)sw('diepvries','Off','Diepvries Off '.$tdiepvries.'°C');
elseif($diepvries=='On'&&$timediepvries<time-7200)sw('diepvries','Off','Diepvries Off '.$tdiepvries.'°C, was aan voor meer dan 2 uur');

if($auto){
	$garage=apcu_fetch('sgarage');
	$tgarage=time-apcu_fetch('tgarage');
	$pirgarage=apcu_fetch('spirgarage');
	$tpirgarage=time-apcu_fetch('tpirgarage');
	$poort=apcu_fetch('spoort');
	$achterdeur=apcu_fetch('sachterdeur');
	$dampkap=apcu_fetch('sdampkap');
	if((($garage=='On'&&$tgarage>180)||($pirgarage=='On'&&$tpirgarage>180))&&time>strtotime('7:00')&&time<strtotime('23:00')&&$poort=='Closed'&&$achterdeur=='Open'){
		if($dampkap=='Off')double('dampkap','On');
	}elseif(($garage=='Off'&&$tgarage>270&&$pirgarage=='Off'&&$tpirgarage>270)||$poort=='Open'||$achterdeur=='Closed'){
		if($dampkap=='On'){
			$tdampkap=time-apcu_fetch('tdampkap');
			if(apcu_fetch('dampkapmanueel')==1){
				if($tdampkap>1200){
					double('dampkap','Off','1');
					apcu_store('dampkapmanueel',0);
				}
			}elseif($tdampkap>350)double('dampkap','Off','1');
		}
	}
}


