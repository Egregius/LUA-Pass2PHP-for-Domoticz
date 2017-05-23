<?php
$kamers=array('living','tobi','alex','kamer','frigo');
foreach($kamers as $kamer){
	${$kamer.'_temp'}=apcu_fetch('s'.$kamer.'_temp');
	${$kamer.'_set'}=apcu_fetch('s'.$kamer.'_set');
}
$items=array('livingZ','kamerZ','tobiZ','alexZ');
foreach($items as $item)${$item}=apcu_fetch('s'.$item);
$heating=apcu_fetch('sheating');
$manual=apcu_fetch('sheatingmanual');
$brander=apcu_fetch('sbrander');
$branderac=apcu_fetch('sbranderac');
$buiten_temp=apcu_fetch('buiten_temp');
$maxtemp=apcu_fetch('maxtemp');
$timebrander=time-apcu_fetch('ttbrander');
$timebranderac=time-apcu_fetch('tbranderac');
$licht=apcu_fetch('slichtbadkamer1');

$tobithuis=false;
$Setkamer=4;
$setpointkamer=apcu_fetch('setpointkamer');
if($setpointkamer!=0&&apcu_fetch('tkamer_set')<time-21600){apcu_store('setpointkamer',0);$setpointkamer=0;}
if($setpointkamer!=2){
	if($buiten_temp<10&&$maxtemp<15 /*&&apcu_fetch('sraamkamer')=='Closed'*/ &&apcu_fetch('sheating')=='On'/*&&(apcu_fetch('traamkamer')<time-7198||time>strtotime('19:00'))*/){
		//$Setkamer=8.0;
		if(time<strtotime('5:00'))$Setkamer=16;
		elseif(time>strtotime('22:00'))$Setkamer=16;
		elseif(time>strtotime('21:40'))$Setkamer=15.9;
		elseif(time>strtotime('21:20'))$Setkamer=15.8;
		elseif(time>strtotime('21:00'))$Setkamer=15.7;
		elseif(time>strtotime('20:40'))$Setkamer=15.6;
		elseif(time>strtotime('20:20'))$Setkamer=15.5;
		elseif(time>strtotime('20:00'))$Setkamer=15.4;
		elseif(time>strtotime('19:40'))$Setkamer=15.3;
		elseif(time>strtotime('19:20'))$Setkamer=15.2;
		elseif(time>strtotime('19:00'))$Setkamer=15.1;
	}
	if($kamer_set!=$Setkamer)ud('kamer_set',0,$Setkamer);
}

$Settobi=4;
$setpointtobi=apcu_fetch('setpointtobi');
if($setpointtobi!=0&&apcu_fetch('ttobi_set')<time-21600){apcu_store('setpointtobi',0);$setpointtobi=0;}
if($setpointtobi!=2){
	if($buiten_temp<14&&$maxtemp<15&&apcu_fetch('sraamtobi')=='Closed'&&$heating=='On'&&(apcu_fetch('traamtobi')<time-7198||time>strtotime('19:10'))){
		//$Settobi=8.0;
		if(date('W')%2==1){
			if(date('N')==3){if(time>strtotime('19:10'))$Settobi=16;$tobithuis=true;}
			elseif(date('N')==4){if(time<strtotime('5:00')||time>strtotime('19:10'))$Settobi=16;$tobithuis=true;}
			elseif(date('N')==5){if(time<strtotime('5:00'))$Settobi=16;}
		}else{
			if(date('N')==3){if(time>strtotime('19:10'))$Settobi=16;$tobithuis=true;}
			elseif(in_array(date('N'),array(4,5,6))){if(time<strtotime('5:00')||time>strtotime('19:10'))$Settobi=16;$tobithuis=true;}
			elseif(date('N')==7){if(time<strtotime('5:00'))$Settobi=16;}
		}
		//if(time<strtotime('5:00')||time>strtotime('21:00'))$Settobi=16.0;
	}
	if($tobi_set!=$Settobi){ud('tobi_set',0,$Settobi);$tobi_set=$Settobi;}}

$Setalex=4;
$setpointalex=apcu_fetch('setpointalex');
if($setpointalex!=0&&apcu_fetch('talex_set')<time-21600){apcu_store('setpointalex',0);$setpointalex=0;}
if($setpointalex!=2){
	if($buiten_temp<16&&$maxtemp<15&&apcu_fetch('sraamalex')=='Closed'&&$heating=='On'&&(apcu_fetch('traamalex')<time-1800||time>strtotime('19:00'))){
		//$Setalex=8;
		if(time<strtotime('5:00')||time>strtotime('19:00'))$Setalex=15.0;
	}
	if($alex_set!=$Setalex){ud('alex_set',0,$Setalex);$alex_set=$Setalex;}
}

