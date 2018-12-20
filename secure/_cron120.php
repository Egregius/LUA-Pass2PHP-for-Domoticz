<?php
$items=array('GroheRed','garage','denon','bureel','tv','tvled','kristal','eettafel','zithoek','terras','tuin','voordeur','hall','inkom','keuken','werkblad','wasbak','kookplaat','sony','kamer','tobi','alex','lichtbadkamer','badkamervuur','zolderg','Xlight','dampkap',
			'pirgarage','pirkeuken','pirliving','pirinkom','pirhall',
			'raamtobi','raamalex','raamkamer',
			'buien','living_temp','badkamer_temp','kamer_temp','tobi_temp','alex_temp','zolder_temp',
			'heating','meldingen','zonop','zononder'
			);
foreach($items as $i)${$i}=apcu_fetch($i);

$items=array('GroheRed','Weg','garage','denon','bureel','tv','tvled','kristal','eettafel','zithoek','terras','tuin','voordeur','hall','inkom','keuken','werkblad','wasbak','kookplaat','sony','kamer','tobi','alex','lichtbadkamer','badkamervuur','zolderg','Xlight',
		'pirkeuken','pirliving','pirgarage','pirinkom','pirhall',
		);
foreach($items as $i)${'T'.$i}=past($i);

$db=new mysqli('127.0.0.1','domotica','domotica','domotica');
if($db->connect_errno>0)die('Unable to connect to database ['.$db->connect_error.']');

$prevtemp=apcu_fetch('buiten_temp');
$prevwind=apcu_fetch('wind');
$prevbuien=apcu_fetch('buien');
$wind=$prevwind;

/*$wu=@json_decode(@file_get_contents('http://api.wunderground.com/api/1234567890/conditions/q/BX/Beitem.json'),true);
if(isset($wu['current_observation'])){
	$lastobservation=apcu_fetch('wu-observation');
	if(isset($wu['current_observation']['estimated']['estimated']))goto exitwunderground;
	elseif($wu['current_observation']['observation_epoch']<=$lastobservation)goto exitwunderground;
	if(isset($wu['current_observation']['temp_c'])){$wutemp=$wu['current_observation']['temp_c'];if($wutemp>$prevtemp+0.5)$wutemp=$prevtemp+0.5;elseif($wutemp<$prevtemp-0.5)$wutemp=$prevtemp-0.5;}
	if(isset($wu['current_observation']['wind_kph']))$wuwind=$wu['current_observation']['wind_kph'];
	if(isset($wu['current_observation']['wind_gust_kph']))if($wu['current_observation']['wind_gust_kph']>$wuwind)$wuwind=$wu['current_observation']['wind_gust_kph'];
	if(isset($wu['current_observation']['precip_1hr_metric']))$owbuien=$wu['current_observation']['precip_1hr_metric']*35;
	if(isset($wu['current_observation']['wind_dir']))apcu_store('winddir',$wu['current_observation']['wind_dir']);
	if(isset($wu['current_observation']['icon']))apcu_store('icon',$wu['current_observation']['icon']);
	apcu_store('wu-observation',$wu['current_observation']['observation_epoch']);
}
exitwunderground:*/
$maxtemp=1;
$maxrain=-1;
$ds=@json_decode(@file_get_contents('https://api.darksky.net/forecast/123456789/50.2020861,3.2064103?units=si'),true);
if(isset($ds['currently'])){
	if(isset($ds['currently']['temperature'])){
		$dstemp=$ds['currently']['temperature'];
		if($dstemp>$prevtemp+0.5)$dstemp=$prevtemp+0.5;
		elseif($dstemp<$prevtemp-0.5)$dstemp=$prevtemp-0.5;
	}
	if(isset($ds['currently']['windSpeed'])){
		$dswind=$ds['currently']['windSpeed'];
	}
	if(isset($ds['currently']['windGust'])){
		if($ds['currently']['windGust']>$dswind)$dswind=$ds['currently']['windGust'];
	}
	if(isset($dswind))$dswind=$dswind / 0.621371192;
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
		apcu_store('maxtemp',$maxtemp);
		apcu_store('maxrain',$maxrain);
	}
}
$ow=@json_decode(@file_get_contents('https://api.openweathermap.org/data/2.5/weather?id=2787889&units=metric&APPID=1234567890'),true);
if(isset($ow['main']['temp'])){
	$owtemp=$ow['main']['temp'];
	if($owtemp>$prevtemp+0.5)$owtemp=$prevtemp+0.5;
	elseif($owtemp<$prevtemp-0.5)$owtemp=$prevtemp-0.5;
	$owwind=$ow['wind']['speed'];
	apcu_store('humidity',$ow['main']['humidity']);
	apcu_store('icon',$ow['weather'][0]['icon']);
}

