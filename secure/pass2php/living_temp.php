<?php
$room='living';
$prev=apcu_fetch($room.'_temp');
if($s[$room.'_temp']!=$prev)apcu_store($room.'_temp',$s[$room.'_temp']);
if($s[$room.'_temp']>$prev&&$s[$room.'_temp']>$s[$room.'_set']&&apcu_fetch('tbrander')<time-600)
	sw(apcu_fetch('ibrander'),'Off','Brander door '.$room.' prev='.$prev.', new='.$s['alex_temp']);
elseif($s[$room.'_temp']<$prev&&$s[$room.'_temp']<$s[$room.'_set']&&apcu_fetch('tbrander')<time-600)
	sw(apcu_fetch('ibrander'),'Off','Brander door '.$room.' prev='.$prev.', new='.$s['alex_temp']);
