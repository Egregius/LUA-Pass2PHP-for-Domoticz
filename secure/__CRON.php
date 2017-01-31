<?php
$cron=604800;
$prev=apcu_fetch('cron'.$cron);
if($prev<time-$cron){
	apcu_store('cron'.$cron,time);
	if($prev==0||empty($prev))$prev=time;
	if((time-$prev)>apcu_fetch('maxcron'.$cron))apcu_store('maxcron'.$cron,(time-$prev));
	//print strftime("%Y-%m-%d %H:%M:%S",time()).'   => CRON604800'.PHP_EOL;
	$domoticz=json_decode(file_get_contents('http://127.0.0.1:8084/json.htm?type=devices&used=true'),true);
	if($domoticz){
		foreach($domoticz['result'] as $dom){
			$name=$dom['Name'];
			$type=$dom['Type'];
			if(isset($dom['SwitchType']))$switchtype=$dom['SwitchType'];else $switchtype='none';
			apcu_store('t'.$name,strtotime($dom['LastUpdate']));
			apcu_store('i'.$name,$dom['idx']);
			if($type=='Temp')apcu_store('s'.$name,str_replace(' C','',$dom['Data']));
			elseif($switchtype=='Dimmer'){
				if($dom['Data']=='Off')apcu_store('s'.$name,'Off');
				else apcu_store('s'.$name,filter_var($dom['Data'],FILTER_SANITIZE_NUMBER_INT));
			}
			else apcu_store('s'.$name,$dom['Data']);
		}
		usleep(10000);
	}
	$item='meldingen';if(!apcu_exists('s'.$item)){apcu_store('s'.$item,'On');apcu_store('t'.$item,1);}
	$item='lichten_auto';if(!apcu_exists('s'.$item)){apcu_store('s'.$item,'On');apcu_store('t'.$item,1);}
	$item='heating';if(!apcu_exists('s'.$item)){apcu_store('s'.$item,'On');apcu_store('t'.$item,1);}
	$item='weg';if(!apcu_exists('s'.$item)){apcu_store('s'.$item,'Off');apcu_store('t'.$item,1);}
	$item='slapen';if(!apcu_exists('s'.$item)){apcu_store('s'.$item,'Off');apcu_store('t'.$item,1);}
}

