<?php
if(apcu_exists('sbuiten_temp'))$prevtemp=apcu_fetch('sbuiten_temp');
else{
	$query="SELECT buiten from temp order by stamp desc limit 0,1;";
	$db=new mysqli('server','user','password','database');
	if($db->connect_errno>0)die('Unable to connect to database ['.$db->connect_error.']');
	if(!$result=$db->query($query))die('There was an error running the query ['.$query.'-'.$db->error.']');
	while($row=$result->fetch_assoc())$prevtemp=$row['buiten'];$result->free();
	telegram('Buitentemp fetched from database: '.$prevtemp);
	$db->close();
}
if(apcu_exists('wind'))$prevwind=apcu_fetch('wind');
if(apcu_exists('buien'))$prevbuien=apcu_fetch('buien');
if(apcu_exists('wolken'))$prevwolken=apcu_fetch('wolken');

$wu=json_decode(file_get_contents('http://api.wunderground.com/api/a12345bc1234567d/conditions/q/BX/Beitem.json'),true);
if(isset($wu['current_observation'])){
	$lastobservation=apcu_fetch('wu-observation');
	if(isset($wu['current_observation']['estimated']['estimated']))goto exitwunderground;
	elseif($wu['current_observation']['observation_epoch']<=$lastobservation)goto exitwunderground;
	if(isset($wu['current_observation']['temp_c'])){$wutemp=$wu['current_observation']['temp_c'];if($wutemp>$prevtemp+0.5)$wutemp=$prevtemp+0.5;elseif($wutemp<$prevtemp-0.5)$wutemp=$prevtemp-0.5;}
	if(isset($wu['current_observation']['wind_kph']))$wuwind=$wu['current_observation']['wind_kph'];
	if(isset($wu['current_observation']['wind_gust_kph']))if($wu['current_observation']['wind_gust_kph']>$wuwind)$wuwind=$wu['current_observation']['wind_gust_kph'];
	if(isset($wu['current_observation']['precip_1hr_metric']))$wubuien=$wu['current_observation']['precip_1hr_metric']*35;
	if(isset($wu['current_observation']['wind_dir']))apcu_store('winddir',$wu['current_observation']['wind_dir']);
	if(isset($wu['current_observation']['icon']))apcu_store('icon',$wu['current_observation']['icon']);
}
exitwunderground:
$maxtemp=10;
$maxrain=0;
$ds=json_decode(file_get_contents('https://api.darksky.net/forecast/2a43b9cd64ef79ghi86j329285203310/51.9020861,3.2064103?units=si'),true);
if(isset($ds['currently'])){
	if(isset($ds['currently']['temperature'])){$dstemp=$ds['currently']['temperature'];if($dstemp>$prevtemp+0.5)$dstemp=$prevtemp+0.5;elseif($dstemp<$prevtemp-0.5)$dstemp=$prevtemp-0.5;}
	if(isset($ds['currently']['windSpeed']))$dswind=$ds['currently']['windSpeed'];
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
$rains=file_get_contents('http://gadgets.buienradar.nl/data/raintext/?lat=51.89&lon=3.21');
if(!empty($rains)){
	$rains=str_split($rains,11);$totalrain=0;$aantal=0;
	foreach($rains as $rain){
		$aantal=$aantal+1;
		$totalrain=$totalrain+substr($rain,0,3);
		if($aantal==7)break;
	}
	$newbuien=$totalrain/7;
	if($newbuien>100)$newbuien=100;
}

if(isset($prevtemp)&&isset($wutemp)&&isset($dstemp))apcu_store('sbuiten_temp',($prevtemp+$wutemp+$dstemp)/3);
elseif(isset($prevtemp)&&isset($wutemp))apcu_store('sbuiten_temp',($prevtemp+$wutemp)/2);
elseif(isset($prevtemp)&&isset($dstemp))apcu_store('sbuiten_temp',($prevtemp+$dstemp)/2);
elseif(isset($wutemp)&&isset($dstemp))apcu_store('sbuiten_temp',($wutemp+$dstemp)/2);
elseif(isset($wutemp))apcu_store('sbuiten_temp',$wutemp);
elseif(isset($dstemp))apcu_store('sbuiten_temp',$dstemp);

if(isset($prevwind)&&isset($wuwind)&&isset($dswind))apcu_store('wind',($prevwind+$wuwind+$dswind)/3);
elseif(isset($prevwind)&&isset($wuwind))apcu_store('wind',($prevwind+$wuwind)/2);
elseif(isset($prevwind)&&isset($dswind))apcu_store('wind',($prevwind+$dswind)/2);
elseif(isset($wuwind)&&isset($dswind))apcu_store('wind',($wuwind+$dswind)/2);
elseif(isset($wuwind))apcu_store('wind',$wuwind);
elseif(isset($dswind))apcu_store('wind',$dswind);

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
apcu_store('buien',$buien);
if(!isset($wubuien))$wubuien=0;
if(!isset($dsbuien))$dsbuien=0;
if(!isset($newbuien))$newbuien=0;
$query="INSERT IGNORE INTO `regen` (`buienradar`,`wunderground`,`darksky`,`buien`) VALUES ('$newbuien','$wubuien','$dsbuien','$buien');";
$db=new mysqli('server','user','password','database');
if($db->connect_errno>0)die('Unable to connect to database [' . $db->connect_error . ']');
if(!$result=$db->query($query))die('There was an error running the query ['.$query .' - ' . $db->error . ']');
$db->close();

if(apcu_fetch('sGroheRed')=='On'){
	if(apcu_fetch('swasbak')=='Off'&&apcu_fetch('swerkblad')=='Off'&&apcu_fetch('skeuken')=='Off'&&apcu_fetch('skookplaat')=='Off'&&apcu_fetch('tGroheRed')<time-300&&apcu_fetch('sUsage_grohered')<50)sw('GroheRed','Off');
}else{
	if((apcu_fetch('spirkeuken')=='On'&&apcu_fetch('tpirkeuken')<time-190)||(apcu_fetch('swasbak')=='On'&&apcu_fetch('twasbak')<time-190)||(apcu_fetch('skeuken')=='On'&&apcu_fetch('tkeuken')<time-190)||(apcu_fetch('skookplaat')=='On'&&apcu_fetch('tkookplaat')<time-190))sw('GroheRed','On');
}
if(apcu_fetch('smeldingen')=='On'&&apcu_fetch('tWeg')<time-300){
	$items=array('living_temp','badkamer_temp','kamer_temp','tobi_temp','alex_temp','zolder_temp');$avg=0;
	foreach($items as $item)$avg=$avg+apcu_fetch('s'.$item);$avg=$avg/6;
	foreach($items as $item){
		$temp=apcu_fetch('s'.$item);
		if($temp>$avg+5&&$temp>25){
			$msg='T '.$item.'='.$temp.'°C. AVG='.round($avg,1).'°C';
			if(apcu_fetch('alerttemp'.$item)<time-3598){telegram($msg,false,2);ios($msg);apcu_store('alerttemp'.$item,time);}
		}
		if(apcu_fetch('t'.$item)<time-43150){if(apcu_fetch('alerttempupd'.$item)<time-43100){telegram($item.' not updated');apcu_store('alerttempupd'.$item,time);}}}
	$devices=array('tobiZ','alexZ',/*'livingZ','livingZZ',*/'kamerZ');
	foreach($devices as $device){
		if(apcu_fetch('t'.$device)<time-2000){if(apcu_fetch('nocom'.$device)<time-43190){telegram($device.' geen communicatie',true);apcu_store('nocom'.$device,time);}}}
	$buiten_temp=apcu_fetch('sbuiten_temp');
	if($Weg==0){if(($buiten_temp>apcu_fetch('skamer_temp')&&$buiten_temp>apcu_fetch('stobi_temp')&&$buiten_temp>apcu_fetch('salex_temp'))&&$buiten_temp>22&&(apcu_fetch('skamer_temp')>20||apcu_fetch('stobi_temp')>20||apcu_fetch('salex_temp')>20)&&(apcu_fetch('sraamkamer')=='Open'||apcu_fetch('sraamtobi')=='Open'||apcu_fetch('sraamalex')=='Open'))if((int)apcu_fetch('timeramen')<time-43190){telegram('Ramen boven dicht doen, te warm buiten. Buiten = '.round($buiten_temp,1).',kamer = '.apcu_fetch('skamer_temp').', Tobi = '.apcu_fetch('stobi_temp').', Alex = '.apcu_fetch('salex_temp'),false,2);apcu_store('timeramen',time);}elseif(($buiten_temp<=apcu_fetch('skamer_temp')||$buiten_temp<=apcu_fetch('stobi_temp')||$buiten_temp<=apcu_fetch('salex_temp'))&&(apcu_fetch('skamer_temp')>20||apcu_fetch('stobi_temp')>20||apcu_fetch('salex_temp')>20)&&(apcu_fetch('sraamkamer')=='Closed'||apcu_fetch('sraamkamer')=='Closed'||apcu_fetch('sraamkamer')=='Closed'))if((int)apcu_fetch('timeramen')<time-43190){telegram('Ramen boven open doen, te warm binnen. Buiten = '.round($buiten_temp,1).',kamer = '.apcu_fetch('skamer_temp').', Tobi = '.apcu_fetch('stobi_temp').', Alex = '.apcu_fetch('salex_temp'),false,2);apcu_store('timeramen',time);}}

}
if(apcu_fetch('svoordeur')=='On'&&apcu_fetch('tvoordeur')<time-598)sw('voordeur','Off');
$nodes=json_decode(file_get_contents('http://127.0.0.1:8080/json.htm?type=openzwavenodes&idx=3'),true);
if($nodes['NodesQueried']==1){
	$timehealnetwork=apcu_fetch('healnetwork');
	if($timehealnetwork<time-3600*24*2){
		$result=json_decode(file_get_contents('http://127.0.0.1:8080/json.htm?type=command&param=zwavenetworkheal&idx=3'),true);
		if($result['status']=="OK"){
			apcu_store('healnetwork',time);
			exit;
		}
	}
	$kamers=array('living','tobi','alex','kamer');
	foreach($kamers as $kamer)${'dif'.$kamer}=number_format(apcu_fetch('s'.$kamer.'_temp')-apcu_fetch('s'.$kamer.'_set'),1);
	foreach($nodes['result'] as $node){
		if(in_array($node['NodeID'],array(2,3,4,5,6,7,8,9,10,11,12,13,14,15,17,18,19,20,22,23,25,26,27,29))){
			if($timehealnetwork<time-1800&&apcu_fetch('healnode-'.$node['Name'])<time-3600*24*7&&apcu_fetch('healnode')<time-300){
				$healnode=json_decode(file_get_contents('http://127.0.0.1:8080/json.htm?type=command&param=zwavenodeheal&idx=3&node='.$node['NodeID']),true);
				if($healnode['status']=="OK"){
					apcu_store('healnode',time);
					//lg('     Heal Node '.$node['Name'].' started');
					apcu_store('healnode-'.$node['Name'],time);
					exit;
				}
				unset($healnode);
			}
		}
		/*if($node['Product_name']=='Z Thermostat 014G0013'){if(is_array($node['config'])){$confs=$node['config'];foreach($confs as $conf){if($conf['label']=='Wake-up Interval'){
			if($node['Name']=='LivingZ'){$Uwake=1200;if(time>=strtotime('17:00'))$Uwake=480;if($difliving<1)$Uwake=240;if($conf['value']!=$Uwake&&time>apcu_fetch('UwakeLivingZ')){$result=json_decode(file_get_contents('http://127.0.0.1:8080/json.htm?type=command&param=applyzwavenodeconfig&idx='.$node['idx'].'&valuelist=2000_'.base64_encode($Uwake).'_3001_VW5wcm90ZWN0ZWQ=_'),true);if($result['status']=='OK')apcu_store('UwakeLivingZ',time+$conf['value']);lg(' Update Wakeupinterval for '.$node['Name'].' from '.$conf['value'].' to '.$Uwake.'. difliving='.$difliving);}}
			elseif($node['Name']=='LivingZE'){$Uwake=1200;if(time>=strtotime('17:00'))$Uwake=480;if($difliving<1)$Uwake=240;if($conf['value']!=$Uwake&&time>apcu_fetch('UwakeLivingZE')){$result=json_decode(file_get_contents('http://127.0.0.1:8080/json.htm?type=command&param=applyzwavenodeconfig&idx='.$node['idx'].'&valuelist=2000_'.base64_encode($Uwake).'_3001_VW5wcm90ZWN0ZWQ=_'),true);if($result['status']=='OK')apcu_store('UwakeLivingZE',time+$conf['value']);lg(' Update Wakeupinterval for '.$node['Name'].' from '.$conf['value'].' to '.$Uwake.'. difliving='.$difliving);}}
			elseif($node['Name']=='LivingZZ'){$Uwake=1200;if(time>=strtotime('17:00'))$Uwake=480;if($difliving<1)$Uwake=240;if($conf['value']!=$Uwake&&time>apcu_fetch('UwakeLivingZZ')){$result=json_decode(file_get_contents('http://127.0.0.1:8080/json.htm?type=command&param=applyzwavenodeconfig&idx='.$node['idx'].'&valuelist=2000_'.base64_encode($Uwake).'_3001_VW5wcm90ZWN0ZWQ=_'),true);if($result['status']=='OK')apcu_store('UwakeLivingZZ',time+$conf['value']);lg(' Update Wakeupinterval for '.$node['Name'].' from '.$conf['value'].' to '.$Uwake.'. difliving='.$difliving);}}
			elseif($node['Name']=="KamerZ"){$Uwake=1200;if(time<strtotime('5:00')||time>strtotime('20:00'))$Uwake=600;if($difkamer<1)$Uwake=300;if($conf['value']!=$Uwake&&time>apcu_fetch('UwakeKamerZ')){$result=json_decode(file_get_contents('http://127.0.0.1:8080/json.htm?type=command&param=applyzwavenodeconfig&idx='.$node['idx'].'&valuelist=2000_'.base64_encode($Uwake).'_3001_VW5wcm90ZWN0ZWQ=_'),true);if($result['status']=='OK')apcu_store('UwakeKamerZ',time+$conf['value']);lg(' Update Wakeupinterval for '.$node['Name'].' from '.$conf['value'].' to '.$Uwake.'. difkamer='.$difkamer);}}
			elseif($node['Name']=="TobiZ"){$Uwake=1200;if($s['heating']=='On'){if(date('W')%2==1){if(date('N')==3){if(time>strtotime('20:00'))$Uwake=600;}elseif(date('N')==4){if(time<strtotime('5:00')||time>strtotime('20:00'))$Uwake=600;}elseif(date('N')==5){if(time<strtotime('5:00'))$Uwake=600;}}else{if(date('N')==3){if(time>strtotime('20:00'))$Uwake=600;}elseif(in_array(date('N'),array(4,5,6))){if(time<strtotime('5:00')||time>strtotime('20:00'))$Uwake=600;}elseif(date('N')==7){if(time<strtotime('5:00'))$Uwake=600;}}}if($diftobi<1)$Uwake=240;if($conf['value']!=$Uwake&&time>apcu_fetch('UwakeTobiZ')){$result=json_decode(file_get_contents('http://127.0.0.1:8080/json.htm?type=command&param=applyzwavenodeconfig&idx='.$node['idx'].'&valuelist=2000_'.base64_encode($Uwake).'_3001_VW5wcm90ZWN0ZWQ=_'),true);if($result['status']=='OK')apcu_store('UwakeTobiZ',time+$conf['value']);lg(' Update Wakeupinterval for '.$node['Name'].' from '.$conf['value'].' to '.$Uwake.'. diftobi='.$diftobi);}}
			elseif($node['Name']=="AlexZ"){$Uwake=1200;if(time<strtotime('5:00')||time>strtotime('18:00'))$Uwake=600;if($difalex<1)$Uwake=240;if($conf['value']!=$Uwake&&time>apcu_fetch('UwakeAlexZ')){$result=json_decode(file_get_contents('http://127.0.0.1:8080/json.htm?type=command&param=applyzwavenodeconfig&idx='.$node['idx'].'&valuelist=2000_'.base64_encode($Uwake).'_3001_VW5wcm90ZWN0ZWQ=_'),true);if($result['status']=='OK')apcu_store('UwakeAlexZ',time+$conf['value']);lg(' Update Wakeupinterval for '.$node['Name'].' from '.$conf['value'].' to '.$Uwake.'. difalex='.$difalex);}}
		}*/

		unset($confs,$conf);
	}
//}}}
}else{
	apcu_store('healnetwork',0);
	$items=array('Media','Eettafel','Zithoek','Keuken','WasbakKookplaat','InkomVoordeur','BadkamerVuur','Kamer','Badkamer','Tobi','Zoldertrap','HallZolder','GarageTerras','Alex','GroheRed','Water','BureelTobi','Brander','Sony','TuinPomp','FilterWarmtepomp','PoortRF','Sirene','Kerstboom');
	foreach($items as $item)apcu_store('healnode-'.$item,0);
}
if(apcu_fetch('skodi')=='On'){if(pingport('192.168.2.7',1597)==1){$prevcheck=apcu_fetch('check192.168.2.57:1597');if($prevcheck>0)apcu_store('check192.168.2.57:1597',0);}else{$check=apcu_fetch('check192.168.2.57:1597')+1;if($check>0)apcu_store('check192.168.2.57:1597',$check);if($check>=5)sw(apcu_fetch('ikodi'),'Off','kodi');}}

checkport('192.168.2.11',80);checkport('192.168.2.12',80);checkport('192.168.2.13',80);checkport('192.168.2.2',53);checkport('192.168.2.2',80);checkport($smappeeip,80);
if(!$auto)if(apcu_fetch('tlichten_auto')<time-10795)sw('lichten_auto','On');
if(!$meldingen&&apcu_fetch('tmeldingen')<time-10795)sw('meldingen','On');
if(apcu_fetch('tpirliving')<time-14395&&apcu_fetch('tpirgarage')<time-14395&&apcu_fetch('tpirinkom')<time-14395&&apcu_fetch('tpirhall')<time-14395&&apcu_fetch('tWeg')<time-14395&&$Weg==0){apcu_store('Weg',1);apcu_store('tWeg',time);telegram('Slapen ingeschakeld na 4 uur geen beweging',false,2);}
if(apcu_fetch('tpirliving')<time-43190&&apcu_fetch('tpirgarage')<time-43190&&apcu_fetch('tpirinkom')<time-43190&&apcu_fetch('tpirhall')<time-43190&&apcu_fetch('tWeg')<time-43190&&$Weg==1){apcu_store('Weg',2);apcu_store('tWeg',time);telegram('Weg ingeschakeld na 12 uur geen beweging',false,2);}

$items=array(5=>'keukenzolderg',6=>'wasbakkookplaat',7=>'werkbladtuin',8=>'inkomvoordeur',11=>'badkamer');
foreach($items as $item => $name)if(apcu_fetch('refresh'.$item)<time-7198&&apcu_fetch('healnode')<time-900){RefreshZwave($item,'time',$name);break;}

if(time>strtotime('0:00')&&time<strtotime('0:05')){
	apcu_store('power_min',140);
	apcu_store('power_max',6000);
}
if(apcu_fetch('swater')=='On'){
	if(apcu_fetch('twater')<time-1800)double('water','Off');
}else{
	if($maxrain==0){
		if(time>=strtotime('22:00')&&time<strtotime('22:10')){
			$stamp=strftime("%G-%m-%d %k:%M:%S",time-3600*48);
			$query="select SUM(`buien`) as buien from regen where stamp > '$STAMP';";
			$db=new mysqli('server','user','password','database');
			if($db->connect_errno>0)die('Unable to connect to database ['.$db->connect_error.']');
			if(!$result=$db->query($query))die('There was an error running the query ['.$query.'-'.$db->error.']');
			while($row=$result->fetch_assoc())$rainpast=$row['buien'];$result->free();$db->close();
			if($rainpast==0){
				sw('water','On');
				telegram('Automatisch tuin water geven gestart');
			}
		}
	}
}
//if($s['zwembadfilter']=='On'){if(apcu_fetch('tzwembadfilter') < time-14395&&time>strtotime("18:00")&&$s['zwembadwarmte']=='Off')sw('zwembadfilter','Off');}else{if(apcu_fetch('tzwembadfilter')<time-14395&&time>strtotime("12:00")&&time<strtotime("16:00"))sw('zwembadfilter','On');}if($s['zwembadwarmte']=='On'){if(apcu_fetch('tzwembadwarmte')<time-86398)sw('zwembadwarmte','Off');if($s['zwembadfilter']=='Off')sw('zwembadfilter','On');}