$rains=@file_get_contents('http://gadgets.buienradar.nl/data/raintext/?lat=50.29&lon=3.21');
if(!empty($rains)){
	$rains=str_split($rains,11);$totalrain=0;$aantal=0;
	foreach($rains as $rain){
		$aantal=$aantal+1;
		$totalrain=$totalrain+substr($rain,0,3);
		if($aantal==7)break;
	}
	$newbuien=$totalrain/7;
	if($newbuien>100)$newbuien=100;
	if($newbuien>20)$maxrain=$newbuien;
}
/*if(time>=strtotime('21:30')&&$zon==0&&$Twater>10800){
	if($Tregencheck>82800){
		$query="select SUM(`buien`) as buien from regen;";
		if(!$result=$db->query($query))die('There was an error running the query ['.$query.'-'.$db->error.']');
		while($row=$result->fetch_assoc())$rainpast=$row['buien'];
		$result->free();
		$msg="Regen check:__Laatste 48u: $rainpast __Volgende 48u: $maxrain __Automatisch tuin water geven gestart.";
		apcu_store('Tregencheck',time);
		if($rainpast<2000&&$maxrain<0.5){
			sw('water','On');
			apcu_store('watertijd',300);
			telegram($msg);
		}

	}
}*/
if(isset($prevtemp)&&isset($dstemp)&&isset($owtemp))apcu_store('buiten_temp',($prevtemp+$dstemp+$owtemp)/3);
elseif(isset($prevtemp)&&isset($dstemp))apcu_store('buiten_temp',($prevtemp+$dstemp)/2);
elseif(isset($owtemp)&&isset($dstemp))apcu_store('buiten_temp',($owtemp+$dstemp)/2);
elseif(isset($owtemp))apcu_store('buiten_temp',$owtemp);
elseif(isset($dstemp))apcu_store('buiten_temp',$dstemp);
if(isset($prevwind)&&isset($owwind)&&isset($dswind))$wind=($prevwind+$owwind+$dswind)/3;
elseif(isset($prevwind)&&isset($owwind))$wind=($prevwind+$owwind)/2;
elseif(isset($prevwind)&&isset($dswind))$wind=($prevwind+$dswind)/2;
elseif(isset($owwind)&&isset($dswind))$wind=($owwind+$dswind)/2;
elseif(isset($owwind))$wind=$owwind;
elseif(isset($dswind))$wind=$dswind;
if($wind!=$prevwind)apcu_store('wind',$wind);
$windhist=json_decode(apcu_fetch('windhist'));
$windhist[]=$wind;
$windhist=array_slice($windhist,-4);
apcu_store('windhist',json_encode($windhist));
$buiten_temp=apcu_fetch('buiten_temp');
$msg='Buiten temperaturen : ';
if(isset($dstemp))$msg.='Darksky = '.$dstemp.'°C ';
if(isset($owtemp))$msg.='Openweathermap = '.$owtemp.'°C ';
if(isset($buiten_temp))$msg.='buiten_temp = '.$buiten_temp.'°C';
lg($msg);
if(isset($prevbuien)&&isset($dsbuien)&&isset($newbuien))$buien=($prevbuien+$dsbuien+$newbuien)/3;
elseif(isset($prevbuien)&&isset($newbuien))$buien=($prevbuien+$newbuien)/2;
elseif(isset($prevbuien)&&isset($dsbuien))$buien=($prevbuien+$dsbuien)/2;
elseif(isset($newbuien))$buien=$newbuien;
elseif(isset($dsbuien))$buien=$dsbuien;
$buien=round($buien,0);
if(isset($newbuien)&&$newbuien>100)$newbuien=100;
if(isset($dsbuien)&&$dsbuien>100)$dsbuien=100;
if(isset($buien)&&$buien>100)$buien=100;
apcu_store('buien',$buien);
if(!isset($owbuien))$owbuien=0;
if(!isset($dsbuien))$dsbuien=0;
if(!isset($newbuien))$newbuien=0;
$query="INSERT IGNORE INTO `regen` (`buienradar`,`darksky`,`buien`) VALUES ('$newbuien','$dsbuien','$buien');";
if(!$result=$db->query($query))die('There was an error running the query ['.$query .' - ' . $db->error . ']');
$db->close();

//UV
$uv=json_decode(shell_exec("curl -X GET 'https://api.openuv.io/api/v1/uv?lat=50.89&lng=3.11' -H 'x-access-token: 3ede211d20c3fac5d9d1df3b5282ebf2'"),true);
if(isset($uv['result'])){
	apcu_store('uv',$uv['result']['uv']);
	apcu_store('uv_max',$uv['result']['uv_max']);
}

