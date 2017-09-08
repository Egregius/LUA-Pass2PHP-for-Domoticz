<?php
if(file_exists('/var/log/cache/sbuiten_temp.cache'))$prevtemp=status('buiten_temp');
else{
	$query="SELECT buiten from temp order by stamp desc limit 0,1;";
	$db=new mysqli('server','username','password','database');
	if($db->connect_errno>0)die('Unable to connect to database ['.$db->connect_error.']');
	if(!$result=$db->query($query))die('There was an error running the query ['.$query.'-'.$db->error.']');
	while($row=$result->fetch_assoc())$prevtemp=$row['buiten'];$result->free();
	//telegram('Buitentemp fetched from database: '.$prevtemp);
	$db->close();
}
$prevwind=status('wind');
$prevbuien=status('buien');

$wu=json_decode(file_get_contents('http://api.wunderground.com/api/1a2b3c4d5e/conditions/q/BX/Beitem.json'),true);
if(isset($wu['current_observation'])){
	$lastobservation=status('wu-observation');
	if(isset($wu['current_observation']['estimated']['estimated']))goto exitwunderground;
	elseif($wu['current_observation']['observation_epoch']<=$lastobservation)goto exitwunderground;
	if(isset($wu['current_observation']['temp_c'])){$wutemp=$wu['current_observation']['temp_c'];if($wutemp>$prevtemp+0.5)$wutemp=$prevtemp+0.5;elseif($wutemp<$prevtemp-0.5)$wutemp=$prevtemp-0.5;}
	if(isset($wu['current_observation']['wind_kph']))$wuwind=$wu['current_observation']['wind_kph'];
	if(isset($wu['current_observation']['wind_gust_kph']))if($wu['current_observation']['wind_gust_kph']>$wuwind)$wuwind=$wu['current_observation']['wind_gust_kph'];
	if(isset($wu['current_observation']['precip_1hr_metric']))$wubuien=$wu['current_observation']['precip_1hr_metric']*35;
	if(isset($wu['current_observation']['wind_dir']))setstatus('winddir',$wu['current_observation']['wind_dir']);
	if(isset($wu['current_observation']['icon']))setstatus('icon',$wu['current_observation']['icon']);
	if(isset($wuwind))$wuwind=$wuwind / 3.6;
}
exitwunderground:
$maxtemp=1;
$maxrain=-1;
$ds=json_decode(file_get_contents('https://api.darksky.net/forecast/1a2b3c4d5e/51.9020861,3.2164103?units=si'),true);
if(isset($ds['currently'])){
	if(isset($ds['currently']['temperature'])){$dstemp=$ds['currently']['temperature'];if($dstemp>$prevtemp+0.5)$dstemp=$prevtemp+0.5;elseif($dstemp<$prevtemp-0.5)$dstemp=$prevtemp-0.5;}
	if(isset($ds['currently']['windSpeed'])){
		$dswind=$ds['currently']['windSpeed'];
		if($ds['currently']['windGust']>$dswind)$dswind=$ds['currently']['windGust'];
	}
	if(isset($dswind))$dswind=$dswind / 3.6;
	if(isset($ds['minutely']['data'])){
		$dsbuien=0;
		foreach($ds['minutely']['data'] as $i){
			if($i['time']>time&&$i['time']<time+1800){
				if($i['precipProbability']*50>$dsbuien)$dsbuien=$i['precipProbability']*35;
			}
		}
	}
	if(isset($ds['hourly']['data'])){
		foreach($ds['hourly']['data'] as $i){
			if($i['time']>time&&$i['time']<time+3600*3){
				if($i['temperature']>$maxtemp)$maxtemp=$i['temperature'];
			}
			if($i['precipIntensity']>$maxrain)$maxrain=$i['precipIntensity'];
		}
		setstatus('maxtemp',$maxtemp);
		setstatus('maxrain',$maxrain);
	}
}
$rains=file_get_contents('http://gadgets.buienradar.nl/data/raintext/?lat=51.89&lon=3.21');
if(!empty($rains)){
	$rains=str_split($rains,11);$totalrain=0;$aantal=0;
	foreach($rains as $rain){
		$aantal=$aantal+1;
		$totalrain=$totalrain+substr($rain,0,3);
		if($aantal==7)break;
	}
	$newbuien=$totalrain/7;
	if($newbuien>70)$newbuien=70;
}

