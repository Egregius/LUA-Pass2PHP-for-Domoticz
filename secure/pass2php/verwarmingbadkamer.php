<?php
if($s['deurbadkamer']=="Open"&&$s['badkamer_set']!=12&&(apcu_fetch('tdeurbadkamer')<time-57||$s['lichtbadkamer']=='Off')){
	ud(apcu_fetch('ibadkamer_set'),0,12,'badkamer_set 12 deur open');
	$s['badkamer_set']=12.0;
}elseif($s['deurbadkamer']=="Closed"&&$s['heating']=='On'){
	if(($s['lichtbadkamer']=='On'||$s['lichtbadkamer1']=='On'||$s['lichtbadkamer2']=='On')&&$s['badkamer_set']!=22.5){
		ud(apcu_fetch('ibadkamer_set'),0,22.5,'badkamer_set 22.5 deur dicht en licht aan');
		$s['badkamer_set']=22.5;
	}elseif($s['lichtbadkamer']=='Off'&&$s['lichtbadkamer1']=='Off'&&$s['lichtbadkamer2']=='Off'&&$s['badkamer_set']!=15){
		ud(apcu_fetch('ibadkamer_set'),0,15,'badkamer_set 15 deur dicht en licht uit');
		$s['badkamer_set']=15.0;
	}
}
$difbadkamer=number_format($s['badkamer_temp']-$s['badkamer_set'],1);
$timebadkvuur=time-apcu_fetch('tbadkamervuur');
if($difbadkamer<=-0.2&&$s['badkamervuur']=="Off"&&$timebadkvuur>180)
	double(apcu_fetch('ibadkamervuur'),'On','badkamervuur1 dif = '.$difbadkamer.' was off for '.convertToHours($timebadkvuur));
elseif($difbadkamer<=-0.1&&$s['badkamervuur']=="Off"&&$timebadkvuur>240)
	double(apcu_fetch('ibadkamervuur'),'On','badkamervuur2 dif = '.$difbadkamer.' was off for '.convertToHours($timebadkvuur));
elseif($difbadkamer<=0&&$s['badkamervuur']=="Off"&&$timebadkvuur>360)
	double(apcu_fetch('ibadkamervuur'),'On','badkamervuur3 dif = '.$difbadkamer.' was off for '.convertToHours($timebadkvuur));
elseif($difbadkamer>=0&&$s['badkamervuur']=="On"&&$timebadkvuur>30)
	double(apcu_fetch('ibadkamervuur'),'Off','badkamervuur4 dif = '.$difbadkamer.' was on for '.convertToHours($timebadkvuur));
elseif($difbadkamer>=-0.2&&$s['badkamervuur']=="On"&&$timebadkvuur>120)
	double(apcu_fetch('ibadkamervuur'),'Off','badkamervuur5 dif = '.$difbadkamer.' was on for '.convertToHours($timebadkvuur));
elseif($difbadkamer>=-0.4&&$s['badkamervuur']=="On"&&$timebadkvuur>180)
	double(apcu_fetch('ibadkamervuur'),'Off','badkamervuur6 dif = '.$difbadkamer.' was on for '.convertToHours($timebadkvuur));
$prev=apcu_fetch('badkamer_temp');
if($s['badkamer_temp']>$prev&&$s['badkamer_temp']>$s['badkamer_set']&&apcu_fetch('tbadkamervuur')<time-600)
	sw(apcu_fetch('ibadkamervuur'),'Off','badkamervuur door '.'badkamer_temp'.' prev='.$prev.', new='.$s['badkamer_temp']);