$cron=10;
$prev=apcu_fetch('cron'.$cron);
if($prev<time-5){
	apcu_store('cron'.$cron,time);
	if($prev==0||empty($prev))$prev=time;
	if((time-$prev)>apcu_fetch('maxcron'.$cron))apcu_store('maxcron'.$cron,(time-$prev));
	//print strftime("%Y-%m-%d %H:%M:%S",time()).'   => CRON10'.PHP_EOL;
	if(apcu_fetch('spirgarage')=='Off'&&apcu_fetch('tpirgarage')<time-150&&apcu_fetch('tpoort')<time-150&&apcu_fetch('tgarage')<time-150&&apcu_fetch('sgarage')=='On'&&apcu_fetch('slichten_auto')=='On'){
		sw('garage','Off');
	}
	if(apcu_fetch('sgarage')=='On'&&apcu_fetch('tgarage')<time-180){
		if(apcu_fetch('sdampkap')=='Off')double('dampkap','On');
	}elseif(apcu_fetch('sgarage')=='Off'&&apcu_fetch('tgarage')<time-600){
		if(apcu_fetch('sdampkap')=='On')double('dampkap','Off','1');
	}
	if(apcu_fetch('tpirinkom')<time-60&&apcu_fetch('tpirhall')<time-80&&apcu_fetch('tinkom')<time-90&&apcu_fetch('thall')<time-90&&apcu_fetch('slichten_auto')=='On'){if(apcu_fetch('sinkom')=='On')sw('inkom','Off');if(apcu_fetch('shall')=='On')sw('hall','Off');}
	if(apcu_fetch('tpirkeuken')<time-60&&apcu_fetch('twasbak')<time-80&&apcu_fetch('spirkeuken')=='Off'&&apcu_fetch('swasbak')=='On'&&apcu_fetch('swerkblad')=='Off'&&apcu_fetch('skeuken')=='Off'&&apcu_fetch('skookplaat')=='Off')sw('wasbak','Off');
	$weg=apcu_fetch('sweg');
	$slapen=apcu_fetch('sslapen');
	if($weg=='On'||$slapen=='On'){
		if($weg=='On'){
			$items=array('garage','denon','bureel','tv','tvled','kristal','eettafel','zithoek','terras','tuin','voordeur','keuken','werkblad','wasbak','kookplaat','sony','kamer','tobi','alex','lichtbadkamer1','lichtbadkamer2','badkamervuur');
			foreach($items as $item)if(apcu_fetch('s'.$item)!='Off')if(apcu_fetch('t'.$item)<time)sw($item,'Off');
		}elseif($slapen=='On'){
			$items=array('hall','bureel','denon','tv','tvled','kristal','eettafel','zithoek','garage','terras','tuin','voordeur','keuken','werkblad','wasbak','kookplaat');
			foreach($items as $item)if(apcu_fetch('s'.$item)!='Off')if(apcu_fetch('t'.$item)<time)sw($item,'Off');
		}
		$items=array('living','badkamer','kamer','tobi','alex');
		foreach($items as $item){${'setpoint'.$item}=apcu_fetch('setpoint'.$item);if(${'setpoint'.$item}!=0&&apcu_fetch('t'.$item)<time-3598)apcu_store('setpoint'.$item,0);}
		$items=array('tobi','living','kamer','alex');
		if(apcu_fetch('tweg')<time-57)if(apcu_fetch('spoortrf')=='On')sw('poortrf','Off');
	}
	include('__verwarming.php');
	include('__verwarmingbadkamer.php');
}

$cron=60;
$prev=apcu_fetch('cron'.$cron);
if($prev<time-$cron-5){
	apcu_store('cron'.$cron,time);
	if($prev==0||empty($prev))$prev=time;
	if((time-$prev)>apcu_fetch('maxcron'.$cron))apcu_store('maxcron'.$cron,(time-$prev));
	//print strftime("%Y-%m-%d %H:%M:%S",time()).'   => CRON60'.PHP_EOL;
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
				if($level>30)$level=30;
				sl($item,$level);
				if($level==30)apcu_store('dimaction'.$item,0);
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
	$s_living=apcu_fetch('sliving_set');
	$s_badkamer=apcu_fetch('sbadkamer_set');
	$s_kamer=apcu_fetch('skamer_set');
	$s_tobi=apcu_fetch('stobi_set');
	$s_alex=apcu_fetch('salex_set');
	if(apcu_fetch('sbrander')=='On')$brander=1;else $brander=0;
	if(apcu_fetch('sbadkamervuur')=='On')$badkamervuur=1;else $badkamervuur=0;
	if($living>0&&$badkamer>0){
		$query="INSERT IGNORE INTO `temp` (`stamp`,`buiten`,`living`,`badkamer`,`kamer`,`tobi`,`alex`,`zolder`,`s_living`,`s_badkamer`,`s_kamer`,`s_tobi`,`s_alex`,`brander`,`badkamervuur`) VALUES ('$stamp','$buiten_temp','$living','$badkamer','$kamer','$tobi','$alex','$zolder','$s_living','$s_badkamer','$s_kamer','$s_tobi','$s_alex','$brander','$badkamervuur');";
		$db=new mysqli('localhost','kodi','kodi','domotica');if($db->connect_errno>0)die('Unable to connect to database [' . $db->connect_error . ']');if(!$result=$db->query($query))die('There was an error running the query ['.$query .' - ' . $db->error . ']');$db->close();
	}
	$buienradar=apcu_fetch('buien');
	$Tregenpomp=apcu_fetch('tregenpomp');
	if($buienradar>0){
		$pomppauze=3600/max(array(1,($buienradar*20)));
		if($pomppauze>10800)$pomppauze=10800;
		elseif($pomppauze<900)$pomppauze=900;
	}else $pomppauze=7200;
	if(apcu_fetch('sregenpomp')=='On'&&$Tregenpomp<time-57)sw('regenpomp','Off','was on for '.convertToHours(time-$Tregenpomp));
	elseif(apcu_fetch('sregenpomp')=='Off'&&$Tregenpomp<time-$pomppauze)sw('regenpomp','On','was off for '.convertToHours(time-$Tregenpomp));
	if(apcu_fetch('skodi')=='On'&&apcu_fetch('tkodi')<time-298){if(pingport('192.168.2.7',1597)==1){$prevcheck=apcu_fetch('check192.168.2.57:1597');if($prevcheck>0)apcu_store('check192.168.2.57:1597',0);}else{$check=apcu_fetch('check192.168.2.57:1597')+1;if($check>0)apcu_store('check192.168.2.57:1597',$check);if($check>=5)sw('kodi','Off');}}
}