$Setliving=14;
$setpointliving=apcu_fetch('setpointliving');
if($setpointliving!=0&&apcu_fetch('tliving_set')<time-21600){apcu_store('setpointliving',0);$setpointliving=0;}
if($setpointliving!=2){
	if($buiten_temp<20&&$maxtemp<20&&$heating=='On'&&apcu_fetch('sraamliving')=='Closed'){
		$Setliving=17;
		if(time>=strtotime('5:00')&&time<strtotime('5:30'))$Weg>0?$Setliving=17.0:$Setliving=20.0;
		elseif(time>=strtotime('5:30')&&time<strtotime('6:00'))$Weg>0?$Setliving=17.5:$Setliving=20.0;
		elseif(time>=strtotime('6:00')&&time<strtotime('6:30'))$Weg>0?$Setliving=18.0:$Setliving=20.0;
		elseif(time>=strtotime('6:30')&&time<strtotime('7:00'))$Weg>0?$Setliving=18.5:$Setliving=20.0;
		elseif(time>=strtotime('7:00')&&time<strtotime('8:15'))$Weg>0?$Setliving=19.0:$Setliving=20.0;
		elseif(time>=strtotime('8:15')&&time<strtotime('18:45'))$Weg>0?$Setliving=19.5:$Setliving=20.4;
		if($Setliving==20.4){
			$Setliving=$living_set;
			if(time>=strtotime('11:00')&&$zon>2000)$Setliving=19;
			elseif($zon<1000)$Setliving=20.4;
		}
	}
	if($living_set!=$Setliving){print 'setpointliving'.PHP_EOL;ud('living_set',0,$Setliving);$living_set=$Setliving;}
}

$kamers=array('living','kamer','tobi','alex');
$bigdif=100;

foreach($kamers as $kamer){
	${'dif'.$kamer}=number_format(${$kamer.'_temp'}-${$kamer.'_set'},1);
	if(${'dif'.$kamer}>9.9)${'dif'.$kamer}=9.9;
	if(${'dif'.$kamer}<$bigdif)$bigdif=${'dif'.$kamer};
	${'Set'.$kamer}=${$kamer.'_set'};
}
foreach($kamers as $kamer){
	if(${'dif'.$kamer}<=number_format(($bigdif+ 0.2),1)&&${'dif'.$kamer}<=0.2)${'RSet'.$kamer}=setradiator($kamer,${'dif'.$kamer},true,${$kamer.'_set'});
	else ${'RSet'.$kamer}=setradiator($kamer,${'dif'.$kamer},false,${$kamer.'_set'});
	if(time>=strtotime('16:00')&&${'RSet'.$kamer}<16&&apcu_fetch('raam'.$kamer)!='Open'){
		if($kamer!='tobi')${'RSet'.$kamer}=16.0;
		elseif($kamer=='tobi'&&$tobithuis)${'RSet'.$kamer}=16.0;
	}
	if(round(${$kamer.'Z'},1)!=round(${'RSet'.$kamer},1)/*&&apcu_fetch('tset'.$kamer.'Z')<time-300*/){
		apcu_store('tset'.$kamer.'Z',time);
		print strftime("%Y-%m-%d %H:%M:%S",time()).'   => Danfoss KamerZ was '.${$kamer.'Z'}.',nieuw='.${'RSet'.$kamer}.PHP_EOL;
		ud($kamer.'Z',0,${'RSet'.$kamer});
	}
}

if((($bigdif<=0.1&&$timebranderac>600)||$licht=='On')&&$branderac!='On'){sw('branderac','On');sleep(2);}
elseif($bigdif>0.2&&$licht=='Off'&&$branderac!='Off'&&$timebranderac>600){sw('branderac','Off');}

