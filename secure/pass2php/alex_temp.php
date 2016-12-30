<?php
$room='alex';
$prev=apcu_fetch('s'.$room.'_temp');
$set=apcu_fetch('s'.$room.'_set');
$tbrander=apcu_fetch('tbrander');
if($status>$prev&&$status>$set&&$tbrander<time-600)
	sw(apcu_fetch('ibrander'),'Off','Brander door '.$room.' prev='.$prev.', new='.$status);
elseif($status<$prev&&$status<$set&&$tbrander<time-600)
	sw(apcu_fetch('ibrander'),'Off','Brander door '.$room.' prev='.$prev.', new='.$status);