$cron=180;
$prev=apcu_fetch('cron'.$cron);
if($prev<time-$cron-8){
	apcu_store('cron'.$cron,time);
	if($prev==0||empty($prev))$prev=time;
	if((time-$prev)>apcu_fetch('maxcron'.$cron))apcu_store('maxcron'.$cron,(time-$prev));
	//print strftime("%Y-%m-%d %H:%M:%S",time()).'   => CRON180'.PHP_EOL;
	$wu=json_decode(file_get_contents('http://api.wunderground.com/api/c46771fe9413775e/conditions/q/BX/Beitem.json'),true);
	if(isset($wu['current_observation'])){
		$lastobservation=apcu_fetch('wu-observation');
		if(isset($wu['current_observation']['estimated']['estimated']))goto exitwunderground;
		elseif($wu['current_observation']['observation_epoch']<=$lastobservation)goto exitwunderground;
		else apcu_store('wu-observation',$wu['current_observation']['observation_epoch']);
		if($wu['current_observation']['feelslike_c']!=apcu_fetch('sbuiten_temp'))apcu_store('sbuiten_temp',$wu['current_observation']['feelslike_c']);
		if($wu['current_observation']['wind_kph']!=apcu_fetch('wind'))apcu_store('wind',$wu['current_observation']['wind_kph']);
		if($wu['current_observation']['wind_dir']!=apcu_fetch('wind_dir'))apcu_store('wind_dir',$wu['current_observation']['wind_dir']);
		apcu_store('icon',str_replace('http','https',$wu['current_observation']['icon_url']));
		//lg('Wunderground '.number_format($wu['current_observation']['feelslike_c'],1).'	'.number_format($wu['current_observation']['temp_c'],1).'	'.number_format($wu['current_observation']['wind_kph'],1).' '.number_format($wu['current_observation']['wind_gust_kph'],1));
	}
	exitwunderground:
	$rains=file_get_contents('http://gadgets.buienradar.nl/data/raintext/?lat=50.89&lon=3.11');
	$rains=str_split($rains,11);$totalrain=0;$aantal=0;
	foreach($rains as $rain){
		$aantal=$aantal+1;
		$totalrain=$totalrain+substr($rain,0,3);
		if($aantal==7)break;
	}
	$newbuien=(pow(10,((($totalrain/7)-109)/32)));
	if($newbuien<0.0004)$newbuien=0;
	if($newbuien!=apcu_fetch('buien'))apcu_store('buien',$newbuien);
	$tfrigo=apcu_fetch('sfrigo_temp');
	$frigo=apcu_fetch('sfrigo');
	if($frigo=='Off'&&$tfrigo>=5&&apcu_fetch('tfrigo')<time-300){sw('frigo','On');apcu_store('tfrigo',time);}
	elseif($frigo=='On'&&$tfrigo<=4.5&&apcu_fetch('tfrigo')<time-300){sw('frigo','Off');apcu_store('tfrigo',time);}
	include('gcal/gcal.php');
}