if($bigdif<=-0.6&&$brander=="Off"&&$timebrander>60)sw('brander','On', 'brander1 dif = '.$bigdif.', was off for '.convertToHours($timebrander));
elseif($bigdif<=-0.5&&$brander=="Off"&&$timebrander>80)sw('brander','On', 'brander2 dif = '.$bigdif.', was off for '.convertToHours($timebrander));
elseif($bigdif<=-0.4&&$brander=="Off"&&$timebrander>100)sw('brander','On', 'brander3 dif = '.$bigdif.', was off for '.convertToHours($timebrander));
elseif($bigdif<=-0.3&&$brander=="Off"&&$timebrander>140)sw('brander','On', 'brander4 dif = '.$bigdif.', was off for '.convertToHours($timebrander));
elseif($bigdif<=-0.2&&$brander=="Off"&&$timebrander>180)sw('brander','On', 'brander5 dif = '.$bigdif.', was off for '.convertToHours($timebrander));
elseif($bigdif<=-0.1&&$brander=="Off"&&$timebrander>220)sw('brander','On', 'brander6 dif = '.$bigdif.', was off for '.convertToHours($timebrander));
elseif($bigdif<=0	&&$brander=="Off"&&$timebrander>1200)sw('brander','On', 'brander7 dif = '.$bigdif.', was off for '.convertToHours($timebrander));
elseif($bigdif>0	&&$brander=="On" &&$timebrander>90)sw('brander','Off','brander8 dif = '.$bigdif.', was on for '.convertToHours($timebrander));
elseif($bigdif>=0	&&$brander=="On"&&$timebrander>150)sw('brander','Off','brander9 dif = '.$bigdif.', was on for '.convertToHours($timebrander));
elseif($bigdif>=-0.1&&$brander=="On"&&$timebrander>240)sw('brander','Off','brander10 dif = '.$bigdif.', was on for '.convertToHours($timebrander));
elseif($bigdif>=-0.2&&$brander=="On"&&$timebrander>300)sw('brander','Off','brander11 dif = '.$bigdif.', was on for '.convertToHours($timebrander));
elseif($bigdif>=-0.3&&$brander=="On"&&$timebrander>360)sw('brander','Off','brander12 dif = '.$bigdif.', was on for '.convertToHours($timebrander));
elseif($bigdif>=-0.4&&$brander=="On"&&$timebrander>420)sw('brander','Off','brander13 dif = '.$bigdif.', was on for '.convertToHours($timebrander));
elseif($bigdif>=-0.5&&$brander=="On"&&$timebrander>480)sw('brander','Off','brander14 dif = '.$bigdif.', was on for '.convertToHours($timebrander));
elseif($bigdif>=-0.6&&$brander=="On"&&$timebrander>600)sw('brander','Off','brander15 dif = '.$bigdif.', was on for '.convertToHours($timebrander));

if($manual=='On'){
	if($Weg==2){
		if($heating=='On'&&apcu_fetch('theating')<time-3598)sw('heating','Off');
	}else{
		if($heating!='On')sw('heating','On');
	}
}

function setradiator($name,$dif,$koudst=false,$set){
	if($koudst==true)$setpoint=28.0;
	else $setpoint=$set-ceil($dif*4);
	if($setpoint>28)$setpoint=28.0;elseif($setpoint<4)$setpoint=4.0;
	return round($setpoint,0).".0";
}

$wantedbadkamer=21.8;
$badkamer_set=apcu_fetch('sbadkamer_set');
if(!isset($deurbadkamer))$deurbadkamer=apcu_fetch('sdeurbadkamer');
$timebadkvuur=time-apcu_fetch('ttbadkamervuur');

if($deurbadkamer=="Open"&&$badkamer_set!=10&&(apcu_fetch('tdeurbadkamer')<time-57||$licht=='Off')){
	ud('badkamer_set',0,10);
	$badkamer_set=10.0;
}elseif($deurbadkamer!="Open"){
	if($buiten_temp<21&&$licht=='On'&&apcu_fetch('sbadkamer_set')!=$wantedbadkamer&&((time>strtotime('5:00')&&time<strtotime('10:00'))||$timebadkvuur<900)){
		ud('badkamer_set',0,$wantedbadkamer);
		$badkamer_set=$wantedbadkamer;
	}elseif($licht=='Off'&&$badkamer_set!=10){
		ud('badkamer_set',0,10);
		$badkamer_set=10.0;
	}
}

$difbadkamer=number_format(apcu_fetch('sbadkamer_temp')-$badkamer_set,1);
$sbadkamervuur=apcu_fetch('sbadkamervuur');

	if($difbadkamer<=-0.2&&$sbadkamervuur=="Off"&&$timebadkvuur>180)double('badkamervuur','On','badkamervuur1 dif = '.$difbadkamer.' was off for '.convertToHours($timebadkvuur));
elseif($difbadkamer<=-0.1&&$sbadkamervuur=="Off"&&$timebadkvuur>240)double('badkamervuur','On','badkamervuur2 dif = '.$difbadkamer.' was off for '.convertToHours($timebadkvuur));
elseif($difbadkamer<= 0  &&$sbadkamervuur=="Off"&&$timebadkvuur>360)double('badkamervuur','On','badkamervuur3 dif = '.$difbadkamer.' was off for '.convertToHours($timebadkvuur));
elseif($difbadkamer>= 0  &&$sbadkamervuur=="On"&&$timebadkvuur>30)	double('badkamervuur','Off','badkamervuur4 dif = '.$difbadkamer.' was on for '.convertToHours($timebadkvuur));
elseif($difbadkamer>=-0.2&&$sbadkamervuur=="On"&&$timebadkvuur>120)	double('badkamervuur','Off','badkamervuur5 dif = '.$difbadkamer.' was on for '.convertToHours($timebadkvuur));
elseif($difbadkamer>=-0.4&&$sbadkamervuur=="On"&&$timebadkvuur>180)	double('badkamervuur','Off','badkamervuur6 dif = '.$difbadkamer.' was on for '.convertToHours($timebadkvuur));
