<?php
$buienradar=$weer['buien'];
$buiten_temp=$weer['buiten_temp'];
if(cget('cron10')<time-10){
	cset('cron10',time);
	$buienradar=$weer['buien'];$buiten_temp=$weer['buiten_temp'];$wind=$weer['wind'];
	if($s['weg']=='On'){
		if($s['heating']!='Off'&&strtotime($t['heating'])<time-3598){
			sw($i['heating'],'Off','heating');
			$s['heating']='Off';
		}
	}else{
		if($s['heating']!='On'){
			sw($i['heating'],'On','heating');
			$s['heating']='On';
		}
	}
	$Setkamer=6;$setpointkamer=cget('setpointkamer');if($setpointkamer!=0&&strtotime($t['kamer_set'])<time-3598){cset('setpointkamer',0);$setpointkamer=0;}if($setpointkamer!=2){if($buiten_temp<14&&$s['raamkamer']=='Closed'&&$s['heating']=='On'&&(strtotime($t['raamkamer'])<time-7198||time>strtotime('21:00'))){$Setkamer=12.0;if(time<strtotime('5:00')||time>strtotime('21:00'))$Setkamer=16;}if($s['kamer_set']!=$Setkamer){ud($i['kamer_set'],0,$Setkamer,'Rkamer_set');$s['kamer_set']=$Setkamer;}}
	$Settobi=6;$setpointtobi=cget('setpointtobi');if($setpointtobi!=0&&strtotime($t['tobi_set'])<time-3598){cset('setpointtobi',0);$setpointtobi=0;}if($setpointtobi!=2){if($buiten_temp<14&&$s['raamtobi']=='Closed'&&$s['heating']=='On'&&(strtotime($t['raamtobi'])<time-7198||time>strtotime('21:00'))){$Settobi=12.0;if(date('W')%2==1){if(date('N')==3){if(time>strtotime('21:00'))$Settobi=16;}elseif(date('N')==4){if(time<strtotime('5:00')||time>strtotime('21:00'))$Settobi=16;}elseif(date('N')==5){if(time<strtotime('5:00'))$Settobi=16;}}else{if(date('N')==3){if(time>strtotime('21:00'))$Settobi=16;}elseif(in_array(date('N'),array(4,5,6))){if(time<strtotime('5:00')||time>strtotime('21:00'))$Settobi=16;}elseif(date('N')==7){if(time<strtotime('5:00'))$Settobi=16;}}}if(isset($s['tobi_set'])&&$s['tobi_set']!=$Settobi){ud($i['tobi_set'],0,$Settobi,'Rtobi_set');$s['tobi_set']=$Settobi;}}
	$Setalex=6;$setpointalex=cget('setpointalex');if($setpointalex!=0&&strtotime($t['alex_set'])<time-28795){cset('setpointalex',0);$setpointalex=0;}if($setpointalex!=2){if($buiten_temp<16&&$s['raamalex']=='Closed'&&$s['heating']=='On'&&(strtotime($t['raamalex'])<time-1800||time>strtotime('19:00'))){$Setalex=12;if(time<strtotime('5:00')||time>strtotime('19:00'))$Setalex=16.0;}if($s['alex_set']!=$Setalex){ud($i['alex_set'],0,$Setalex,'Ralex_set');$s['alex_set']=$Setalex;}}
	$Setliving=14;$setpointliving=cget('setpointliving');if($setpointliving!=0&&strtotime($t['living_set'])<time-10795){cset('setpointliving',0);$setpointliving=0;}if($setpointliving!=2){if($buiten_temp<20&&$s['heating']=='On'&&$s['raamliving']=='Closed'){$Setliving=17;if(time>=strtotime('5:00')&&time<strtotime('8:15'))$s['slapen']=='On'?$Setliving=17.0:$Setliving=20.0;elseif(time>=strtotime('8:15')&&time<strtotime('19:55'))$s['slapen']=='On'?$Setliving=19.0:$Setliving=20.5;}if($s['living_set']!=$Setliving){ud($i['living_set'],0,$Setliving,'Rliving_set');$s['living_set']=$Setliving;}}

	$kamers=array('living','tobi','alex','kamer');$bigdif=100;$timebrander=time-strtotime($t['brander']);
	foreach($kamers as $kamer){
		${'dif'.$kamer}=number_format($s[$kamer.'_temp']-$s[$kamer.'_set'],1);if(${'dif'.$kamer}>9.9)${'dif'.$kamer}=9.9;if(${'dif'.$kamer}<$bigdif&&$kamer!='kamer')$bigdif=${'dif'.$kamer};${'Set'.$kamer}=$s[$kamer.'_set'];
	}
	foreach($kamers as $kamer){
		if(${'dif'.$kamer}<=number_format(($bigdif+ 0.2),1)&&${'dif'.$kamer}<2)${'RSet'.$kamer}=setradiator($kamer,${'dif'.$kamer},true,$s[$kamer.'_set']);else ${'RSet'.$kamer}=setradiator($kamer,${'dif'.$kamer},false,$s[$kamer.'_set']);
	}

	if(round($s['kamerZ'],1)!=round($RSetkamer,1)){lg('Danfoss KamerZ was '.$s['kamerZ'].',nieuw='.$RSetkamer);ud($i['kamerZ'],0,$RSetkamer,'RkamerZ');}
	if(round($s['tobiZ'],1)!=round($RSettobi,1)){lg('Danfoss tobiZ was '.$s['tobiZ'].',nieuw='.$RSettobi);ud($i['tobiZ'],0,$RSettobi,'RtobiZ');}
	if(round($s['alexZ'],1)!=round($RSetalex,1)){lg('Danfoss alexZ was '.$s['alexZ'].',nieuw='.$RSetalex);ud($i['alexZ'],0,$RSetalex,'RalexZ');}
	if(round($s['livingZ'],1)!=round($RSetliving,1)){lg('Danfoss livingZ was '.$s['livingZ'].',nieuw='.$RSetliving);ud($i['livingZ'], 0,$RSetliving,'RlivingZ');}
	if(round($s['livingZZ'],1)!=round($RSetliving,1)){lg('Danfoss livingZZ was '.$s['livingZZ'].',nieuw='.$RSetliving);ud($i['livingZZ'],0,$RSetliving,'RlivingZZ');}
	if(round($s['livingZE'],1)!=round($RSetliving,1)){lg('Danfoss livingZE was '.$s['kamerZ'].',nieuw='.$RSetliving);ud($i['livingZE'],0,$RSetliving,'RlivingZE');}

	if($bigdif<=-0.6&&$s['brander']=="Off"&&$timebrander>60)sw($i['brander'],'On', 'brander1 dif = '.$bigdif.', was off for '.convertToHours($timebrander));
	elseif($bigdif<=-0.5&&$s['brander']=="Off"&&$timebrander>120)sw($i['brander'],'On', 'brander2 dif = '.$bigdif.', was off for '.convertToHours($timebrander));
	elseif($bigdif<=-0.4&&$s['brander']=="Off"&&$timebrander>180)sw($i['brander'],'On', 'brander3 dif = '.$bigdif.', was off for '.convertToHours($timebrander));
	elseif($bigdif<=-0.3&&$s['brander']=="Off"&&$timebrander>300)sw($i['brander'],'On', 'brander4 dif = '.$bigdif.', was off for '.convertToHours($timebrander));
	elseif($bigdif<=-0.2&&$s['brander']=="Off"&&$timebrander>450)sw($i['brander'],'On', 'brander5 dif = '.$bigdif.', was off for '.convertToHours($timebrander));
	elseif($bigdif<=-0.1&&$s['brander']=="Off"&&$timebrander>600)sw($i['brander'],'On', 'brander6 dif = '.$bigdif.', was off for '.convertToHours($timebrander));
	elseif($bigdif<=0	&&$s['brander']=="Off"&&$timebrander>2400)sw($i['brander'],'On', 'brander7 dif = '.$bigdif.', was off for '.convertToHours($timebrander));
	elseif($bigdif>0	&&$s['brander']=="On" &&$timebrander>30)sw($i['brander'],'Off','brander8 dif = '.$bigdif.', was on for '.convertToHours($timebrander));
	elseif($bigdif>=0	&&$s['brander']=="On"&&$timebrander>120)sw($i['brander'],'Off','brander9 dif = '.$bigdif.', was on for '.convertToHours($timebrander));
	elseif($bigdif>=-0.1&&$s['brander']=="On"&&$timebrander>180)sw($i['brander'],'Off','brander10 dif = '.$bigdif.', was on for '.convertToHours($timebrander));
	elseif($bigdif>=-0.2&&$s['brander']=="On"&&$timebrander>240)sw($i['brander'],'Off','brander11 dif = '.$bigdif.', was on for '.convertToHours($timebrander));
	elseif($bigdif>=-0.3&&$s['brander']=="On"&&$timebrander>300)sw($i['brander'],'Off','brander12 dif = '.$bigdif.', was on for '.convertToHours($timebrander));
	elseif($bigdif>=-0.4&&$s['brander']=="On"&&$timebrander>360)sw($i['brander'],'Off','brander13 dif = '.$bigdif.', was on for '.convertToHours($timebrander));
	elseif($bigdif>=-0.5&&$s['brander']=="On"&&$timebrander>420)sw($i['brander'],'Off','brander14 dif = '.$bigdif.', was on for '.convertToHours($timebrander));
	elseif($bigdif>=-0.6&&$s['brander']=="On"&&$timebrander>900)sw($i['brander'],'Off','brander15 dif = '.$bigdif.', was on for '.convertToHours($timebrander));

	if($s['deurbadkamer']=="Open"){
		if($s['badkamer_set']!=12&&(strtotime($t['deurbadkamer'])<time-57||$s['lichtbadkamer']=='Off')){
			ud($i['badkamer_set'],0,12,'badkamer_set 12 deur open');
			$s['badkamer_set']=12.0;
		}
	}
	elseif($s['deurbadkamer']=="Closed"&&$s['heating']=='On'){
		if(($s['lichtbadkamer']=='On'||$s['lichtbadkamer1']=='On'||$s['lichtbadkamer2']=='On')&&$s['badkamer_set']!=22.5){
			ud($i['badkamer_set'],0,22.5,'badkamer_set 22.5 deur dicht en licht aan');
			$s['badkamer_set']=22.5;
		}elseif($s['lichtbadkamer']=='Off'&&$s['lichtbadkamer1']=='Off'&&$s['lichtbadkamer2']=='Off'&&$s['badkamer_set']!=15){
			ud($i['badkamer_set'],0,15,'badkamer_set 15 deur dicht en licht uit');
			$s['badkamer_set']=15.0;
		}
	}
	$difbadkamer=number_format($s['badkamer_temp']-$s['badkamer_set'],1);
	$timebadkvuur=time-strtotime($t['badkamervuur']);
	if($difbadkamer<=-0.2&&$s['badkamervuur']=="Off"&&$timebadkvuur>180)
		double($i['badkamervuur'],'On','badkamervuur1 dif = '.$difbadkamer.' was off for '.convertToHours($timebadkvuur));
	elseif($difbadkamer<=-0.1&&$s['badkamervuur']=="Off"&&$timebadkvuur>240)
		double($i['badkamervuur'],'On','badkamervuur2 dif = '.$difbadkamer.' was off for '.convertToHours($timebadkvuur));
	elseif($difbadkamer<=0&&$s['badkamervuur']=="Off"&&$timebadkvuur>360)
		double($i['badkamervuur'],'On','badkamervuur3 dif = '.$difbadkamer.' was off for '.convertToHours($timebadkvuur));
	elseif($difbadkamer>=0&&$s['badkamervuur']=="On"&&$timebadkvuur>30)
		double($i['badkamervuur'],'Off','badkamervuur4 dif = '.$difbadkamer.' was on for '.convertToHours($timebadkvuur));
	elseif($difbadkamer>=-0.2&&$s['badkamervuur']=="On"&&$timebadkvuur>120)
		double($i['badkamervuur'],'Off','badkamervuur5 dif = '.$difbadkamer.' was on for '.convertToHours($timebadkvuur));
	elseif($difbadkamer>=-0.4&&$s['badkamervuur']=="On"&&$timebadkvuur>180)
		double($i['badkamervuur'],'Off','badkamervuur6 dif = '.$difbadkamer.' was on for '.convertToHours($timebadkvuur));
	if($s['badkamer_temp']>$weer['badkamer_temp']&&$s['badkamer_temp']>$s['badkamer_set']&&strtotime($t['badkamervuur'])<time-600)
		sw($i['badkamervuur'],'Off','badkamervuur door '.'badkamer_temp'.' prev='.$weer['badkamer_temp'].', new='.$s['badkamer_temp']);

	if($s['pirgarage']=='Off'&&strtotime($t['pirgarage'])<time-178&&strtotime($t['poort'])<time-178&&strtotime($t['garage'])<time-178&&$s['garage']=='On'&&$s['lichten_auto']=='On')sw($i['garage'],'Off','licht garage');
	if($s['garage']=='On'&&strtotime($t['garage'])<time-178){
		if($s['dampkap']=='Off')double($i['dampkap'],'On','dampkap');
	}elseif($s['garage']=='Off'&&strtotime($t['garage'])<time-600){
		if($s['dampkap']=='On')double($i['dampkap'],'Off','dampkap');
	}
	if(strtotime($t['pirinkom'])<time-118&&strtotime($t['pirhall'])<time-118&&strtotime($t['inkom'])<time-118&&strtotime($t['hall'])<time-118&&$s['lichten_auto']=='On'){if($s['inkom']=='On')sw($i['inkom'],'Off','licht inkom');if($s['hall']=='On')sw($i['hall'],'Off','licht hall');}
	if(strtotime($t['pirkeuken'])<time-118&&strtotime($t['wasbak'])<time-118&&$s['pirkeuken']=='Off'&&$s['wasbak']=='On'&&$s['werkblad']=='Off'&&$s['keuken']=='Off'&&$s['kookplaat']=='Off')sw($i['wasbak'],'Off','wasbak pir keuken');

}