$cron=300;
$prev=apcu_fetch('cron'.$cron);
if($prev<time-$cron-15){
	apcu_store('cron'.$cron,time);
	if($prev==0||empty($prev))$prev=time;
	if((time-$prev)>apcu_fetch('maxcron'.$cron))apcu_store('maxcron'.$cron,(time-$prev));
	//print strftime("%Y-%m-%d %H:%M:%S",time()).'   => CRON300'.PHP_EOL;
	if(apcu_fetch('sweg')=='Off'&&apcu_fetch('sslapen')=='Off'){
		if(apcu_fetch('sGroheRed')=='Off')if(apcu_fetch('tslapen')<time-900)double('GroheRed','On');
		if(apcu_fetch('spoortrf')=='Off')if(apcu_fetch('tslapen')<time-900)double('poortrf','On');
	}
	if(apcu_fetch('smeldingen')=='On'){
		$items=array('living_temp','badkamer_temp','kamer_temp','tobi_temp','alex_temp','zolder_temp');$avg=0;
		foreach($items as $item)$avg=$avg+apcu_fetch('s'.$item);$avg=$avg/6;
		foreach($items as $item){
			$temp=apcu_fetch('s'.$item);
			if($temp>$avg+5&&$temp>25){
				$msg='T '.$item.'='.$temp.'°C. AVG='.round($avg,1).'°C';
				if(apcu_fetch('alerttemp'.$item)<time-3598){telegram($msg,false,2);ios($msg);apcu_store('alerttemp'.$item,time);}
			}
			if(apcu_fetch('t'.$item)<time-21590){if(apcu_fetch('alerttempupd'.$item)<time-43190){telegram($item.' not updated');apcu_store('alerttempupd'.$item,time);}}}
		$devices=array('tobiZ','alexZ',/*'livingZ','livingZZ',*/'kamerZ');
		foreach($devices as $device){
			if(apcu_fetch('t'.$device)<time-2000){if(apcu_fetch('nocom'.$device)<time-43190){telegram($device.' geen communicatie',true);apcu_store('nocom'.$device,time);}}}
		$buiten_temp=apcu_fetch('sbuiten_temp');
		if(apcu_fetch('sweg')=='Off'&&apcu_fetch('sslapen')=='Off'){if(($buiten_temp>apcu_fetch('skamer_temp')&&$buiten_temp>apcu_fetch('stobi_temp')&&$buiten_temp>apcu_fetch('salex_temp'))&&$buiten_temp>22&&(apcu_fetch('skamer_temp')>20||apcu_fetch('stobi_temp')>20||apcu_fetch('salex_temp')>20)&&(apcu_fetch('sraamkamer')=='Open'||apcu_fetch('sraamtobi')=='Open'||apcu_fetch('sraamalex')=='Open'))if((int)apcu_fetch('timeramen')<time-43190){telegram('Ramen boven dicht doen, te warm buiten. Buiten = '.$buiten_temp.',kamer = '.apcu_fetch('skamer_temp').', Tobi = '.apcu_fetch('stobi_temp').', Alex = '.apcu_fetch('salex_temp'),false,2);apcu_store('timeramen',time);}elseif(($buiten_temp<=apcu_fetch('skamer_temp')||$buiten_temp<=apcu_fetch('stobi_temp')||$buiten_temp<=apcu_fetch('salex_temp'))&&(apcu_fetch('skamer_temp')>20||apcu_fetch('stobi_temp')>20||apcu_fetch('salex_temp')>20)&&(apcu_fetch('sraamkamer')=='Closed'||apcu_fetch('sraamkamer')=='Closed'||apcu_fetch('sraamkamer')=='Closed'))if((int)apcu_fetch('timeramen')<time-43190){telegram('Ramen boven open doen, te warm binnen. Buiten = '.$buiten_temp.',kamer = '.apcu_fetch('skamer_temp').', Tobi = '.apcu_fetch('stobi_temp').', Alex = '.apcu_fetch('salex_temp'),false,2);apcu_store('timeramen',time);}}
	}
	if(apcu_fetch('svoordeur')=='On'&&apcu_fetch('tvoordeur')<time-598)sw('voordeur','Off');
	$nodes=json_decode(file_get_contents('http://127.0.0.1:8084/json.htm?type=openzwavenodes&idx=3'),true);
	if($nodes['NodesQueried']==1){
		$timehealnetwork=apcu_fetch('healnetwork');
		if($timehealnetwork<time-3600*24*2){
			$result=json_decode(file_get_contents('http://127.0.0.1:8084/json.htm?type=command&param=zwavenetworkheal&idx=3'),true);
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
					$healnode=json_decode(file_get_contents('http://127.0.0.1:8084/json.htm?type=command&param=zwavenodeheal&idx=3&node='.$node['NodeID']),true);
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
				if($node['Name']=='LivingZ'){$Uwake=1200;if(time>=strtotime('17:00'))$Uwake=480;if($difliving<1)$Uwake=240;if($conf['value']!=$Uwake&&time>apcu_fetch('UwakeLivingZ')){$result=json_decode(file_get_contents('http://192.168.2.10:8084/json.htm?type=command&param=applyzwavenodeconfig&idx='.$node['idx'].'&valuelist=2000_'.base64_encode($Uwake).'_3001_VW5wcm90ZWN0ZWQ=_'),true);if($result['status']=='OK')apcu_store('UwakeLivingZ',time+$conf['value']);lg(' Update Wakeupinterval for '.$node['Name'].' from '.$conf['value'].' to '.$Uwake.'. difliving='.$difliving);}}
				elseif($node['Name']=='LivingZE'){$Uwake=1200;if(time>=strtotime('17:00'))$Uwake=480;if($difliving<1)$Uwake=240;if($conf['value']!=$Uwake&&time>apcu_fetch('UwakeLivingZE')){$result=json_decode(file_get_contents('http://192.168.2.10:8084/json.htm?type=command&param=applyzwavenodeconfig&idx='.$node['idx'].'&valuelist=2000_'.base64_encode($Uwake).'_3001_VW5wcm90ZWN0ZWQ=_'),true);if($result['status']=='OK')apcu_store('UwakeLivingZE',time+$conf['value']);lg(' Update Wakeupinterval for '.$node['Name'].' from '.$conf['value'].' to '.$Uwake.'. difliving='.$difliving);}}
				elseif($node['Name']=='LivingZZ'){$Uwake=1200;if(time>=strtotime('17:00'))$Uwake=480;if($difliving<1)$Uwake=240;if($conf['value']!=$Uwake&&time>apcu_fetch('UwakeLivingZZ')){$result=json_decode(file_get_contents('http://192.168.2.10:8084/json.htm?type=command&param=applyzwavenodeconfig&idx='.$node['idx'].'&valuelist=2000_'.base64_encode($Uwake).'_3001_VW5wcm90ZWN0ZWQ=_'),true);if($result['status']=='OK')apcu_store('UwakeLivingZZ',time+$conf['value']);lg(' Update Wakeupinterval for '.$node['Name'].' from '.$conf['value'].' to '.$Uwake.'. difliving='.$difliving);}}
				elseif($node['Name']=="KamerZ"){$Uwake=1200;if(time<strtotime('5:00')||time>strtotime('20:00'))$Uwake=600;if($difkamer<1)$Uwake=300;if($conf['value']!=$Uwake&&time>apcu_fetch('UwakeKamerZ')){$result=json_decode(file_get_contents('http://192.168.2.10:8084/json.htm?type=command&param=applyzwavenodeconfig&idx='.$node['idx'].'&valuelist=2000_'.base64_encode($Uwake).'_3001_VW5wcm90ZWN0ZWQ=_'),true);if($result['status']=='OK')apcu_store('UwakeKamerZ',time+$conf['value']);lg(' Update Wakeupinterval for '.$node['Name'].' from '.$conf['value'].' to '.$Uwake.'. difkamer='.$difkamer);}}
				elseif($node['Name']=="TobiZ"){$Uwake=1200;if($s['heating']=='On'){if(date('W')%2==1){if(date('N')==3){if(time>strtotime('20:00'))$Uwake=600;}elseif(date('N')==4){if(time<strtotime('5:00')||time>strtotime('20:00'))$Uwake=600;}elseif(date('N')==5){if(time<strtotime('5:00'))$Uwake=600;}}else{if(date('N')==3){if(time>strtotime('20:00'))$Uwake=600;}elseif(in_array(date('N'),array(4,5,6))){if(time<strtotime('5:00')||time>strtotime('20:00'))$Uwake=600;}elseif(date('N')==7){if(time<strtotime('5:00'))$Uwake=600;}}}if($diftobi<1)$Uwake=240;if($conf['value']!=$Uwake&&time>apcu_fetch('UwakeTobiZ')){$result=json_decode(file_get_contents('http://192.168.2.10:8084/json.htm?type=command&param=applyzwavenodeconfig&idx='.$node['idx'].'&valuelist=2000_'.base64_encode($Uwake).'_3001_VW5wcm90ZWN0ZWQ=_'),true);if($result['status']=='OK')apcu_store('UwakeTobiZ',time+$conf['value']);lg(' Update Wakeupinterval for '.$node['Name'].' from '.$conf['value'].' to '.$Uwake.'. diftobi='.$diftobi);}}
				elseif($node['Name']=="AlexZ"){$Uwake=1200;if(time<strtotime('5:00')||time>strtotime('18:00'))$Uwake=600;if($difalex<1)$Uwake=240;if($conf['value']!=$Uwake&&time>apcu_fetch('UwakeAlexZ')){$result=json_decode(file_get_contents('http://192.168.2.10:8084/json.htm?type=command&param=applyzwavenodeconfig&idx='.$node['idx'].'&valuelist=2000_'.base64_encode($Uwake).'_3001_VW5wcm90ZWN0ZWQ=_'),true);if($result['status']=='OK')apcu_store('UwakeAlexZ',time+$conf['value']);lg(' Update Wakeupinterval for '.$node['Name'].' from '.$conf['value'].' to '.$Uwake.'. difalex='.$difalex);}}
			}*/

			unset($confs,$conf);
		}
	//}}}
	}else{
		apcu_store('healnetwork',0);
		$items=array('Media','Eettafel','Zithoek','Keuken','WasbakKookplaat','InkomVoordeur','BadkamerVuur','Kamer','Badkamer','Tobi','Zoldertrap','HallZolder','GarageTerras','Alex','GroheRed','Water','BureelTobi','Brander','Sony','TuinPomp','FilterWarmtepomp','PoortRF','Sirene','Kerstboom');
		foreach($items as $item)apcu_store('healnode-'.$item,0);
	}
	checkport('192.168.2.11',80);checkport('192.168.2.12',80);checkport('192.168.2.13',80);checkport('192.168.2.2',53);checkport('192.168.2.2',80);
	if(apcu_fetch('slichten_auto')=='Off')if(apcu_fetch('tlichten_auto')<time-10795)sw('lichten_auto','Off');
	if(apcu_fetch('smeldingen')=='Off'&&apcu_fetch('tmeldingen')<time-10795)sw('meldingen','On');
	if(apcu_fetch('tpirliving')<time-14395&&apcu_fetch('tpirgarage')<time-14395&&apcu_fetch('tpirinkom')<time-14395&&apcu_fetch('tpirhall')<time-14395&&apcu_fetch('tslapen')<time-14395&&apcu_fetch('tweg')<time-14395&&apcu_fetch('sweg')=='Off'&&apcu_fetch('sslapen')=="Off"){sw('slapen','On');telegram('slapen ingeschakeld na 4 uur geen beweging',false,2);}
	if(apcu_fetch('tpirliving')<time-43190&&apcu_fetch('tpirgarage')<time-43190&&apcu_fetch('tpirinkom')<time-43190&&apcu_fetch('tpirhall')<time-43190&&apcu_fetch('tslapen')<time-43190&&apcu_fetch('tweg')<time-43190&&apcu_fetch('sweg')=='Off'&&apcu_fetch('sslapen')=="On"){sw('slapen','Off');sw('weg','On');telegram('weg ingeschakeld na 12 uur geen beweging',false,2);}

	$items=array(5=>'keukenzolderg',6=>'wasbakkookplaat',7=>'werkbladtuin',8=>'inkomvoordeur',11=>'badkamer');
	foreach($items as $item => $name)if(apcu_fetch('refresh'.$item)<time-7198&&apcu_fetch('healnode')<time-900){RefreshZwave($item,'time',$name);break;}
	print strftime("%Y-%m-%d %H:%M:%S",time()).'   => MAXCRON: 10='.convertToHours(apcu_fetch('maxcron10')).' 60='.convertToHours(apcu_fetch('maxcron60')).' 180='.convertToHours(apcu_fetch('maxcron180')).' 300='.convertToHours(apcu_fetch('maxcron300')).PHP_EOL;
}