if(strftime("%k")>2){
	if(past('sunrise')>79200){
		if(date('I',time())){apcu_store('DST',true);$DST=true;}
		else{apcu_store('DST',false);$DST=false;}
		$sunrise=json_decode(file_get_contents('http://api.sunrise-sunset.org/json?lat=50.9020861&lng=3.1064103&date=today&formatted=0'),true);
		if(isset($sunrise['results']['civil_twilight_begin'])){
			$zonop=strtotime($sunrise['results']['civil_twilight_begin'])-($DST==true?3600:0);//Add -3600 during wintertime
			$zononder=strtotime($sunrise['results']['civil_twilight_end'])-($DST==true?3600:0);//Add -3600 during wintertime
			apcu_store('zonop',$zonop);
			apcu_store('zononder',$zononder);
			apcu_store('Tsunrise',time);
			$words=array('Mercurius','Venus','Mars','Jupiter','Saturnus','Neptunus','Egregius','Minja','Alex','Tobi','Kirby','Guy','Beitem','Severinus');
			shuffle($words);
			apcu_store('Gast',$words[0]);
		}
	}
}
if(time>$zonop&&time<$zononder)$zonOP=true;else $zonOP=false;
if(apcu_fetch('zonOP')!=$zonOP)apcu_store('zonOP',$zonOP);

if($GroheRed=='On'){
	if($wasbak=='Off'&&$werkblad=='Off'&&$keuken=='Off'&&$kookplaat=='Off'&&$TGroheRed>110&&apcu_fetch('modeGroheRed')==0)sw('GroheRed','Off');
	if($TGroheRed>900){sw('GroheRed','Off');apcu_store('modeGroheRed',0);}
}else{
	if($TGroheRed>120&&($pirkeuken=='On'&&$Tpirkeuken>190)||($wasbak=='On'&&$Twasbak>190)||($keuken=='On'&&$Tkeuken>190)||($kookplaat=='On'&&$Tkookplaat>190))sw('GroheRed','On');
}
if($meldingen=='On'&&$TWeg>300){
	$items=array('living_temp',/*'badkamer_temp',*/'kamer_temp','tobi_temp','alex_temp','zolder_temp');$avg=0;
	foreach($items as $item)$avg=$avg+${$item};$avg=$avg/6;
	foreach($items as $item){
		if(${$item}>$avg+5&&${$item}>25){
			$msg='T '.$item.'='.${$item}.'°C. AVG='.round($avg,1).'°C';
			if(past('alerttemp'.$item)>3598){
				telegram($msg,false,2);
				shell_exec('/var/www/home.egregius.be/secure/ios.sh "'.$msg.'" > /dev/null 2>/dev/null &');
				apcu_store('Talerttemp'.$item,time);
			}
		}
		$past=past($item);
		if($past>43150){
			if(apcu_fetch('alerttempupd'.$item)<time-43100){
				telegram($item.' not updated since '.strftime("%k:%M:%S",time-$past));
				apcu_store('alerttempupd'.$item,time);
			}
		}
	}
	$devices=array('tobiZ','alexZ',/*'livingZ','livingZZ',*/'kamerZ');
	foreach($devices as $device){if(past($device)>2000){
		$past=past('nocom'.$device);
		if($past>43150){
			telegram($device.' geen communicatie sinds '.strftime("%k:%M:%S",time-$past),true);
			apcu_store('Tnocom'.$device,time);}
		}
	}
}
$item='diepvries_temp';
$past=past($item);
if($past>7200){
	if(past('alerttempupd'.$item)>7200){
		telegram($item.' not updated since '.strftime("%k:%M:%S",time-$past));
		apcu_store('Talerttempupd'.$item,time);
	}
}