if(cget('cron30')<time-29){
	cset('cron30',time);
	$items=array('eettafel','zithoek','tobi','kamer','alex');
	foreach($items as $item){if($s[$item]!='Off'){$action=cget('dimmer'.$item);if($action==1){$level=filter_var($s[$item],FILTER_SANITIZE_NUMBER_INT);$level=floor($level*0.95);if($level<2)$level=0;if($level==20)$level=19;sl($i[$item],$level,$item);if($level==0)cset('dimmer'.$item,0);}elseif($action==2){$level=filter_var($s[$item],FILTER_SANITIZE_NUMBER_INT);$level=$level+2;if($level==20)$level=21;if($level>30)$level=30;sl($i[$item],$level,$item);if($level==30)cset('dimmer'.$item,0);}}}

}

if(cget('cron60')<time-59){
	cset('cron60',time);
	$items=array('living_temp','badkamer_temp','kamer_temp','tobi_temp','alex_temp','zolder_temp');foreach($items as $item)$weer[$item]=$s[$item];
	$uweer=serialize($weer);cset('weer',$uweer);
	$stamp=sprintf("%s",date("Y-m-d H:i"));$living=$s['living_temp'];$badkamer=$s['badkamer_temp'];$kamer=$s['kamer_temp'];$tobi=$s['tobi_temp'];$alex=$s['alex_temp'];$zolder=$s['zolder_temp'];$s_living=$s['living_set'];$s_badkamer=$s['badkamer_set'];$s_kamer=$s['kamer_set'];$s_tobi=$s['tobi_set'];$s_alex=$s['alex_set'];if($s['brander']=='On')$brander=1;else $brander=0;if($s['badkamervuur']=='On')$badkamervuur=1;else $badkamervuur=0;
	$query="INSERT IGNORE INTO `temp` (`stamp`,`buiten`,`living`,`badkamer`,`kamer`,`tobi`,`alex`,`zolder`,`s_living`,`s_badkamer`,`s_kamer`,`s_tobi`,`s_alex`,`brander`,`badkamervuur`) VALUES ('$stamp','$buiten_temp','$living','$badkamer','$kamer','$tobi','$alex','$zolder','$s_living','$s_badkamer','$s_kamer','$s_tobi','$s_alex','$brander','$badkamervuur');";
	$db=new mysqli('localhost','kodi','kodi','domotica');if($db->connect_errno>0)die('Unable to connect to database [' . $db->connect_error . ']');if(!$result=$db->query($query))die('There was an error running the query ['.$query .' - ' . $db->error . ']');$db->close();

}