if(isset($prevtemp)&&isset($wutemp)&&isset($dstemp))setstatus('buiten_temp',($prevtemp+$wutemp+$dstemp)/3);
elseif(isset($prevtemp)&&isset($wutemp))setstatus('buiten_temp',($prevtemp+$wutemp)/2);
elseif(isset($prevtemp)&&isset($dstemp))setstatus('buiten_temp',($prevtemp+$dstemp)/2);
elseif(isset($wutemp)&&isset($dstemp))setstatus('buiten_temp',($wutemp+$dstemp)/2);
elseif(isset($wutemp))setstatus('buiten_temp',$wutemp);
elseif(isset($dstemp))setstatus('buiten_temp',$dstemp);

if(isset($prevwind)&&isset($wuwind)&&isset($dswind))$wind=($prevwind+$wuwind+$dswind)/3;
elseif(isset($prevwind)&&isset($wuwind))$wind=($prevwind+$wuwind)/2;
elseif(isset($prevwind)&&isset($dswind))$wind=($prevwind+$dswind)/2;
elseif(isset($wuwind)&&isset($dswind))$wind=($wuwind+$dswind)/2;
elseif(isset($wuwind))$wind=$wuwind;
elseif(isset($dswind))$wind=$dswind;
if($wind!=$prevwind)setstatus('wind',$wind);
$windhist=json_decode(status('windhist'));
$windhist[]=$wind;
$windhist=array_slice($windhist,-4);
setstatus('windhist',json_encode($windhist));
if(isset($prevbuien)&&isset($wubuien)&&isset($dsbuien)&&isset($newbuien))$buien=($prevbuien+$wubuien+$dsbuien+$newbuien)/4;
elseif(isset($prevbuien)&&isset($wubuien)&&isset($dsbuien))$buien=($prevbuien+$wubuien+$dsbuien)/3;
elseif(isset($prevbuien)&&isset($wubuien)&&isset($newbuien))$buien=($prevbuien+$wubuien+$newbuien)/3;
elseif(isset($prevbuien)&&isset($dsbuien)&&isset($newbuien))$buien=($prevbuien+$dsbuien+$newbuien)/3;
elseif(isset($prevbuien)&&isset($newbuien))$buien=($prevbuien+$newbuien)/2;
elseif(isset($prevbuien)&&isset($wubuien))$buien=($prevbuien+$wubuien)/2;
elseif(isset($prevbuien)&&isset($dsbuien))$buien=($prevbuien+$dsbuien)/2;
elseif(isset($newbuien))$buien=$newbuien;
elseif(isset($wubuien))$buien=$wubuien;
elseif(isset($dsbuien))$buien=$dsbuien;
$buien=round($buien,0);
if($buien>100)$buien=100;
setstatus('buien',$buien);
if(!isset($wubuien))$wubuien=0;
if(!isset($dsbuien))$dsbuien=0;
if(!isset($newbuien))$newbuien=0;
$query="INSERT IGNORE INTO `regen` (`buienradar`,`wunderground`,`darksky`,`buien`) VALUES ('$newbuien','$wubuien','$dsbuien','$buien');";
$db=new mysqli('server','user','password','database');
if($db->connect_errno>0)die('Unable to connect to database [' . $db->connect_error . ']');
if(!$result=$db->query($query))die('There was an error running the query ['.$query .' - ' . $db->error . ']');
$db->close();
$buiten_temp=status('buiten_temp');
$tgrohe=time-timestamp('GroheRed');
if(status('GroheRed')=='On'){
	if(status('wasbak')=='Off'&&status('werkblad')=='Off'&&status('keuken')=='Off'&&status('kookplaat')=='Off'&&$tgrohe>240&&status('Usage_grohered')<50)sw('GroheRed','Off');
	if($tgrohe>900)sw('GroheRed','Off');
}else{
	if($tgrohe>120&&(status('pirkeuken')=='On'&&timestamp('pirkeuken')<time-190)||(status('wasbak')=='On'&&timestamp('wasbak')<time-190)||(status('keuken')=='On'&&timestamp('keuken')<time-190)||(status('kookplaat')=='On'&&timestamp('kookplaat')<time-190))sw('GroheRed','On');
}
if(status('meldingen')=='On'&&timestamp('Weg')<time-300){
	$items=array('living_temp','badkamer_temp','kamer_temp','tobi_temp','alex_temp','zolder_temp');$avg=0;
	foreach($items as $item)$avg=$avg+status($item);$avg=$avg/6;
	foreach($items as $item){
		$temp=status($item);
		if($temp>$avg+5&&$temp>25){
			$msg='T '.$item.'='.$temp.'°C. AVG='.round($avg,1).'°C';
			if(timestamp('alerttemp'.$item)<time-3598){telegram($msg,false,2);ios($msg);settimestamp('alerttemp'.$item);}
		}
		if(timestamp($item)<time-43150){if(timestamp('alerttempupd'.$item)<time-43100){telegram($item.' not updated');settimestamp('alerttempupd'.$item);}}}
	$devices=array(/*'tobiZ',*/'alexZ',/*'livingZ','livingZZ',*/'kamerZ');
	foreach($devices as $device){if(timestamp($device)<time-2000){
		if(timestamp('nocom'.$device)<time-43190){
			telegram($device.' geen communicatie',true);
			settimestamp('nocom'.$device);}
		}
	}
	if($Weg==0){if(($buiten_temp>status('kamer_temp')&&$buiten_temp>status('tobi_temp')&&$buiten_temp>status('alex_temp'))&&$buiten_temp>22&&(status('kamer_temp')>20||status('tobi_temp')>20||status('alex_temp')>20)&&(status('raamkamer')=='Open'||status('raamtobi')=='Open'||status('raamalex')=='Open'))if((int)timestamp('timeramen')<time-43190){telegram('Ramen boven dicht doen, te warm buiten. Buiten = '.round($buiten_temp,1).',kamer = '.status('kamer_temp').', Tobi = '.status('tobi_temp').', Alex = '.status('alex_temp'),false,2);settimestamp('timeramen');}
	elseif(($buiten_temp<=status('kamer_temp')||$buiten_temp<=status('tobi_temp')||$buiten_temp<=status('alex_temp'))&&(status('kamer_temp')>20||status('tobi_temp')>20||status('alex_temp')>20)&&(status('raamkamer')=='Closed'||status('raamkamer')=='Closed'||status('raamkamer')=='Closed'))if((int)timestamp('timeramen')<time-43190){telegram('Ramen boven open doen, te warm binnen. Buiten = '.round($buiten_temp,1).',kamer = '.status('kamer_temp').', Tobi = '.status('tobi_temp').', Alex = '.status('alex_temp'),false,2);settimestamp('timeramen');}}

}
if(status('voordeur')=='On'&&timestamp('voordeur')<time-598)sw('voordeur','Off');
if($Weg==2){
	$uit=600;
	$items=array('pirgarage','pirkeuken','pirliving','pirinkom','pirhall');
	foreach($items as $item)if(status($item)!='Off')ud($item,0,'Off');
	$items=array('garage','denon','bureel','tv','tvled','kristal','eettafel','zithoek','terras','tuin','voordeur','hall','inkom','keuken','werkblad','wasbak','kookplaat','sony','kamer','tobi','alex','lichtbadkamer1','lichtbadkamer2','badkamervuur');
	foreach($items as $item)if(status($item)!='Off')if(timestamp($item)<time-$uit)sw($item,'Off');
	$items=array('living','badkamer','kamer','tobi','alex');
	foreach($items as $item){${'setpoint'.$item}=status('setpoint'.$item);if(${'setpoint'.$item}!=0&&timestamp($item)<time-21600)setstatus('setpoint'.$item,0);}
	$items=array('tobi','living','kamer','alex');
}elseif($Weg==1){
	$uit=600;
	$items=array('pirgarage','pirkeuken','pirliving','pirinkom');
	foreach($items as $item)if(status($item)!='Off')ud($item,0,'Off');
	$items=array('hall','bureel','denon','tv','tvled','kristal','eettafel','zithoek','garage','terras','tuin','voordeur','keuken','werkblad','wasbak','kookplaat','zolderg','dampkap');
	foreach($items as $item)if(status($item)!='Off')if(timestamp($item)<time-$uit)sw($item,'Off');
	$items=array('living','badkamer','kamer','tobi','alex');
	foreach($items as $item){${'setpoint'.$item}=status('setpoint'.$item);if(${'setpoint'.$item}!=0&&timestamp($item)<time-21600)setstatus('setpoint'.$item,0);}
	$items=array('tobi','living','kamer','alex');
}elseif($Weg==0){
	$uit=900;
	if(status('pirkeuken')=='Off'){
		if(timestamp('pirkeuken')<time-900){
			$items=array('keuken','wasbak','kookplaat','werkblad');
			foreach($items as $item)if(status($item)!='Off')if(timestamp($item)<time-$uit)sw($item,'Off');
		}
	}
	if(status('pirliving')=='Off'){
		$tpirliving=timestamp('pirliving');
		if($tpirliving<time-7200){
			$items=array('eettafel','zithoek','bureel');
			foreach($items as $item)if(status($item)!='Off')if(timestamp($item)<time-$uit)sw($item,'Off');
		}
		if($tpirliving<time-9000){
			$items=array('tvled','kristal');
			foreach($items as $item)if(status($item)!='Off')if(timestamp($item)<time-$uit)sw($item,'Off');
		}
		if($tpirliving<time-10800){
			ud('miniliving4l',1,'On');
		}
	}
	$tdeurbadkamer=time-timestamp('deurbadkamer');
	if($tdeurbadkamer>7200){
		if(status('lichtbadkamer1')!='Off')sw('lichtbadkamer1','Off');
		if(status('lichtbadkamer2')!='Off')sw('lichtbadkamer2','Off');
	}
	if(status('tv')=='On'){
		if($zon<50){
			if(status('tvled')=='Off'){
				if(timestamp('tvled')<time-14400)sw('tvled','On');
			}else{
				if($zon<20){
					if(status('kristal')=='Off'){
						if(timestamp('kristal')<time-14400)sw('kristal','On');
					}
				}
			}
		}
	}
}