if($voordeur=='On'&&$Tvoordeur>598)sw('voordeur','Off');
if($Weg==2){//Weg
	$uit=600;
	$items=array('pirgarage','pirkeuken','pirliving','pirinkom','pirhall');
	foreach($items as $item)if(${$item}!='Off'){ud($item,0,'Off');lg($item.' uitgeschakeld omdat we weg zijn');}
	$items=array('garage','denon','bureel','tv','tvled','kristal','eettafel','zithoek','terras','tuin','voordeur','hall','inkom','keuken','werkblad','wasbak','kookplaat','sony','kamer','tobi','alex','lichtbadkamer','badkamervuur','zolderg','Xlight');
	foreach($items as $item)if(${$item}!='Off'){if(past($item)>$uit)sw($item,'Off');lg($item.' uitgeschakeld omdat we weg zijn');}
	$items=array('living','bureel','keukenL','keukenR','kamerL','kamerR','tobi','alex');
	foreach($items as $i){
		if(apcu_fetch('modeR'.$i)==false&&past('modeR'.$i)>21600){apcu_store('modeR'.$i,true);apcu_store('TmodeR'.$i,time);}
	}
	$items=array('living','badkamer','kamer','tobi','alex');
	foreach($items as $item){${'setpoint'.$item}=apcu_fetch('setpoint'.$item);if(${'setpoint'.$item}!=0&&past($item)>7200)apcu_store('setpoint'.$item,0);}
}elseif($Weg==1){//Slapen
	$uit=600;
	$items=array('pirgarage','pirkeuken','pirliving','pirinkom');
	foreach($items as $item)if(${$item}!='Off'){ud($item,0,'Off');lg($item.' uitgeschakeld omdat we slapen');}
	$items=array('hall','bureel','denon','tv','tvled','kristal','eettafel','zithoek','garage','terras','tuin','voordeur','keuken','werkblad','wasbak','kookplaat','zolderg','dampkap','Xlight');
	foreach($items as $item)if(${$item}!='Off'){if(past($item)>$uit)sw($item,'Off');lg($item.' uitgeschakeld omdat we slapen');}
	$items=array('living','badkamer','kamer','tobi','alex');
	foreach($items as $item){${'setpoint'.$item}=apcu_fetch('setpoint'.$item);if(${'setpoint'.$item}!=0&&past($item)>7200)apcu_store('setpoint'.$item,0);}
}elseif($Weg==0){//Thuis
	if($pirkeuken=='Off'){
		$uit=300;
		if(past('pirkeuken')>$uit){
			$items=array('keuken','wasbak','kookplaat','werkblad');
			foreach($items as $item)if(${$item}!='Off')if(past($item)>$uit)sw($item,'Off');
		}
	}
	if($pirliving=='Off'){
		$uit=7200;
		$tpirliving=past('pirliving');
		if($tpirliving>$uit){
			$items=array('eettafel','zithoek','bureel');
			foreach($items as $item)if(apcu_fetch($item)!='Off')if(past($item)>$uit)sw($item,'Off');
		}
		$uit=10800;
		if($tpirliving>$uit){
			$items=array('tvled','kristal','jbl');
			foreach($items as $item)if(apcu_fetch($item)!='Off')if(past($item)>$uit)sw($item,'Off');
		}
		$uit=10800;
		if($tpirliving>$uit){
			if($denon=='On'||$tv=='On'){ud('miniliving4l',1,'On');lg('miniliving4l pressed omdat er al 3 uur geen beweging is');}
		}
	}
	if(past('deurbadkamer')>3600&&past('lichtbadkamer')>3600){
		if($lichtbadkamer!='Off')sw('lichtbadkamer','Off');
	}
	if($tv=='On'){
		if($zon<$zonmedia){
			if($tvled=='Off'){
				if(past('tvled')>14400)sw('tvled','On');
			}
			if($kristal=='Off'){
				if(past('kristal')>14400)sw('kristal','On');
			}
		}
	}
	if(past('tXlight')>300&&$Xlight!='Off')sw('Xlight','Off');
	$items=array('living','badkamer','kamer','tobi','alex');
	foreach($items as $item){${'setpoint'.$item}=apcu_fetch('setpoint'.$item);if(${'setpoint'.$item}!=0&&past($item.'_set')>7200){apcu_store('setpoint'.$item,0);}}
	$items=array('living','bureel','keukenL','keukenR','kamerL','kamerR','tobi','alex');
	foreach($items as $i){
		if(apcu_fetch('modeR'.$i)==false&&past('modeR'.$i)>21600){apcu_store('modeR'.$i,true);apcu_store('TmodeR'.$i,time);}
	}
	if($heating==2){

		if(	  $buiten_temp>$kamer_temp
			&&$buiten_temp>$tobi_temp
			&&$buiten_temp>$alex_temp
			&&
				(
					  $raamkamer=='Open'
					||$raamtobi=='Open'
					||$raamalex=='Open')
				)if(past('timeramen')>43190){
					telegram('Ramen boven dicht doen, te warm buiten. Buiten = '.round($buiten_temp,1).',kamer = '.apcu_fetch('kamer_temp').', Tobi = '.apcu_fetch('tobi_temp').', Alex = '.apcu_fetch('alex_temp'),false,2);
					apcu_store('Ttimeramen',time);
				}
		elseif(
			(
				  $buiten_temp<=$kamer_temp
				||$buiten_temp<=$tobi_temp
				||$buiten_temp<=$alex_temp
			)&&(
					  $raamkamer=='Closed'
					||$raamkamer=='Closed'
					||$raamkamer=='Closed'
				)
			)if(past('timeramen')>43190){
				telegram('Ramen boven open doen, te warm binnen. Buiten = '.round($buiten_temp,1).',kamer = '.apcu_fetch('kamer_temp').', Tobi = '.apcu_fetch('tobi_temp').', Alex = '.apcu_fetch('alex_temp'),false,2);
				apcu_store('Ttimeramen',time);
			}
	}else{
		if(($buiten_temp>$kamer_temp&&$buiten_temp>$tobi_temp&&$buiten_temp>$alex_temp)&&$buiten_temp>22&&($kamer_temp>20||$tobi_temp>20||$alex_temp>20)&&($raamkamer=='Open'||$raamtobi=='Open'||$raamalex=='Open'))if((int)past('timeramen')>43190){telegram('Ramen boven dicht doen, te warm buiten. Buiten = '.round($buiten_temp,1).',kamer = '.apcu_fetch('kamer_temp').', Tobi = '.apcu_fetch('tobi_temp').', Alex = '.apcu_fetch('alex_temp'),false,2);apcu_store('Ttimeramen',time);}
		elseif(($buiten_temp<=$kamer_temp||$buiten_temp<=$tobi_temp||$buiten_temp<=$alex_temp)&&($kamer_temp>20||$tobi_temp>20||$alex_temp>20)&&($raamkamer=='Closed'||$raamkamer=='Closed'||$raamkamer=='Closed'))if((int)past('timeramen')>43190){telegram('Ramen boven open doen, te warm binnen. Buiten = '.round($buiten_temp,1).',kamer = '.apcu_fetch('kamer_temp').', Tobi = '.apcu_fetch('tobi_temp').', Alex = '.apcu_fetch('alex_temp'),false,2);apcu_store('Ttimeramen',time);}
	}
}
if(apcu_fetch('nvidia')=='On'){
	if($Weg>0){
		$ctx=stream_context_create(array('http'=>array('timeout' => 5)));
		@file_get_contents('http://shield:1597/jsonrpc?request={"jsonrpc":"2.0","id":1,"method":"System.Shutdown"}',false,$ctx);
	}
	/*if(pingport('shield',1597)==1){
		$check=apcu_fetch('checkshield_1597');
		if($check>0)apcu_store('checkshield_1597',0);
	}else{
		$check=apcu_fetch('checkshield_1597')+1;
		if($check>0)apcu_store('checkshield_1597',$check);
		if($check>=15){sw('kodi','Off');apcu_store('checkshield_1597',0);}
	}*/
}