if(cget('cron120')<time-119){
	cset('cron120',time);
	$Tregenpomp=strtotime($t['regenpomp']);if($buienradar>0){$pomppauze=3600/max(array(1,($buienradar*20)));if($pomppauze>10800)$pomppauze=10800;}else $pomppauze=3600;if($s['regenpomp']=='On'&&$Tregenpomp<time-57)sw($i['regenpomp'],'Off','regenpomp off, was on for '.convertToHours(time-$Tregenpomp));elseif($s['regenpomp']=='Off'&&$Tregenpomp<time-$pomppauze)sw($i['regenpomp'],'On','regenpomp on, was off for '.convertToHours(time-$Tregenpomp));
	if($s['kodi']=='On'&&strtotime($t['kodi'])<time-298){if(pingport('192.168.2.7',1597)==1){$prevcheck=cget('check192.168.2.57:1597');if($prevcheck>0)cset('check192.168.2.57:1597',0);}else{$check=cget('check192.168.2.57:1597')+1;if($check>0)cset('check192.168.2.57:1597',$check);if($check>=5)sw($i['kodi'],'Off','kodi');}}
	include('gcal/gcal.php');
}

if(cget('cron180')<time-179){
	cset('cron180',time);
	$wu=json_decode(curl('http://api.wunderground.com/api/c46771fe9413775e/conditions/q/BX/Beitem.json'),true);
	if(isset($wu['current_observation'])){
		$lastobservation=cget('wu-observation');
		if(isset($wu['current_observation']['estimated']['estimated']))goto exitwunderground;
		elseif($wu['current_observation']['observation_epoch']<=$lastobservation)goto exitwunderground;
		else cset('wu-observation',$wu['current_observation']['observation_epoch']);
		$weer['buiten_temp']=$wu['current_observation']['feelslike_c'];
		lg('Wunderground '.number_format($wu['current_observation']['feelslike_c'],1).'	'.number_format($wu['current_observation']['temp_c'],1).'	'.number_format($wu['current_observation']['wind_kph'],1).' '.number_format($wu['current_observation']['wind_gust_kph'],1).' Newtemp='.$weer['buiten_temp']);
		$weer['wind']=round(max(array($wu['current_observation']['wind_kph'],$wu['current_observation']['wind_gust_kph'])),0);
		$weer['wind_dir']=$wu['current_observation']['wind_dir'];
		$weer['icon']=str_replace('http://','https://',$wu['current_observation']['icon_url']);
	}
	exitwunderground:
	$rains=curl('http://gadgets.buienradar.nl/data/raintext/?lat=50.89&lon=3.11');
	$rains=str_split($rains,11);$totalrain=0;$aantal=0;
	foreach($rains as $rain){$aantal=$aantal+1;$totalrain=$totalrain+substr($rain,0,3);if($aantal==7)break;}$newbuienradar=pow(10,((($totalrain/7)-109)/32));if(isset($newbuienradar))$weer['buien']=$newbuienradar;
	$uweer=serialize($weer);
	cset('weer',$uweer);
}

