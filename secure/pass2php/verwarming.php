<?php
if($s['weg']=='On'){
	if($s['heating']!='Off'&&apcu_fetch('theating')<time-3598){
		sw(apcu_fetch('iheating'),'Off','heating');
		$s['heating']='Off';
	}
}else{
	if($s['heating']!='On'){
		sw(apcu_fetch('iheating'),'On','heating');
		$s['heating']='On';
	}
}
$buiten_temp=apcu_fetch('buiten_temp');
$Setkamer=6;$setpointkamer=apcu_fetch('setpointkamer');if($setpointkamer!=0&&apcu_fetch('tkamer_set')<time-3598){apcu_store('setpointkamer',0);$setpointkamer=0;}if($setpointkamer!=2){if($buiten_temp<14&&$s['raamkamer']=='Closed'&&$s['heating']=='On'&&(apcu_fetch('traamkamer')<time-7198||time>strtotime('21:00'))){$Setkamer=12.0;if(time<strtotime('5:00')||time>strtotime('21:00'))$Setkamer=16;}if($s['kamer_set']!=$Setkamer){ud(apcu_fetch('ikamer_set'),0,$Setkamer,'Rkamer_set');$s['kamer_set']=$Setkamer;}}
$Settobi=6;$setpointtobi=apcu_fetch('setpointtobi');if($setpointtobi!=0&&apcu_fetch('ttobi_set')<time-3598){apcu_store('setpointtobi',0);$setpointtobi=0;}if($setpointtobi!=2){if($buiten_temp<14&&$s['raamtobi']=='Closed'&&$s['heating']=='On'&&(apcu_fetch('traamtobi')<time-7198||time>strtotime('21:00'))){$Settobi=12.0;if(date('W')%2==1){if(date('N')==3){if(time>strtotime('21:00'))$Settobi=16;}elseif(date('N')==4){if(time<strtotime('5:00')||time>strtotime('21:00'))$Settobi=16;}elseif(date('N')==5){if(time<strtotime('5:00'))$Settobi=16;}}else{if(date('N')==3){if(time>strtotime('21:00'))$Settobi=16;}elseif(in_array(date('N'),array(4,5,6))){if(time<strtotime('5:00')||time>strtotime('21:00'))$Settobi=16;}elseif(date('N')==7){if(time<strtotime('5:00'))$Settobi=16;}}}if(isset($s['tobi_set'])&&$s['tobi_set']!=$Settobi){ud(apcu_fetch('itobi_set'),0,$Settobi,'Rtobi_set');$s['tobi_set']=$Settobi;}}
$Setalex=6;$setpointalex=apcu_fetch('setpointalex');if($setpointalex!=0&&apcu_fetch('talex_set')<time-28795){apcu_store('setpointalex',0);$setpointalex=0;}if($setpointalex!=2){if($buiten_temp<16&&$s['raamalex']=='Closed'&&$s['heating']=='On'&&(apcu_fetch('traamalex')<time-1800||time>strtotime('19:00'))){$Setalex=12;if(time<strtotime('5:00')||time>strtotime('19:00'))$Setalex=16.0;}if($s['alex_set']!=$Setalex){ud(apcu_fetch('ialex_set'),0,$Setalex,'Ralex_set');$s['alex_set']=$Setalex;}}
$Setliving=14;$setpointliving=apcu_fetch('setpointliving');if($setpointliving!=0&&apcu_fetch('tliving_set')<time-10795){apcu_store('setpointliving',0);$setpointliving=0;}if($setpointliving!=2){if($buiten_temp<20&&$s['heating']=='On'&&$s['raamliving']=='Closed'){$Setliving=17;if(time>=strtotime('5:00')&&time<strtotime('8:15'))$s['slapen']=='On'?$Setliving=17.0:$Setliving=20.0;elseif(time>=strtotime('8:15')&&time<strtotime('19:55'))$s['slapen']=='On'?$Setliving=19.0:$Setliving=20.5;}if($s['living_set']!=$Setliving){ud(apcu_fetch('iliving_set'),0,$Setliving,'Rliving_set');$s['living_set']=$Setliving;}}

$kamers=array('living','tobi','alex','kamer');
$bigdif=100;
$timebrander=time-apcu_fetch('tbrander');
foreach($kamers as $kamer){
	${'dif'.$kamer}=number_format($s[$kamer.'_temp']-$s[$kamer.'_set'],1);if(${'dif'.$kamer}>9.9)${'dif'.$kamer}=9.9;if(${'dif'.$kamer}<$bigdif&&$kamer!='kamer')$bigdif=${'dif'.$kamer};${'Set'.$kamer}=$s[$kamer.'_set'];
}
foreach($kamers as $kamer){
	if(${'dif'.$kamer}<=number_format(($bigdif+ 0.2),1)&&${'dif'.$kamer}<2)${'RSet'.$kamer}=setradiator($kamer,${'dif'.$kamer},true,$s[$kamer.'_set']);else ${'RSet'.$kamer}=setradiator($kamer,${'dif'.$kamer},false,$s[$kamer.'_set']);
}