//if($s['zwembadfilter']=='On'){if(apcu_fetch('tzwembadfilter') < time-14395&&time>strtotime("18:00")&&$s['zwembadwarmte']=='Off')sw('zwembadfilter','Off');}else{if(apcu_fetch('tzwembadfilter')<time-14395&&time>strtotime("12:00")&&time<strtotime("16:00"))sw('zwembadfilter','On');}if($s['zwembadwarmte']=='On'){if(apcu_fetch('tzwembadwarmte')<time-86398)sw('zwembadwarmte','Off');if($s['zwembadfilter']=='Off')sw('zwembadfilter','On');}
/*
	$maxbuien=20;$maxwolken=80;$zonopen=1500;$zontoe=200;$zon=apcu_fetch('zon');$wind=apcu_fetch('wind');
	if(in_array(apcu_fetch('wind'),array('W','S','SE')))$maxwind=6;
	else $maxwind=8;
	if($s['luifel']!='Open'&&($wind>=$maxwind||$buienradar>=$maxbuien||$zon)<$zontoe)){
		lg('  --- Luifel: Wind='.$wind.'|Buien='.round($buienradar,0).'|Zon='.$zon.'|Luifel='.$s['luifel'].'|Last='.apcu_fetch('tluifel'));
		if($wind>=$maxwind){sw('luifel','Off');if(apcu_fetch('tluifel')<time-3598)sw('luifel','Off');}
		elseif($buienradar>=$maxbuien){sw('luifel'),'Off');if(apcu_fetch('tluifel')<time-3598)sw('luifel','Off');}
		elseif($zon<$zontoe){sw('luifel','Off');if(apcu_fetch('tluifel')<time-3598)sw('luifel','Off');}
	}
	elseif($s['luifel']!='Closed'&&time>strtotime('10:25')&&$wind<$maxwind-1&&$buienradar<$maxbuien-1&&$s['living_temp']>22&&$zon>$zonopen&&apcu_fetch('tluifel')<time-598){
		lg('  --- Luifel: Wind='.$wind.'|Buien='.round($buienradar,0).'|Zon='.$zon.'|Luifel='.$s['luifel'].'|Last='.apcu_fetch('tluifel'));
		sw('luifel','On',$msg);
	}
}*/

function checkport($ip,$port){if(pingport($ip,$port)==1){$prevcheck=apcu_fetch($ip.':'.$port);if($prevcheck>=3)telegram($ip.':'.$port.' online',true);if($prevcheck>0)apcu_store($ip.':'.$port,0);}else{$check=apcu_fetch($ip.':'.$port)+1;if($check>0)apcu_store($ip.':'.$port,$check);if($check==3)telegram($ip.':'.$port.' Offline',true);if($check%12==0)telegram($ip.':'.$port.' nog steeds Offline',true);}}
function pingport($ip,$port){$file=fsockopen($ip,$port,$errno,$errstr,10);$status=0;if(!$file)$status=-1;else{fclose($file);$status=1;}return $status;}
?>