if(cget('cron300')<time-299){
	cset('cron300',time);
	if($s['weg']=='Off'&&$s['slapen']=='Off'){if($s['GroheRed']=='Off')if(strtotime($t['slapen'])<time-900)double($i['GroheRed'],'On',$item);if($s['poortrf']=='Off')if(strtotime($t['slapen'])<time-900)double($i['poortrf'],'On',$item);}
	if($s['meldingen']=='On'){
		$items=array('living_temp','badkamer_temp','kamer_temp','tobi_temp','alex_temp','zolder_temp');$avg=0;
		foreach($items as $item)$avg=$avg+$s[$item];$avg=$avg/6;
		foreach($items as $item){$temp=$s[$item];if($temp>$avg+5&&$temp>25){$msg='T '.$item.'='.$temp.'°C. AVG='.round($avg,1).'°C';if(cget('timealerttemp'.$item)<time-3598){telegram($msg,false,2);ios($msg);cset('timealerttemp'.$item,time);}}if(strtotime($t[$item])<time-21590){if(cget('timealerttempupd'.$item)<time-43190){telegram($item.' not updated');cset('timealerttempupd'.$item,time);}}}
		$devices=array('tobiZ','alexZ','livingZ','livingZZ','livingZE','kamerZ');
		foreach($devices as $device){if(strtotime($t[$device])<time-2000){if(cget('timealert'.$device)<time-43190){telegram($device.' geen communicatie',true);cset('timealert'.$device,time);}}}
		if($s['weg']=='Off'&&$s['slapen']=='Off'){if(($buiten_temp>$s['kamer_temp']&&$buiten_temp>$s['tobi_temp']&&$buiten_temp>$s['alex_temp'])&&$buiten_temp>22&&($s['kamer_temp']>20||$s['tobi_temp']>20||$s['alex_temp']>20)&&($s['raamkamer']=='Open'||$s['raamtobi']=='Open'||$s['raamalex']=='Open'))if((int)cget('timeramen')<time-43190){telegram('Ramen boven dicht doen, te warm buiten. Buiten = '.$buiten_temp.',kamer = '.$s['kamer_temp'].', Tobi = '.$s['tobi_temp'].', Alex = '.$s['alex_temp'],false,2);cset('timeramen',time);}elseif(($buiten_temp<=$s['kamer_temp']||$buiten_temp<=$s['tobi_temp']||$buiten_temp<=$s['alex_temp'])&&($s['kamer_temp']>20||$s['tobi_temp']>20||$s['alex_temp']>20)&&($s['raamkamer']=='Closed'||$s['raamkamer']=='Closed'||$s['raamkamer']=='Closed'))if((int)cget('timeramen')<time-43190){telegram('Ramen boven open doen, te warm binnen. Buiten = '.$buiten_temp.',kamer = '.$s['kamer_temp'].', Tobi = '.$s['tobi_temp'].', Alex = '.$s['alex_temp'],false,2);cset('timeramen',time);}}
	}
	if($s['voordeur']=='On'&&strtotime($t['voordeur'])<time-598)sw($i['voordeur'],'Off','Voordeur uit');
	$nodes=json_decode(curl('http://127.0.0.1:8084/json.htm?type=openzwavenodes&idx=3'),true);
	if($nodes['NodesQueried']==1){
		$timehealnetwork=cget('healnetwork');
		if($timehealnetwork<time-3600*24*7){$result=json_decode(curl('http://127.0.0.1:8084/json.htm?type=command&param=zwavenetworkheal&idx=3'),true);if($result['status']=="OK"){cset('healnetwork',time);exit;}}
		$kamers=array('living','tobi','alex','kamer');
		foreach($kamers as $kamer)${'dif'.$kamer}=number_format($s[$kamer.'_temp']-$s[$kamer.'_set'],1);
		foreach($nodes['result'] as $node){
			if(in_array($node['NodeID'],array(2,3,4,5,6,7,8,9,10,11,12,13,14,15,17,18,19,20,22,23,25,26,27,29))){if($timehealnetwork<time-1800&&cget('timehealnode-'.$node['Name'])<time-3600*24*7&&cget('timehealnode')<time-300){$healnode=json_decode(curl('http://127.0.0.1:8084/json.htm?type=command&param=zwavenodeheal&idx=3&node='.$node['NodeID']),true);if($healnode['status']=="OK"){lg('     Heal Node '.$node['Name'].' started');cset('timehealnode-'.$node['Name'],time);cset('timehealnode',time);exit;}unset($healnode);}}
			if($node['Product_name']=='Z Thermostat 014G0013'){if(is_array($node['config'])){$confs=$node['config'];foreach($confs as $conf){if($conf['label']=='Wake-up Interval'){
				if($node['Name']=='LivingZ'){$Uwake=1800;if(time>=strtotime('17:00'))$Uwake=480;if($difliving<1)$Uwake=240;if($conf['value']!=$Uwake&&time>cget('time-UwakeLivingZ')){$result=json_decode(curl('http://192.168.2.10:8084/json.htm?type=command&param=applyzwavenodeconfig&idx='.$node['idx'].'&valuelist=2000_'.base64_encode($Uwake).'_3001_VW5wcm90ZWN0ZWQ=_'),true);if($result['status']=='OK')cset('time-UwakeLivingZ',time+$conf['value']);lg(' Update Wakeupinterval for '.$node['Name'].' from '.$conf['value'].' to '.$Uwake.'. difliving='.$difliving);}}
				elseif($node['Name']=='LivingZE'){$Uwake=1800;if(time>=strtotime('17:00'))$Uwake=480;if($difliving<1)$Uwake=240;if($conf['value']!=$Uwake&&time>cget('time-UwakeLivingZE')){$result=json_decode(curl('http://192.168.2.10:8084/json.htm?type=command&param=applyzwavenodeconfig&idx='.$node['idx'].'&valuelist=2000_'.base64_encode($Uwake).'_3001_VW5wcm90ZWN0ZWQ=_'),true);if($result['status']=='OK')cset('time-UwakeLivingZE',time+$conf['value']);lg(' Update Wakeupinterval for '.$node['Name'].' from '.$conf['value'].' to '.$Uwake.'. difliving='.$difliving);}}
				elseif($node['Name']=='LivingZZ'){$Uwake=1800;if(time>=strtotime('17:00'))$Uwake=480;if($difliving<1)$Uwake=240;if($conf['value']!=$Uwake&&time>cget('time-UwakeLivingZZ')){$result=json_decode(curl('http://192.168.2.10:8084/json.htm?type=command&param=applyzwavenodeconfig&idx='.$node['idx'].'&valuelist=2000_'.base64_encode($Uwake).'_3001_VW5wcm90ZWN0ZWQ=_'),true);if($result['status']=='OK')cset('time-UwakeLivingZZ',time+$conf['value']);lg(' Update Wakeupinterval for '.$node['Name'].' from '.$conf['value'].' to '.$Uwake.'. difliving='.$difliving);}}
				elseif($node['Name']=="KamerZ"){$Uwake=1800;if(time<strtotime('5:00')||time>strtotime('20:00'))$Uwake=600;if($difkamer<1)$Uwake=300;if($conf['value']!=$Uwake&&time>cget('time-UwakeKamerZ')){$result=json_decode(curl('http://192.168.2.10:8084/json.htm?type=command&param=applyzwavenodeconfig&idx='.$node['idx'].'&valuelist=2000_'.base64_encode($Uwake).'_3001_VW5wcm90ZWN0ZWQ=_'),true);if($result['status']=='OK')cset('time-UwakeKamerZ',time+$conf['value']);lg(' Update Wakeupinterval for '.$node['Name'].' from '.$conf['value'].' to '.$Uwake.'. difkamer='.$difkamer);}}
				elseif($node['Name']=="TobiZ"){$Uwake=1800;if($s['heating']=='On'){if(date('W')%2==1){if(date('N')==3){if(time>strtotime('20:00'))$Uwake=600;}elseif(date('N')==4){if(time<strtotime('5:00')||time>strtotime('20:00'))$Uwake=600;}elseif(date('N')==5){if(time<strtotime('5:00'))$Uwake=600;}}else{if(date('N')==3){if(time>strtotime('20:00'))$Uwake=600;}elseif(in_array(date('N'),array(4,5,6))){if(time<strtotime('5:00')||time>strtotime('20:00'))$Uwake=600;}elseif(date('N')==7){if(time<strtotime('5:00'))$Uwake=600;}}}if($diftobi<1)$Uwake=240;if($conf['value']!=$Uwake&&time>cget('time-UwakeTobiZ')){$result=json_decode(curl('http://192.168.2.10:8084/json.htm?type=command&param=applyzwavenodeconfig&idx='.$node['idx'].'&valuelist=2000_'.base64_encode($Uwake).'_3001_VW5wcm90ZWN0ZWQ=_'),true);if($result['status']=='OK')cset('time-UwakeTobiZ',time+$conf['value']);lg(' Update Wakeupinterval for '.$node['Name'].' from '.$conf['value'].' to '.$Uwake.'. diftobi='.$diftobi);}}
				elseif($node['Name']=="AlexZ"){$Uwake=1800;if(time<strtotime('5:00')||time>strtotime('18:00'))$Uwake=600;if($difalex<1)$Uwake=240;if($conf['value']!=$Uwake&&time>cget('time-UwakeAlexZ')){$result=json_decode(curl('http://192.168.2.10:8084/json.htm?type=command&param=applyzwavenodeconfig&idx='.$node['idx'].'&valuelist=2000_'.base64_encode($Uwake).'_3001_VW5wcm90ZWN0ZWQ=_'),true);if($result['status']=='OK')cset('time-UwakeAlexZ',time+$conf['value']);lg(' Update Wakeupinterval for '.$node['Name'].' from '.$conf['value'].' to '.$Uwake.'. difalex='.$difalex);}}
			}
			unset($confs,$conf);}}}
		}
	}else cset('healnetwork',0);
	checkport('192.168.2.11',80);checkport('192.168.2.12',80);checkport('192.168.2.13',80);checkport('192.168.2.2',53);checkport('192.168.2.2',80);
}