checkport('picam1',80);
checkport('picam2',80);
checkport('picam3',80);
checkport('xiaomi');
checkport('smappee',80);
checkport('livingvuur',80);
checkport('controlpi',8080);

if(!$auto)if(past('lichten_auto')>10795)sw('lichten_auto','On');
if(!$meldingen&&past('meldingen')>10795)sw('meldingen','On');
if(past('pirliving')>14395&&past('pirgarage')>14395&&past('pirinkom')>14395&&past('pirhall')>14395&&past('Weg')>14395&&$Weg==0){apcu_store('Weg',1);telegram('Slapen ingeschakeld na 4 uur geen beweging',false,2);}
if(past('pirliving')>43190&&past('pirgarage')>43190&&past('pirinkom')>43190&&past('pirhall')>43190&&past('Weg')>43190&&$Weg==1){apcu_store('Weg',2);telegram('Weg ingeschakeld na 12 uur geen beweging',false,2);}

//$items=array(4=>'keukenzolderg',6=>'wasbakkookplaat',7=>'werkblad',20=>'inkomvoordeur',11=>'badkamer',60=>'diepvries');
//foreach($items as $item => $name)if(past('refresh'.$item)>7198&&past('healnode')>900){RefreshZwave($item,'time',$name);break;}


if(time<=strtotime('0:04')){
	apcu_store('gasvandaag',0);
	apcu_store('watervandaag',0);
}elseif(time>=strtotime('10:00')&&time<strtotime('10:05')){
	$items=array('wijslapen','tobislaapt','alexslaapt');
	foreach($items as $i){
		if(apcu_fetch($i)==true)apcu_store($i,false);
	}
}
/*$zwembadfilter=apcu_fetch('zwembadfilter');
$zwembadwarmte=apcu_fetch('zwembadwarmte');
if($zwembadfilter=='On'){
	if(past('zwembadfilter')>10700&&time>strtotime("16:00")&&$zwembadwarmte=='Off'&&$buiten_temp<27)sw('zwembadfilter','Off');
}else{
	if(
		(
				past('zwembadfilter')>10700
				&&	time>strtotime("13:00")
				&&	time<strtotime("16:00")
			)
			||
			(
				past('zwembadfilter')>10700
				&&	$buiten_temp>27
			)
		)sw('zwembadfilter','On');
}
if($zwembadwarmte=='On'){
	if(past('zwembadwarmte')>86398)sw('zwembadwarmte','Off');
	if($zwembadfilter=='Off')sw('zwembadfilter','On');
}*/
if($auto){
	$regenpomp=apcu_fetch('regenpomp');
	$Tregenpomp=past('regenpomp');
	if($buien>0){
		$pomppauze=21600/$buien;
		if($pomppauze>21600)$pomppauze=21600;
		elseif($pomppauze<300)$pomppauze=300;
	}else $pomppauze=21600;
	if($regenpomp=='On'&&$Tregenpomp>57)sw('regenpomp','Off','was on for '.convertToHours($Tregenpomp));
	elseif($regenpomp=='Off'&&$Tregenpomp>$pomppauze)sw('regenpomp','On','was off for '.convertToHours($Tregenpomp));

	$zonopen=1500;
	$luifel=100-apcu_fetch('luifel');
	$maxbuien=5;
	$living_temp=apcu_fetch('living_temp');
	$x=0;foreach($windhist as $y)$x=$y+$x;$windhist=round($x/count($windhist),2);
	if	  ($wind>=30)$maxluifel=0;
	elseif($wind>=25)$maxluifel=28;
	elseif($wind>=20)$maxluifel=36;
	elseif($wind>=15)$maxluifel=44;
	elseif($wind>=10)$maxluifel=52;
	else $maxluifel=60;
	$dir=apcu_fetch('winddir');
	if($dir=='East')$maxluifel=round($maxluifel*0.8,0);
	elseif($dir=='East')$maxluifel=round($maxluifel*0.8,0);
	$wind=round($wind,1);
	$luifelauto=apcu_fetch('modeluifel');//0=manueel,1=auto
	$tluifel=past('luifel');
	if($luifelauto==0){
		if($tluifel>3600&&$maxluifel<30){apcu_store('dimactionluifel',1);$luifelauto=1;}
		elseif($tluifel>28800){apcu_store('dimactionluifel',1);$luifelauto=1;}
	}
	$luifelauto=false;
	if($luifelauto)lg("Luifel: buien=$buien | wind=$wind $dir $windhist | zon:$zon | living:$living_temp | Tluifel=$tluifel | Luifel:$luifel | maxluifel=$maxluifel");
	if($luifel>$maxluifel&&$luifelauto==1){
		if($maxluifel==0)sl('luifel',100);else sl('luifel',((100-$maxluifel)+1));
		//telegram("Luifel ".$maxluifel." dicht: __buien=$buien __wind=$wind $dir __zon:$zon __living:$living_temp __Tluifel=$tluifel",true);
	}elseif($maxluifel==0&&$luifelauto==0&&$luifel>0){
		sl('luifel',100);
		//telegram("Luifel volledig dicht: __buien=$buien __wind=$wind $dir __zon:$zon __living:$living_temp __Tluifel=$tluifel",true);
	}elseif($heating==2&&$luifel<$maxluifel&&$buien<$maxbuien&&$zon>$zonopen&&$luifelauto==1&&$tluifel>600&&$wind<$windhist&&time>strtotime("10:00")){
		if($luifelauto)sl('luifel',((100-$maxluifel)));
		//telegram("Luifel ".$maxluifel." open: __buien=$buien __wind=$wind $dir __zon:$zon __living:$living_temp __Tluifel=$tluifel",true);
	}elseif($heating<2&&$luifel<$maxluifel&&$buien<$maxbuien&&$living_temp>22&&$buiten_temp>17&&$zon>$zonopen&&$luifelauto==1&&$tluifel>600&&$wind<$windhist&&time>strtotime("10:00")){
		if($luifelauto)sl('luifel',((100-$maxluifel)));
		//telegram("Luifel ".$maxluifel." open: __buien=$buien __wind=$wind $dir __zon:$zon __living:$living_temp __Tluifel=$tluifel",true);
	}elseif(($buien>$maxbuien||(($zon==0||$living_temp<19)&&$luifelauto==1))&&$luifel!=0){
		sl('luifel',100);
		//telegram('Luifel dicht __buien=$buien __wind=$wind $dir __zon:$zon __living:$living_temp __Tluifel=$tluifel',true);
	}
	if(apcu_fetch('poort')!='Open'&&past('poort')>120&&past('poortrf')>120&&apcu_fetch('poortrf')!='Off')double('poortrf','Off');
	/*if(time>=strtotime('10:00')&&time<=strtotime('22:00')&&($Weg==0||$Weg==2)){
		$items=array('luifel','RkamerL','RkamerR','Rtobi','Ralex','Rliving','Rbureel','RkeukenL','RkeukenR');
		foreach($items as $i){
			if(past('mode'.$i)>7200){
				if(apcu_fetch('mode'.$i)==false){
					lg('								reset mode '.$i.' '.past('mode'.$i));
					apcu_store('mode'.$i,true);
					apcu_store('Tmode'.$i,time);
				}
			}
		}
	}*/
	if($zonOP){
		if(apcu_fetch('Rliving')<30&&apcu_fetch('Rbureel')<30&&apcu_fetch('zon')>75){
			if(apcu_fetch('jbl')!='Off')sw('jbl','Off');
			if(apcu_fetch('bureel')!='Off')sw('bureel','Off');
			if(apcu_fetch('kristal')!='Off')sw('kristal','Off');
			if(apcu_fetch('tvled')!='Off')sw('tvled','Off');
		}
	}
}
if(apcu_fetch('alex')>0){
	if(past('alex')>3600){
		apcu_store('dimactionalex',1);
	}
}