if(round($s['kamerZ'],1)!=round($RSetkamer,1)){lg('Danfoss KamerZ was '.$s['kamerZ'].',nieuw='.$RSetkamer);ud(apcu_fetch('ikamerZ'),0,$RSetkamer,'RkamerZ');}
if(round($s['tobiZ'],1)!=round($RSettobi,1)){lg('Danfoss tobiZ was '.$s['tobiZ'].',nieuw='.$RSettobi);ud(apcu_fetch('itobiZ'),0,$RSettobi,'RtobiZ');}
if(round($s['alexZ'],1)!=round($RSetalex,1)){lg('Danfoss alexZ was '.$s['alexZ'].',nieuw='.$RSetalex);ud(apcu_fetch('ialexZ'),0,$RSetalex,'RalexZ');}
if(round($s['livingZ'],1)!=round($RSetliving,1)){lg('Danfoss livingZ was '.$s['livingZ'].',nieuw='.$RSetliving);ud(apcu_fetch('ilivingZ'), 0,$RSetliving,'RlivingZ');}
if(round($s['livingZZ'],1)!=round($RSetliving,1)){lg('Danfoss livingZZ was '.$s['livingZZ'].',nieuw='.$RSetliving);ud(apcu_fetch('ilivingZZ'),0,$RSetliving,'RlivingZZ');}
if(round($s['livingZE'],1)!=round($RSetliving,1)){lg('Danfoss livingZE was '.$s['kamerZ'].',nieuw='.$RSetliving);ud(apcu_fetch('ilivingZE'),0,$RSetliving,'RlivingZE');}

if($bigdif<=-0.6&&$s['brander']=="Off"&&$timebrander>60)sw(apcu_fetch('ibrander'),'On', 'brander1 dif = '.$bigdif.', was off for '.convertToHours($timebrander));
elseif($bigdif<=-0.5&&$s['brander']=="Off"&&$timebrander>120)sw(apcu_fetch('ibrander'),'On', 'brander2 dif = '.$bigdif.', was off for '.convertToHours($timebrander));
elseif($bigdif<=-0.4&&$s['brander']=="Off"&&$timebrander>180)sw(apcu_fetch('ibrander'),'On', 'brander3 dif = '.$bigdif.', was off for '.convertToHours($timebrander));
elseif($bigdif<=-0.3&&$s['brander']=="Off"&&$timebrander>300)sw(apcu_fetch('ibrander'),'On', 'brander4 dif = '.$bigdif.', was off for '.convertToHours($timebrander));
elseif($bigdif<=-0.2&&$s['brander']=="Off"&&$timebrander>450)sw(apcu_fetch('ibrander'),'On', 'brander5 dif = '.$bigdif.', was off for '.convertToHours($timebrander));
elseif($bigdif<=-0.1&&$s['brander']=="Off"&&$timebrander>600)sw(apcu_fetch('ibrander'),'On', 'brander6 dif = '.$bigdif.', was off for '.convertToHours($timebrander));
elseif($bigdif<=0	&&$s['brander']=="Off"&&$timebrander>2400)sw(apcu_fetch('ibrander'),'On', 'brander7 dif = '.$bigdif.', was off for '.convertToHours($timebrander));
elseif($bigdif>0	&&$s['brander']=="On" &&$timebrander>30)sw(apcu_fetch('ibrander'),'Off','brander8 dif = '.$bigdif.', was on for '.convertToHours($timebrander));
elseif($bigdif>=0	&&$s['brander']=="On"&&$timebrander>120)sw(apcu_fetch('ibrander'),'Off','brander9 dif = '.$bigdif.', was on for '.convertToHours($timebrander));
elseif($bigdif>=-0.1&&$s['brander']=="On"&&$timebrander>180)sw(apcu_fetch('ibrander'),'Off','brander10 dif = '.$bigdif.', was on for '.convertToHours($timebrander));
elseif($bigdif>=-0.2&&$s['brander']=="On"&&$timebrander>240)sw(apcu_fetch('ibrander'),'Off','brander11 dif = '.$bigdif.', was on for '.convertToHours($timebrander));
elseif($bigdif>=-0.3&&$s['brander']=="On"&&$timebrander>300)sw(apcu_fetch('ibrander'),'Off','brander12 dif = '.$bigdif.', was on for '.convertToHours($timebrander));
elseif($bigdif>=-0.4&&$s['brander']=="On"&&$timebrander>360)sw(apcu_fetch('ibrander'),'Off','brander13 dif = '.$bigdif.', was on for '.convertToHours($timebrander));
elseif($bigdif>=-0.5&&$s['brander']=="On"&&$timebrander>420)sw(apcu_fetch('ibrander'),'Off','brander14 dif = '.$bigdif.', was on for '.convertToHours($timebrander));
elseif($bigdif>=-0.6&&$s['brander']=="On"&&$timebrander>900)sw(apcu_fetch('ibrander'),'Off','brander15 dif = '.$bigdif.', was on for '.convertToHours($timebrander));
