<?php
if(apcu_fetch('slichtbadkamer')=='On'||apcu_fetch('slichtbadkamer1')=='On'||apcu_fetch('slichtbadkamer2')=='On')$licht='On';else $licht='Off';
$badkamer_set=apcu_fetch('sbadkamer_set');
if(apcu_fetch('sdeurbadkamer')=="Open"&&$badkamer_set!=12&&(apcu_fetch('tdeurbadkamer')<time-57||$licht=='Off')){
	ud(apcu_fetch('ibadkamer_set'),0,12,'badkamer_set 12 deur open');
	$badkamer_set=12.0;
}elseif(apcu_fetch('sdeurbadkamer')=="Closed"&&apcu_fetch('sheating')=='On'){
	if($licht=='On'&&apcu_fetch('sbadkamer_set')!=22.5){
		ud(apcu_fetch('ibadkamer_set'),0,22.5,'badkamer_set 22.5 deur dicht en licht aan');
		$badkamer_set=22.5;
	}elseif($licht=='Off'&&$badkamer_set!=15){
		ud(apcu_fetch('ibadkamer_set'),0,15,'badkamer_set 15 deur dicht en licht uit');
		$badkamer_set=15.0;
	}
}
$difbadkamer=number_format(apcu_fetch('sbadkamer_temp')-$badkamer_set,1);
$timebadkvuur=time-apcu_fetch('tbadkamervuur');
$sbadkamervuur=apcu_fetch('sbadkamervuur');
if($difbadkamer<=-0.2&&$sbadkamervuur=="Off"&&$timebadkvuur>180){
	double(apcu_fetch('ibadkamervuur'),'On','badkamervuur1 dif = '.$difbadkamer.' was off for '.convertToHours($timebadkvuur));
	$sbadkamervuur='On';
}
elseif($difbadkamer<=-0.1&&$sbadkamervuur=="Off"&&$timebadkvuur>240){
	double(apcu_fetch('ibadkamervuur'),'On','badkamervuur2 dif = '.$difbadkamer.' was off for '.convertToHours($timebadkvuur));
	$sbadkamervuur='On';
}
elseif($difbadkamer<=0&&$sbadkamervuur=="Off"&&$timebadkvuur>360){
	double(apcu_fetch('ibadkamervuur'),'On','badkamervuur3 dif = '.$difbadkamer.' was off for '.convertToHours($timebadkvuur));
	$sbadkamervuur='On';
}
elseif($difbadkamer>=0&&$sbadkamervuur=="On"&&$timebadkvuur>30){
	double(apcu_fetch('ibadkamervuur'),'Off','badkamervuur4 dif = '.$difbadkamer.' was on for '.convertToHours($timebadkvuur));
	$sbadkamervuur='Off';
}
elseif($difbadkamer>=-0.2&&$sbadkamervuur=="On"&&$timebadkvuur>120){
	double(apcu_fetch('ibadkamervuur'),'Off','badkamervuur5 dif = '.$difbadkamer.' was on for '.convertToHours($timebadkvuur));
	$sbadkamervuur='Off';
}
elseif($difbadkamer>=-0.4&&$sbadkamervuur=="On"&&$timebadkvuur>180){
	double(apcu_fetch('ibadkamervuur'),'Off','badkamervuur6 dif = '.$difbadkamer.' was on for '.convertToHours($timebadkvuur));
	$sbadkamervuur='Off';
}
