<?php
$room='badkamer';
$prev=apcu_fetch('s'.$room.'_temp');
$set=apcu_fetch('s'.$room.'_set');
$tbadkamervuur=apcu_fetch('tbadkamervuur');
if($status>$prev&&$status>$set&&$tbadkamervuur<time-600)
	sw('badkamervuur','Off',' prev='.$prev.', new='.$status);
elseif($status<$prev&&$status<$set&&$tbadkamervuur<time-600)
	sw('badkamervuur','On',' prev='.$prev.', new='.$status);