if(status('kodi')=='On'){
	if(pingport('192.168.2.7',1597)==1){
		$prevcheck=status('check192.168.2.7:1597');
		if($prevcheck>0)setstatus('check192.168.2.7:1597',0);
	}else{
		$check=status('check192.168.2.7:1597')+1;
		if($check>0)setstatus('check192.168.2.7:1597',$check);
		if($check>=5)sw('kodi','Off');
	}
}

$item='diepvries_temp';
if(timestamp(''.$item)<time-7200){if(timestamp('alerttempupd'.$item)<time-7200){telegram($item.' not updated');settimestamp('alerttempupd'.$item);}}

checkport('192.168.2.11',80);checkport('192.168.2.12',80);checkport('192.168.2.13',80);checkport('192.168.2.2',53);checkport('192.168.2.2',80);checkport($smappeeip,80);
if(!$auto)if(timestamp('lichten_auto')<time-10795)sw('lichten_auto','On');
if(!$meldingen&&timestamp('meldingen')<time-10795)sw('meldingen','On');
if(timestamp('pirliving')<time-14395&&timestamp('pirgarage')<time-14395&&timestamp('pirinkom')<time-14395&&timestamp('pirhall')<time-14395&&timestamp('Weg')<time-14395&&$Weg==0){setstatus('Weg',1);telegram('Slapen ingeschakeld na 4 uur geen beweging',false,2);}
if(timestamp('pirliving')<time-43190&&timestamp('pirgarage')<time-43190&&timestamp('pirinkom')<time-43190&&timestamp('pirhall')<time-43190&&timestamp('Weg')<time-43190&&$Weg==1){setstatus('Weg',2);telegram('Weg ingeschakeld na 12 uur geen beweging',false,2);}