$timefrom=time-86400;
$chauth = curl_init('https://app1pub.smappee.net/dev/v1/oauth2/token?grant_type=password&client_id='.$smappeeclient_id.'&client_secret='.$smappeeclient_secret.'&username='.$smappeeusername.'&password='.$smappeepassword.'');
curl_setopt($chauth,CURLOPT_AUTOREFERER,true);
curl_setopt($chauth,CURLOPT_RETURNTRANSFER,1);
curl_setopt($chauth,CURLOPT_FOLLOWLOCATION,1);
curl_setopt($chauth,CURLOPT_VERBOSE,1);
curl_setopt($chauth,CURLOPT_SSL_VERIFYHOST,false);
curl_setopt($chauth,CURLOPT_SSL_VERIFYPEER,false);
$objauth=json_decode(curl_exec($chauth));
if(!empty($objauth)){
	$access=$objauth->{'access_token'};
	curl_close($chauth);
	$chconsumption=curl_init('');
	curl_setopt($chconsumption,CURLOPT_HEADER,0);
	$headers=array('Authorization: Bearer '.$access);
	curl_setopt($chconsumption,CURLOPT_HTTPHEADER,$headers);
	curl_setopt($chconsumption,CURLOPT_AUTOREFERER,true);
	curl_setopt($chconsumption,CURLOPT_URL,'https://app1pub.smappee.net/dev/v1/servicelocation/'.$smappeeserviceLocationId.'/consumption?aggregation=3&from='.$timefrom.'000&to='.time.'000');
	curl_setopt($chconsumption,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($chconsumption,CURLOPT_FOLLOWLOCATION,1);
	curl_setopt($chconsumption,CURLOPT_VERBOSE,1);
	curl_setopt($chconsumption,CURLOPT_SSL_VERIFYHOST,false);
	curl_setopt($chconsumption,CURLOPT_SSL_VERIFYPEER,false);
	$objconsumption=json_decode(curl_exec($chconsumption),true);
	if(!empty($objconsumption['consumptions'])){
		$verbruikvandaag=$objconsumption['consumptions'][0]['consumption']/1000;
		apcu_store('verbruikvandaag',round($verbruikvandaag,1));
		$zonvandaag=$objconsumption['consumptions'][0]['solar']/1000;
		apcu_store('zonvandaag',round($zonvandaag,1));
		$gas=apcu_fetch('gasvandaag')/100;
		$water=apcu_fetch('watervandaag')/1000;
		lg("verbruik => gas = $gas | verbruik = $verbruikvandaag | zon = $zonvandaag | water = $water");
	}
	curl_close($chconsumption);
}
//Update and clean SQL database
$db=new mysqli('127.0.0.1','domotica','domotica','domotica');
if($db->connect_errno>0){die('Unable to connect to database ['.$db->connect_error.']');}

$limit=86400000;
echo '<h2>Putting temps min,avg,max into temp_hour</h2>';
$sql="select left(stamp,13) as stamp,min(buiten) as buiten_min,max(buiten) as buiten_max,avg(buiten) as buiten_avg,min(living) as living_min,max(living) as living_max,avg(living) as living_avg,min(badkamer) as badkamer_min,max(badkamer) as badkamer_max,avg(badkamer) as badkamer_avg,min(kamer) as kamer_min,max(kamer) as kamer_max,avg(kamer) as kamer_avg,min(tobi) as tobi_min,max(tobi) as tobi_max,avg(tobi) as tobi_avg,min(alex) as alex_min,max(alex) as alex_max,avg(alex) as alex_avg,min(zolder) as zolder_min,max(zolder) as zolder_max,avg(zolder) as zolder_avg from temp group by left(stamp,13)";
	if(!$result=$db->query($sql)){die('There was an error running the query ['.$sql.'-'.$db->error.']');}
	$values=array();
	while($row=$result->fetch_assoc())$values[]=$row;$result->free();
	foreach($values as $value) {
		$stamp=$value['stamp'];
		$buiten_min=$value['buiten_min'];$buiten_max=$value['buiten_max'];$buiten_avg=$value['buiten_avg'];
		$living_min=$value['living_min'];$living_max=$value['living_max'];$living_avg=$value['living_avg'];
		$badkamer_min=$value['badkamer_min'];$badkamer_max=$value['badkamer_max'];$badkamer_avg=$value['badkamer_avg'];
		$kamer_min=$value['kamer_min'];$kamer_max=$value['kamer_max'];$kamer_avg=$value['kamer_avg'];
		$tobi_min=$value['tobi_min'];$tobi_max=$value['tobi_max'];$tobi_avg=$value['tobi_avg'];
		$alex_min=$value['alex_min'];$alex_max=$value['alex_max'];$alex_avg=$value['alex_avg'];
		$zolder_min=$value['zolder_min'];$zolder_max=$value['zolder_max'];$zolder_avg=$value['zolder_avg'];
		$query = "INSERT INTO `temp_hour` (`stamp`,`buiten_min`,`buiten_max`,`buiten_avg`,`living_min`,`living_max`,`living_avg`,`badkamer_min`,`badkamer_max`,`badkamer_avg`,`kamer_min`,`kamer_max`,`kamer_avg`,`tobi_min`,`tobi_max`,`tobi_avg`,`alex_min`,`alex_max`,`alex_avg`,`zolder_min`,`zolder_max`,`zolder_avg`) VALUES ('$stamp','$buiten_min','$buiten_max','$buiten_avg','$living_min','$living_max','$living_avg','$badkamer_min','$badkamer_max','$badkamer_avg','$kamer_min','$kamer_max','$kamer_avg','$tobi_min','$tobi_max','$tobi_avg','$alex_min','$alex_max','$alex_avg','$zolder_min','$zolder_max','$zolder_avg') ON DUPLICATE KEY UPDATE `buiten_min`='$buiten_min',`buiten_max`='$buiten_max',`buiten_avg`='$buiten_avg',`living_min`='$living_min',`living_max`='$living_max',`living_avg`='$living_avg',`badkamer_min`='$badkamer_min',`badkamer_max`='$badkamer_max',`badkamer_avg`='$badkamer_avg',`kamer_min`='$kamer_min',`kamer_max`='$kamer_max',`kamer_avg`='$kamer_avg',`tobi_min`='$tobi_min',`tobi_max`='$tobi_max',`tobi_avg`='$tobi_avg',`alex_min`='$alex_min',`alex_max`='$alex_max',`alex_avg`='$alex_avg',`zolder_min`='$zolder_min',`zolder_max`='$zolder_max',`zolder_avg`='$zolder_avg';";
		if(!$result = $db->query($query)) { die('There was an error running the query ['.$query .' - ' . $db->error . ']');}
	}

echo '<h2>Putting buiten temp to verbruik.egregius.be</h2>';
//$sql = "select left(stamp,10) as stamp,min(buiten) as buiten_min,max(buiten) as buiten_max,avg(buiten) as buiten_avg from temp group by left(stamp,10)";
$sql = "select left(stamp,10) as stamp,min(buiten_min) as buiten_min,max(buiten_max) as buiten_max,avg(buiten_avg) as buiten_avg from temp_hour group by left(stamp,10) ORDER BY `stamp` DESC LIMIT 0,10";
	if(!$result = $db->query($sql)) { die('There was an error running the query ['.$sql.' - '.$db->error.']');}
	$values=array();
	while ($row = $result->fetch_assoc()) $values[]=$row;$result->free();
	$dbe=new mysqli('server','user','password','database');
	if($dbe->connect_errno>0){die('Unable to connect to database ['.$dbe->connect_error.']');}
	foreach($values as $value) {
		$stamp=$value['stamp'];
		$buiten_min=$value['buiten_min'];$buiten_max=$value['buiten_max'];$buiten_avg=$value['buiten_avg'];
		$query = "INSERT INTO `temp_buiten` (`stamp`,`min`,`max`,`avg`) VALUES ('$stamp','$buiten_min','$buiten_max','$buiten_avg') ON DUPLICATE KEY UPDATE `min`='$buiten_min',`max`='$buiten_max',`avg`='$buiten_avg';";
		if(!$result = $dbe->query($query)) { die('There was an error running the query ['.$query .' - ' . $dbe->error . ']');}
	}
echo '<h2>Putting consumption into smappee_day</h2>';
$sql = "select left(timestamp,10) as stamp,min(consumption) as consumption_min,max(consumption) as consumption_max,avg(consumption) as consumption_avg from smappee group by left(timestamp,10)";
	if(!$result = $db->query($sql)) { die('There was an error running the query ['.$sql .' - ' . $db->error . ']');}
	$values=array();
	while ($row = $result->fetch_assoc()) $values[]=$row;$result->free();
	foreach($values as $value) {
		$stamp=$value['stamp'];
		$consumption_min=$value['consumption_min'];$consumption_max=$value['consumption_max'];$consumption_avg=$value['consumption_avg'];
		$query = "INSERT INTO `smappee_day` (`timestamp`,`consumption_min`,`consumption_max`,`consumption_avg`) VALUES ('$stamp','$consumption_min','$consumption_max','$consumption_avg') ON DUPLICATE KEY UPDATE `consumption_min`='$consumption_min',`consumption_max`='$consumption_max',`consumption_avg`='$consumption_avg';";
		if(!$result = $db->query($query)) { die('There was an error running the query ['.$query .' - ' . $db->error . ']');}
	}
$ctx=stream_context_create(array('http'=>array('timeout'=>30)));
$data=file_get_contents('https://verbruik.egregius.be/tellerjaar.php',false,$ctx);
if(!empty($data))apcu_store('jaarteller',$data);

echo '<hr>';
$remove=strftime("%Y-%m-%d",time-691200);
$sql="delete from temp where stamp < '$remove'";
echo $sql.'<br>';
if($result=$db->query($sql))echo $db->affected_rows.' removed from temp<br>';else die('There was an error running the query ['.$sql.'-'.$db->error.']');

$remove=strftime("%Y-%m-%d %H:%M",time-172700);
$sql="delete from diepvries where stamp < '$remove'";
echo $sql.'<br>';
if($result=$db->query($sql))echo $db->affected_rows.' removed from temp<br>';else die('There was an error running the query ['.$sql.'-'.$db->error.']');
$sql="delete from regen where stamp < '$remove'";
echo $sql.'<br>';
if($result=$db->query($sql))echo $db->affected_rows.' removed from regen<br>';else die('There was an error running the query ['.$sql.'-'.$db->error.']');
$sql="delete from smappee where timestamp < '$remove'";
echo $sql.'<br>';
if($result=$db->query($sql))echo $db->affected_rows.' removed from smappee<br>';else die('There was an error running the query ['.$sql.'-'.$db->error.']');
shell_exec('/var/www/home.egregius.be/secure/cleandisk.sh');
?>
