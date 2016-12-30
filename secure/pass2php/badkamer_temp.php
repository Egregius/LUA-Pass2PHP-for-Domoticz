<?php
$room='badkamer';
$prev=apcu_fetch('s'.$room.'_temp');
$set=apcu_fetch('s'.$room.'_set');
$tbrander=apcu_fetch('tbadkamervuur');
if($status>$prev&&$status>$set&&$tbrander<time-600)
	sw(apcu_fetch('ibadkamervuur'),'Off','badkamervuur door '.$room.' prev='.$prev.', new='.$status);
elseif($status<$prev&&$status<$set&&$tbrander<time-600)
	sw(apcu_fetch('ibadkamervuur'),'Off','badkamervuur door '.$room.' prev='.$prev.', new='.$status);
