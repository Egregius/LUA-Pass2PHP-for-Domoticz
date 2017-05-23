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
if(((apcu_fetch('sgarage')=='On'&&apcu_fetch('tgarage')<time-180)||(apcu_fetch('spirgarage')=='On'&&apcu_fetch('tpirgarage')<time-180))&&time>strtotime('7:00')&&time<strtotime('23:00')&&apcu_fetch('spoort')=='Closed'&&apcu_fetch('sachterdeur')=='Open'){
	if(apcu_fetch('sdampkap')=='Off')double('dampkap','On');
}elseif((apcu_fetch('sgarage')=='Off'&&apcu_fetch('tgarage')<time-350)||(apcu_fetch('spirgarage')=='Off'&&apcu_fetch('tpirgarage')<time-350)||apcu_fetch('spoort')=='Open'||apcu_fetch('sachterdeur')=='Closed'){
	if(apcu_fetch('sdampkap')=='On'){
		if(apcu_fetch('dampkapmanueel')==1){
			if(apcu_fetch('tdampkap')<time-1200){
				double('dampkap','Off','1');
				apcu_store('dampkapmanueel',0);
			}elseif(apcu_fetch('tdampkap')<time-350)double('dampkap','Off','1');
		}
	}
}
if($Weg>0){
	if($Weg==2){
		$items=array('pirgarage','pirkeuken','pirliving','pirinkom','pirhall');
		foreach($items as $item)if(apcu_fetch('s'.$item)!='Off')ud($item,0,'Off');
		$items=array('garage','denon','bureel','tv','tvled','kristal','eettafel','zithoek','terras','tuin','voordeur','hall','inkom','keuken','werkblad','wasbak','kookplaat','sony','kamer','tobi','alex','lichtbadkamer1','lichtbadkamer2','badkamervuur');
		foreach($items as $item)if(apcu_fetch('s'.$item)!='Off')if(apcu_fetch('t'.$item)<time)sw($item,'Off');
	}elseif($Weg==1){
		$items=array('pirgarage','pirkeuken','pirliving','pirinkom');
		foreach($items as $item)if(apcu_fetch('s'.$item)!='Off')ud($item,0,'Off');
		$items=array('hall','bureel','denon','tv','tvled','kristal','eettafel','zithoek','garage','terras','tuin','voordeur','keuken','werkblad','wasbak','kookplaat','zolderg','dampkap');
		foreach($items as $item)if(apcu_fetch('s'.$item)!='Off')if(apcu_fetch('t'.$item)<time)sw($item,'Off');
	}
	$items=array('living','badkamer','kamer','tobi','alex');
	foreach($items as $item){${'setpoint'.$item}=apcu_fetch('setpoint'.$item);if(${'setpoint'.$item}!=0&&apcu_fetch('t'.$item)<time-21600)apcu_store('setpoint'.$item,0);}
	$items=array('tobi','living','kamer','alex');
}
$buiten_temp=apcu_fetch('sbuiten_temp');
$stamp=sprintf("%s",date("Y-m-d H:i"));
$living=apcu_fetch('sliving_temp');
$badkamer=apcu_fetch('sbadkamer_temp');
$kamer=apcu_fetch('skamer_temp');
$tobi=apcu_fetch('stobi_temp');
$alex=apcu_fetch('salex_temp');
$zolder=apcu_fetch('szolder_temp');
$s_living=apcu_fetch('sliving_set');
$s_badkamer=apcu_fetch('sbadkamer_set');
$s_kamer=apcu_fetch('skamer_set');
$s_tobi=apcu_fetch('stobi_set');
$s_alex=apcu_fetch('salex_set');
if(apcu_fetch('sbrander')=='On')$brander=1;else $brander=0;
if(apcu_fetch('sbadkamervuur')=='On')$badkamervuur=1;else $badkamervuur=0;
if($living>0&&$badkamer>0){
	$query="INSERT IGNORE INTO `temp` (`stamp`,`buiten`,`living`,`badkamer`,`kamer`,`tobi`,`alex`,`zolder`,`s_living`,`s_badkamer`,`s_kamer`,`s_tobi`,`s_alex`,`brander`,`badkamervuur`) VALUES ('$stamp','$buiten_temp','$living','$badkamer','$kamer','$tobi','$alex','$zolder','$s_living','$s_badkamer','$s_kamer','$s_tobi','$s_alex','$brander','$badkamervuur');";
	$db=new mysqli('server','user','password','database');if($db->connect_errno>0)die('Unable to connect to database [' . $db->connect_error . ']');if(!$result=$db->query($query))die('There was an error running the query ['.$query .' - ' . $db->error . ']');$db->close();
}
if($Weg==0){
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
$buien=round(apcu_fetch('buien'),8);
$Tregenpomp=apcu_fetch('tregenpomp');
if($buien>0){
	$pomppauze=21600/$buien;
	if($pomppauze>21600)$pomppauze=21600;
	elseif($pomppauze<300)$pomppauze=300;
}else $pomppauze=86400;
if(apcu_fetch('sregenpomp')=='On'&&$Tregenpomp<time-57)sw('regenpomp','Off','was on for '.convertToHours(time-$Tregenpomp));
elseif(apcu_fetch('sregenpomp')=='Off'&&$Tregenpomp<time-$pomppauze)sw('regenpomp','On','was off for '.convertToHours(time-$Tregenpomp));

$tdiepvries=apcu_fetch('sdiepvries_temp');
$diepvries=apcu_fetch('sdiepvries');
$diepvries_set=apcu_fetch('sdiepvries_set');
$timediepvries=time-apcu_fetch('tdiepvries');
if($diepvries=='Off'&&$tdiepvries>$diepvries_set&&$timediepvries>1200)sw('diepvries','On','Diepvries On '.$tdiepvries.'°C');
elseif($diepvries=='On'&&$tdiepvries<=$diepvries_set&&$timediepvries>300)sw('diepvries','Off','Diepvries Off '.$tdiepvries.'°C');
elseif($diepvries=='On'&&$timediepvries>7200)sw('diepvries','Off','Diepvries Off '.$tdiepvries.'°C, was aan voor meer dan 2 uur');

$zonopen=1500;
$luifel=apcu_fetch('sluifel');
$maxbuien=4;
$wind=round(apcu_fetch('wind'),3);
$winddir=apcu_fetch('winddir');
$sliving_temp=apcu_fetch('sliving_temp');
if(	   $winddir=='S')	$maxwind=10;
elseif($winddir=='SE')	$maxwind=10;
elseif($winddir=='SSW')	$maxwind=10;
elseif($winddir=='West')$maxwind=10;
elseif($winddir=='W')	$maxwind=10;
elseif($winddir=='WSW')	$maxwind=8;
elseif($winddir=='WNW')	$maxwind=10;
elseif($winddir=='N')	$maxwind=10;
elseif($winddir=='NNE')	$maxwind=10;
else $maxwind=10;
//telegram("Luifel: __buien = $buien | $maxbuien __wind = $wind | $maxwind __winddir = $winddir __zon = $zon");
if($luifel!='Closed'&&($wind>$maxwind||$buien>$maxbuien||$zon==0||$sliving_temp<=20)){
	sw('luifel','Off');
	telegram("Luifel dicht: __buien=$buien | $maxbuien __wind=$wind | $maxwind $winddir");
}elseif($luifel!='Open'&&$wind<$maxwind&&$buien<$maxbuien&&$sliving_temp>=20.4&&$zon>$zonopen&&apcu_fetch('tluifel')<time-598){
	sw('luifel','On');
	telegram("Luifel open: __buien=$buien | $maxbuien __wind=$wind | $maxwind $winddir __zon:$zon __living:$sliving_temp");

}
if(apcu_fetch('tluifel')<time-3598){
	if($luifel=='Open')sw('luifel','On');
	else sw('luifel','Off');
}

if(apcu_fetch('skodi')=='On'&&apcu_fetch('tkodi')<time-300){
	$ctx=stream_context_create(array('http'=>array('timeout' => 5)));
	if($Weg>0)file_get_contents('http://192.168.2.7:1597/jsonrpc?request={"jsonrpc":"2.0","id":1,"method":"System.Shutdown"}',false,$ctx);
	if(apcu_fetch('tkodi')<time-298){if(pingport('192.168.2.7',1597)==1){$prevcheck=apcu_fetch('check192.168.2.57:1597');if($prevcheck>0)apcu_store('check192.168.2.57:1597',0);}else{$check=apcu_fetch('check192.168.2.57:1597')+1;if($check>0)apcu_store('check192.168.2.57:1597',$check);if($check>=5)sw('kodi','Off');}}
}


include('gcal/gcal.php');
