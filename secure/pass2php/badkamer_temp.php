<?php
if($s['deurbadkamer']=="Open"){if($s['badkamer_set']!=12&&(strtotime($t['deurbadkamer'])<time-57||$s['lichtbadkamer']=='Off')){ud($i['badkamer_set'],0,12,'badkamer_set 12 deur open');$s['badkamer_set']=12.0;}}
elseif($s['deurbadkamer']=="Closed"&&$s['heating']=='On'){if($s['lichtbadkamer']=='On'&&$s['badkamer_set']!=22.5){ud($i['badkamer_set'],0,22.5,'badkamer_set 22.5 deur dicht en licht aan');$s['badkamer_set']=22.5;}elseif($s['lichtbadkamer']=='Off'&&$s['badkamer_set']!=15){ud($i['badkamer_set'],0,15,'badkamer_set 15 deur dicht en licht uit');$s['badkamer_set']=15.0;}}
$difbadkamer=number_format($s['badkamer_temp']-$s['badkamer_set'],1);$timebadkvuur=time-strtotime($t['badkamervuur']);
if($difbadkamer<=-0.2&&$s['badkamervuur']=="Off"&&$timebadkvuur>180)double($i['badkamervuur'],'On','badkamervuur1 dif = '.$difbadkamer.' was off for '.convertToHours($timebadkvuur));
elseif($difbadkamer<=-0.1&&$s['badkamervuur']=="Off"&&$timebadkvuur>240)double($i['badkamervuur'],'On','badkamervuur2 dif = '.$difbadkamer.' was off for '.convertToHours($timebadkvuur));
elseif($difbadkamer<=0&&$s['badkamervuur']=="Off"&&$timebadkvuur>360)double($i['badkamervuur'],'On','badkamervuur3 dif = '.$difbadkamer.' was off for '.convertToHours($timebadkvuur));
elseif($difbadkamer>=0&&$s['badkamervuur']=="On"&&$timebadkvuur>30)double($i['badkamervuur'],'Off','badkamervuur4 dif = '.$difbadkamer.' was on for '.convertToHours($timebadkvuur));
elseif($difbadkamer>=-0.2&&$s['badkamervuur']=="On"&&$timebadkvuur>120)double($i['badkamervuur'],'Off','badkamervuur5 dif = '.$difbadkamer.' was on for '.convertToHours($timebadkvuur));
elseif($difbadkamer>=-0.4&&$s['badkamervuur']=="On"&&$timebadkvuur>180)double($i['badkamervuur'],'Off','badkamervuur6 dif = '.$difbadkamer.' was on for '.convertToHours($timebadkvuur));
if($s['badkamer_temp']>$weer['badkamer_temp']&&$s['badkamer_temp']>$s[str_replace("_temp","_set",'badkamer_temp')]&&strtotime($t['badkamervuur'])<time-600)sw($i['badkamervuur'],'Off','badkamervuur door '.'badkamer_temp'.' prev='.$weer['badkamer_temp'].', new='.$s['badkamer_temp']);