if(cget('cron600')<time-599){
	cset('cron600',time);
	//if($s['water']=='On'&&strtotime($t['water']<time-3598))sw($i['water'],'Off');
	if($s['lichten_auto']=='Off')if(strtotime($t['lichten_auto'])<time-10795)sw($i['lichten_auto'],'Off','lichten_auto aan');
	if($s['meldingen']=='Off'&&strtotime($t['meldingen'])<time-10795)sw($i['meldingen'],'On','meldingen');
	if(strtotime($t['pirliving'])<time-14395&&strtotime($t['pirlivingR'])<time-14395&&strtotime($t['pirgarage'])<time-14395&&strtotime($t['pirinkom'])<time-14395&&strtotime($t['pirhall'])<time-14395&&strtotime($t['slapen'])<time-14395&&strtotime($t['weg'])<time-14395&&$s['weg']=='Off'&&$s['slapen']=="Off"){sw($i['slapen'],'On');telegram('slapen ingeschakeld na 4 uur geen beweging',false,2);}
	if(strtotime($t['pirliving'])<time-43190&&strtotime($t['pirlivingR'])<time-43190&&strtotime($t['pirgarage'])<time-43190&&strtotime($t['pirinkom'])<time-43190&&strtotime($t['pirhall'])<time-43190&&strtotime($t['slapen'])<time-43190&&strtotime($t['weg'])<time-43190&&$s['weg']=='Off'&&$s['slapen']=="On"){sw($i['slapen'],'Off');sw($i['weg'],'On','weg');telegram('weg ingeschakeld na 12 uur geen beweging',false,2);}
	if($s['weg']=='On'||$s['slapen']=='On'){
		if(strtotime($t['weg'])>time-59||strtotime($t['slapen'])>time-59)$uit=60;else $uit=900;
		if($s['weg']=='On')alles('Off',$uit);
		if($s['slapen']=='On')alles('Slapen',$uit);
		$items=array('living','badkamer','kamer','tobi','alex');foreach($items as $item){${'setpoint'.$item}=cget('setpoint'.$item);if(${'setpoint'.$item}!=0&&strtotime($t[$item])<time-3598)cset('setpoint'.$item,0);}
		$items=array('tobi','living','kamer','alex');foreach($items as $item)if(strtotime($t[$item.'_set'])<time-86398)ud($i[$item.'_set'],0,$s[$item.'_set'],'Update '.$item);
		if(strtotime($t['weg'])<time-57)if($s['poortrf']=='On')sw($i['poortrf'],'Off','Poort uit');
	}
	$items=array(5=>'keukenzolderg',6=>'wasbakkookplaat',7=>'werkbladtuin',8=>'inkomvoordeur',11=>'badkamer');
	foreach($items as $item => $name)if(cget('refresh'.$item)<time-7198){RefreshZwave($item,'time',$name);break;}
}
//if($s['zwembadfilter']=='On'){if(strtotime($t['zwembadfilter']) < time-14395&&time>strtotime("18:00")&&$s['zwembadwarmte']=='Off')sw($i['zwembadfilter'],'Off','zwembadfilter');}else{if(strtotime($t['zwembadfilter'])<time-14395&&time>strtotime("12:00")&&time<strtotime("16:00"))sw($i['zwembadfilter'],'On','zwembadfilter');}if($s['zwembadwarmte']=='On'){if(strtotime($t['zwembadwarmte'])<time-86398)sw($i['zwembadwarmte'],'Off','warmtepomp zwembad');if($s['zwembadfilter']=='Off')sw($i['zwembadfilter'],'On','zwembadfilter');}
/*$wind=$weer['wind'];
	$maxbuien=20;$maxwolken=80;$zonopen=1500;$zontoe=200;
	if(in_array($weer['wind_dir'],array('W','S','SE')))$maxwind=6;
	else $maxwind=8;
	if($s['luifel']!='Open'&&($wind>=$maxwind||$buienradar>=$maxbuien||$s['zon']<$zontoe)){
		lg('  --- Luifel: Wind='.$wind.'|Buien='.round($buienradar,0).'|Zon='.$s['zon'].'|Luifel='.$s['luifel'].'|Last='.$t['luifel']);
		if($wind>=$maxwind){sw($i['luifel'],'Off');if(strtotime($t['luifel'])<time-3598)sw($i['luifel'],'Off');}
		elseif($buienradar>=$maxbuien){sw($i['luifel'],'Off');if(strtotime($t['luifel'])<time-3598)sw($i['luifel'],'Off');}
		elseif($s['zon']<$zontoe){sw($i['luifel'],'Off');if(strtotime($t['luifel'])<time-3598)sw($i['luifel'],'Off');}
	}
	elseif($s['luifel']!='Closed'&&time>strtotime('10:25')&&$wind<$maxwind-1&&$buienradar<$maxbuien-1&&$s['living_temp']>22&&$s['zon']>$zonopen&&strtotime($t['luifel'])<time-598){
		lg('  --- Luifel: Wind='.$wind.'|Buien='.round($buienradar,0).'|Zon='.$s['zon'].'|Luifel='.$s['luifel'].'|Last='.$t['luifel']);
		sw($i['luifel'],'On',$msg);
	}
}*/