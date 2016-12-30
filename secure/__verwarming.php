<?php
$kamers=array('living','tobi','alex','kamer');
foreach($kamers as $kamer){
	${$kamer.'_temp'}=apcu_fetch('s'.$kamer.'_temp');
	${$kamer.'_set'}=apcu_fetch('s'.$kamer.'_set');
}
$items=array('livingZ','livingZE','livingZZ','kamerZ','tobiZ','alexZ');
foreach($items as $item)${$item}=apcu_fetch('s'.$item);
$heating=apcu_fetch('sheating');
$weg=apcu_fetch('sweg');
$slapen=apcu_fetch('sslapen');
$brander=apcu_fetch('sbrander');
$buiten_temp=apcu_fetch('buiten_temp');

if($weg=='On'){
	if($heating=='On'&&apcu_fetch('theating')<time-3598){
		sw(apcu_fetch('iheating'),'Off','heating');
	}
}else{
	if($heating!='On'){
		sw(apcu_fetch('iheating'),'On','heating');
	}
}

$Setkamer=6;
$setpointkamer=apcu_fetch('setpointkamer');
if($setpointkamer!=0&&apcu_fetch('tkamer_set')<time-3598){apcu_store('setpointkamer',0);$setpointkamer=0;}
if($setpointkamer!=2){
	if($buiten_temp<14&&apcu_fetch('sraamkamer')=='Closed'&&apcu_fetch('sheating')=='On'&&(apcu_fetch('traamkamer')<time-7198||time>strtotime('21:00'))){
		$Setkamer=12.0;
		if(time<strtotime('5:00')||time>strtotime('21:00'))$Setkamer=14;
	}
	if($kamer_set!=$Setkamer){
		ud(apcu_fetch('ikamer_set'),0,$Setkamer,'Rkamer_set');
	}
}

$Settobi=6;
$setpointtobi=apcu_fetch('setpointtobi');
if($setpointtobi!=0&&apcu_fetch('ttobi_set')<time-3598){apcu_store('setpointtobi',0);$setpointtobi=0;}
if($setpointtobi!=2){
	if($buiten_temp<14&&apcu_fetch('sraamtobi')=='Closed'&&$heating=='On'&&(apcu_fetch('traamtobi')<time-7198||time>strtotime('21:00'))){
		$Settobi=12.0;
		if(date('W')%2==1){
			if(date('N')==3){if(time>strtotime('21:00'))$Settobi=14;}
			elseif(date('N')==4){if(time<strtotime('5:00')||time>strtotime('21:00'))$Settobi=14;}
			elseif(date('N')==5){if(time<strtotime('5:00'))$Settobi=14;}
		}else{
			if(date('N')==3){if(time>strtotime('21:00'))$Settobi=14;}
			elseif(in_array(date('N'),array(4,5,6))){if(time<strtotime('5:00')||time>strtotime('21:00'))$Settobi=14;}
			elseif(date('N')==7){if(time<strtotime('5:00'))$Settobi=14;}
		}
	}
	if($tobi_set!=$Settobi){ud(apcu_fetch('itobi_set'),0,$Settobi,'Rtobi_set');$tobi_set=$Settobi;}}

$Setalex=6;
$setpointalex=apcu_fetch('setpointalex');
if($setpointalex!=0&&apcu_fetch('talex_set')<time-28795){apcu_store('setpointalex',0);$setpointalex=0;}
if($setpointalex!=2){
	if($buiten_temp<16&&apcu_fetch('sraamalex')=='Closed'&&$heating=='On'&&(apcu_fetch('traamalex')<time-1800||time>strtotime('19:00'))){
		$Setalex=12;
		if(time<strtotime('5:00')||time>strtotime('19:00'))$Setalex=16.0;
	}
	if($alex_set!=$Setalex){
		ud(apcu_fetch('ialex_set'),0,$Setalex,'Ralex_set');
		$alex_set=$Setalex;
	}
}

$Setliving=14;
$setpointliving=apcu_fetch('setpointliving');
if($setpointliving!=0&&apcu_fetch('tliving_set')<time-10795){apcu_store('setpointliving',0);$setpointliving=0;}
if($setpointliving!=2){
	if($buiten_temp<20&&$heating=='On'&&apcu_fetch('sraamliving')=='Closed'){
		$Setliving=17;
		if(time>=strtotime('5:00')&&time<strtotime('5:30'))$slapen=='On'?$Setliving=17.5:$Setliving=20.0;
		elseif(time>=strtotime('5:30')&&time<strtotime('6:00'))$slapen=='On'?$Setliving=18.0:$Setliving=20.0;
		elseif(time>=strtotime('6:00')&&time<strtotime('6:30'))$slapen=='On'?$Setliving=18.5:$Setliving=20.0;
		elseif(time>=strtotime('6:30')&&time<strtotime('7:00'))$slapen=='On'?$Setliving=19.0:$Setliving=20.0;
		elseif(time>=strtotime('7:00')&&time<strtotime('8:15'))$slapen=='On'?$Setliving=19.5:$Setliving=20.0;
		elseif(time>=strtotime('8:15')&&time<strtotime('19:55'))$slapen=='On'?$Setliving=19.0:$Setliving=20.5;
	}
	if($living_set!=$Setliving){
		ud(apcu_fetch('iliving_set'),0,$Setliving,'Rliving_set');
		$living_set=$Setliving;
	}
}