$items=array(4=>'keukenzolderg',6=>'wasbakkookplaat',7=>'werkblad',20=>'inkomvoordeur',11=>'badkamer',60=>'diepvries');
foreach($items as $item => $name)if(timestamp('refresh'.$item)<time-7198&&timestamp('healnode')<time-900){RefreshZwave($item,'time',$name);break;}

if(status('water')=='On'){
	if(time>=strtotime('21:30')){if(timestamp('water')<time-1200)double('water','Off');}
	else{if(timestamp('water')<time-1800)double('water','Off');}
}else{
	if(time>=strtotime('21:30')&&$zon==0){
		if(timestamp('regencheck')<time-82800){
			$stamp=strftime("%G-%m-%d %k:%M:%S",time-3600*48);
			$query="select SUM(`buien`) as buien from regen where stamp > '$stamp';";
			$db=new mysqli('server','user','password','database');
			if($db->connect_errno>0)die('Unable to connect to database ['.$db->connect_error.']');
			if(!$result=$db->query($query))die('There was an error running the query ['.$query.'-'.$db->error.']');
			while($row=$result->fetch_assoc())$rainpast=$row['buien'];$msg=print_r($row,True);$result->free();$db->close();
			settimestamp('regencheck');
			if($rainpast<2000&&$maxrain<0.5){
				sw('water','On');
				$msg="Regen check:__Laatste 48u: $rainpast __Volgende 48u: $maxrain";
				$msg.='__Automatisch tuin water geven gestart.';
				telegram($msg);
			}
		}
	}
}
if(time>=strtotime('23:55')){
	if(status('nas')!='On'){
		$aantal=file_get_contents('https://films.egregius.be/query.php?user=nas');
		if($aantal>0)shell_exec('/home/pi/wakenas.sh');
	}
}
$zwembadfilter=status('zwembadfilter');
$zwembadwarmte=status('zwembadwarmte');
if($zwembadfilter=='On'){
	if(timestamp('zwembadfilter')<time-10700&&time>strtotime("16:00")&&$zwembadwarmte=='Off')sw('zwembadfilter','Off');
}else{
	if((timestamp('zwembadfilter')<time-10700&&time>strtotime("13:00")&&time<strtotime("16:00"))||(timestamp('zwembadfilter')<time-10700&&time>strtotime("11:00")&&time<strtotime("19:00")&&$buiten_temp>28))sw('zwembadfilter','On');
}
if($zwembadwarmte=='On'){
	if(timestamp('zwembadwarmte')<time-86398)sw('zwembadwarmte','Off');
	if($zwembadfilter=='Off')sw('zwembadfilter','On');
}
if(status('kodi')=='On'&&timestamp('kodi')<time-300){
	$ctx=stream_context_create(array('http'=>array('timeout' => 5)));
	if($Weg>0)file_get_contents('http://192.168.2.7:1597/jsonrpc?request={"jsonrpc":"2.0","id":1,"method":"System.Shutdown"}',false,$ctx);
	if(timestamp('kodi')<time-298){if(pingport('192.168.2.7',1597)==1){$prevcheck=status('check192.168.2.57:1597');if($prevcheck>0)setstatus('check192.168.2.57:1597',0);}else{$check=status('check192.168.2.57:1597')+1;if($check>0)setstatus('check192.168.2.57:1597',$check);if($check>=5)sw('kodi','Off');}}
}
if($auto){
	$regenpomp=status('regenpomp');
	$Tregenpomp=timestamp('regenpomp');
	if($buien>0){
		$pomppauze=21600/$buien;
		if($pomppauze>21600)$pomppauze=21600;
		elseif($pomppauze<300)$pomppauze=300;
	}else $pomppauze=86400;
	if($regenpomp=='On'&&$Tregenpomp<time-57)sw('regenpomp','Off','was on for '.convertToHours(time-$Tregenpomp));
	elseif($regenpomp=='Off'&&$Tregenpomp<time-$pomppauze)sw('regenpomp','On','was off for '.convertToHours(time-$Tregenpomp));

	$zonopen=2000;
	$luifel=100-status('luifel');
	$maxbuien=5;
	$sliving_temp=status('living_temp');
	$x=0;foreach($windhist as $y)$x=$y+$x;$windhist=$x/count($windhist);
	if	  ($wind>=4.5)$maxluifel=0;
	elseif($wind>=4.0)$maxluifel=20;
	elseif($wind>=3.5)$maxluifel=28;
	elseif($wind>=3.0)$maxluifel=36;
	elseif($wind>=2.5)$maxluifel=44;
	else $maxluifel=52;
	$dir=status('winddir');
	if($dir=='East')$maxluifel=round($maxluifel*0.8,0);
	elseif($dir=='East')$maxluifel=round($maxluifel*0.8,0);
	$wind=round($wind,1);
	$luifelauto=status('dimactionluifel');//0=manueel,1=auto
	$tluifel=time-timestamp('luifel');
	if($luifelauto==0){
		if($tluifel>3600&&$maxluifel<30){setstatus('dimactionluifel',1);$luifelauto=1;}
		elseif($tluifel>28800){setstatus('dimactionluifel',1);$luifelauto=1;}
	}
	if($luifel>$maxluifel&&$luifelauto==1){
		if($maxluifel==0)sl('luifel',100);else sl('luifel',((100-$maxluifel)+1));
		//telegram("1 Luifel ".$maxluifel." dicht: __buien=$buien __wind=$wind $dir__zon:$zon __living:$sliving_temp __Tluifel=$tluifel");
	}elseif($maxluifel==0&&$luifelauto==0&&$luifel>0){
		sl('luifel',100);
		//telegram("2 Luifel volledig dicht: __buien=$buien __wind=$wind $dir __zon:$zon __living:$sliving_temp __Tluifel=$tluifel");
	}elseif($luifel<$maxluifel&&$buien<$maxbuien&&$sliving_temp>=22&&$zon>$zonopen&&$luifelauto==1&&$tluifel>600&&$wind<$windhist&&time>strtotime("10:00")){
		sl('luifel',((100-$maxluifel)+1));
		//telegram("3 Luifel ".$maxluifel." open: __buien=$buien __wind=$wind $dir__zon:$zon __living:$sliving_temp __Tluifel=$tluifel");
	}elseif(($buien>$maxbuien||(($zon==0||$sliving_temp<22)&&$luifelauto==1))&&$luifel!=0){
		sl('luifel',100);
		//telegram('4 Luifel');
	}
}
if($zon>0){
	$zonvandaag=file_get_contents('https://egregius.be/zon/vandaag.php');
	if($zonvandaag>0)setstatus('zonvandaag',round($zonvandaag,1));
}else{
	if(time<strtotime("0:10"))setstatus('zonvandaag',0);
}
include('gcal/gcal.php');
function roundUpToAny($n,$x=5){
    return (ceil($n)%$x===0)?ceil($n):round(($n+$x/2)/$x)*$x;
}
?>
