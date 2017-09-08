<?php
$items=array('eettafel','zithoek','tobi','kamer','alex');
foreach($items as $item){
	$stat=status(''.$item);
	if($stat!='Off'){
		$action=status('dimaction'.$item);
		if($action==1){
			$level=floor($stat*0.95);
			if($level<2)$level=0;
			if($level==20)$level=19;
			sl($item,$level);
			if($level==0)setstatus('dimaction'.$item,0);
		}elseif($action==2){
			$level=$stat+2;
			if($level==20)$level=21;
			if($level>60)$level=60;
			sl($item,$level);
			if($level==60)setstatus('dimaction'.$item,0);
		}
	}
}

$buiten_temp=status('buiten_temp');
$stamp=sprintf("%s",date("Y-m-d H:i"));
$living=status('living_temp');
$badkamer=status('badkamer_temp');
$kamer=status('kamer_temp');
$tobi=status('tobi_temp');
$alex=status('alex_temp');
$zolder=status('zolder_temp');
$diepvries=status('diepvries_temp');
if($living>0&&$badkamer>0){
	$query="INSERT IGNORE INTO `temp` (`stamp`,`buiten`,`living`,`badkamer`,`kamer`,`tobi`,`alex`,`zolder`) VALUES ('$stamp','$buiten_temp','$living','$badkamer','$kamer','$tobi','$alex','$zolder');";
	$db=new mysqli('server','user','password','database');
	if($db->connect_errno>0)die('Unable to connect to database [' . $db->connect_error . ']');
	if(!$result=$db->query($query))die('There was an error running the query ['.$query .' - ' . $db->error . ']');
	$query="INSERT IGNORE INTO `diepvries` (`stamp`,`temp`) VALUES ('$stamp','$diepvries');";
	if(!$result=$db->query($query))die('There was an error running the query ['.$query .' - ' . $db->error . ']');
	$db->close();
}
if($Weg==0){
	$brander=status('brander');
	if($living>22&&$brander==1){
		if(timestamp('elegramtempliving')<time-3600){
			settimestamp('telegramtempliving',time);
			telegram('Te warm in living, '.$living.' °C. Controleer verwarming',false,2);
		}
	}
	if(time>strtotime('16:00')){
		if(status('raamalex')=='Open'&&$alex<16){
			if(timestamp('elegramraamalex')<time-1800){
				settimestamp('telegramraamalex',time);
				telegram('Raam Alex dicht doen, '.$alex.' °C.',false,2);
			}
		}
	}
}

$tdiepvries=status('diepvries_temp');
$diepvries=status('diepvries');
$diepvries_set=status('diepvries_set');
$timediepvries=timestamp('diepvries');
if(   $diepvries!='On'&&$tdiepvries>$diepvries_set&&$timediepvries<time-1780)sw('diepvries','On','Diepvries On '.$tdiepvries.'°C');
elseif($diepvries!='Off'&&$tdiepvries<=$diepvries_set&&$timediepvries<time-280)sw('diepvries','Off','Diepvries Off '.$tdiepvries.'°C');
elseif($diepvries!='Off'&&$timediepvries<time-7200)sw('diepvries','Off','Diepvries Off '.$tdiepvries.'°C, was aan voor meer dan 2 uur');

if($auto){
	$garage=status('garage');
	$tgarage=time-timestamp('garage');
	$pirgarage=status('pirgarage');
	$tpirgarage=time-timestamp('pirgarage');
	$poort=status('poort');
	$achterdeur=status('achterdeur');
	$dampkap=status('dampkap');
	if((($garage=='On'&&$tgarage>180)||($pirgarage=='On'&&$tpirgarage>180))&&time>strtotime('7:00')&&time<strtotime('23:00')&&$poort=='Closed'&&$achterdeur=='Open'){
		if($dampkap=='Off')double('dampkap','On');
	}elseif(($garage=='Off'&&$tgarage>270&&$pirgarage=='Off'&&$tpirgarage>270)||$poort=='Open'||$achterdeur=='Closed'){
		if($dampkap=='On'){
			$tdampkap=time-timestamp('dampkap');
			if(status('dampkapmanueel')==1){
				if($tdampkap>1200){
					double('dampkap','Off','1');
					setstatus('dampkapmanueel',0);
				}
			}elseif($tdampkap>350)double('dampkap','Off','1');
		}
	}
}

$nas=status('nas');
if(pingport('192.168.2.10',1598)==1){
	$prevcheck=status('check192.168.2.10:1598');
	if($prevcheck>0)setstatus('check192.168.2.10:1598',0);
	if($nas!='On'){
		setstatus('nas','On');
	}
}else{
	$check=status('check192.168.2.10:1598')+1;
	if($check>0)setstatus('check192.168.2.10:1598',$check);
	if($check>=3&&$nas!='Off'){
		setstatus('nas','Off');
	}
}
?>