$kamers=array('living','tobi','alex','kamer');
$bigdif=100;
$timebrander=time-apcu_fetch('tbrander');
foreach($kamers as $kamer){
	${'dif'.$kamer}=number_format(${$kamer.'_temp'}-${$kamer.'_set'},1);
	if(${'dif'.$kamer}>9.9)${'dif'.$kamer}=9.9;
	if(${'dif'.$kamer}<$bigdif&&$kamer!='kamer')$bigdif=${'dif'.$kamer};
	${'Set'.$kamer}=${$kamer.'_set'};
}
foreach($kamers as $kamer){
	if(${'dif'.$kamer}<=number_format(($bigdif+ 0.2),1)&&${'dif'.$kamer}<2)${'RSet'.$kamer}=setradiator($kamer,${'dif'.$kamer},true,${$kamer.'_set'});
	else ${'RSet'.$kamer}=setradiator($kamer,${'dif'.$kamer},false,${$kamer.'_set'});
}

if(round($kamerZ,1)!=round($RSetkamer,1)){lg('Danfoss KamerZ was '.$kamerZ.',nieuw='.$RSetkamer);ud(apcu_fetch('ikamerZ'),0,$RSetkamer,'RkamerZ');}
if(round($tobiZ,1)!=round($RSettobi,1)){lg('Danfoss tobiZ was '.$tobiZ.',nieuw='.$RSettobi);ud(apcu_fetch('itobiZ'),0,$RSettobi,'RtobiZ');}
if(round($alexZ,1)!=round($RSetalex,1)){lg('Danfoss alexZ was '.$alexZ.',nieuw='.$RSetalex);ud(apcu_fetch('ialexZ'),0,$RSetalex,'RalexZ');}
if(round($livingZ,1)!=round($RSetliving,1)){lg('Danfoss livingZ was '.$livingZ.',nieuw='.$RSetliving);ud(apcu_fetch('ilivingZ'), 0,$RSetliving,'RlivingZ');}
if(round($livingZZ,1)!=round($RSetliving,1)){lg('Danfoss livingZZ was '.$livingZZ.',nieuw='.$RSetliving);ud(apcu_fetch('ilivingZZ'),0,$RSetliving,'RlivingZZ');}
if(round($livingZE,1)!=round($RSetliving,1)){lg('Danfoss livingZE was '.$kamerZ.',nieuw='.$RSetliving);ud(apcu_fetch('ilivingZE'),0,$RSetliving,'RlivingZE');}
if($bigdif<=-0.6&&$brander=="Off"&&$timebrander>60)sw(apcu_fetch('ibrander'),'On', 'brander1 dif = '.$bigdif.', was off for '.convertToHours($timebrander));
elseif($bigdif<=-0.5&&$brander=="Off"&&$timebrander>120)sw(apcu_fetch('ibrander'),'On', 'brander2 dif = '.$bigdif.', was off for '.convertToHours($timebrander));
elseif($bigdif<=-0.4&&$brander=="Off"&&$timebrander>180)sw(apcu_fetch('ibrander'),'On', 'brander3 dif = '.$bigdif.', was off for '.convertToHours($timebrander));
elseif($bigdif<=-0.3&&$brander=="Off"&&$timebrander>300)sw(apcu_fetch('ibrander'),'On', 'brander4 dif = '.$bigdif.', was off for '.convertToHours($timebrander));
elseif($bigdif<=-0.2&&$brander=="Off"&&$timebrander>450)sw(apcu_fetch('ibrander'),'On', 'brander5 dif = '.$bigdif.', was off for '.convertToHours($timebrander));
elseif($bigdif<=-0.1&&$brander=="Off"&&$timebrander>600)sw(apcu_fetch('ibrander'),'On', 'brander6 dif = '.$bigdif.', was off for '.convertToHours($timebrander));
elseif($bigdif<=0	&&$brander=="Off"&&$timebrander>2400)sw(apcu_fetch('ibrander'),'On', 'brander7 dif = '.$bigdif.', was off for '.convertToHours($timebrander));
elseif($bigdif>0	&&$brander=="On" &&$timebrander>30)sw(apcu_fetch('ibrander'),'Off','brander8 dif = '.$bigdif.', was on for '.convertToHours($timebrander));
elseif($bigdif>=0	&&$brander=="On"&&$timebrander>120)sw(apcu_fetch('ibrander'),'Off','brander9 dif = '.$bigdif.', was on for '.convertToHours($timebrander));
elseif($bigdif>=-0.1&&$brander=="On"&&$timebrander>180)sw(apcu_fetch('ibrander'),'Off','brander10 dif = '.$bigdif.', was on for '.convertToHours($timebrander));
elseif($bigdif>=-0.2&&$brander=="On"&&$timebrander>240)sw(apcu_fetch('ibrander'),'Off','brander11 dif = '.$bigdif.', was on for '.convertToHours($timebrander));
elseif($bigdif>=-0.3&&$brander=="On"&&$timebrander>300)sw(apcu_fetch('ibrander'),'Off','brander12 dif = '.$bigdif.', was on for '.convertToHours($timebrander));
elseif($bigdif>=-0.4&&$brander=="On"&&$timebrander>360)sw(apcu_fetch('ibrander'),'Off','brander13 dif = '.$bigdif.', was on for '.convertToHours($timebrander));
elseif($bigdif>=-0.5&&$brander=="On"&&$timebrander>420)sw(apcu_fetch('ibrander'),'Off','brander14 dif = '.$bigdif.', was on for '.convertToHours($timebrander));
elseif($bigdif>=-0.6&&$brander=="On"&&$timebrander>900)sw(apcu_fetch('ibrander'),'Off','brander15 dif = '.$bigdif.', was on for '.convertToHours($timebrander));

function setradiator($name,$dif,$koudst=false,$set){
	$setpoint=$set-ceil($dif*4);
	if($koudst==true)$setpoint=28.0;
	if($setpoint>28)$setpoint=28.0;elseif($setpoint<4)$setpoint=4.0;
	return round($setpoint,0).".0";
}