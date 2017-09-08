<?php
$room='badkamer';
$prev=status(''.$room.'_temp');
$set=status(''.$room.'_set');
$tbadkamervuur=timestamp('badkamervuur');
if($status>$prev&&$status>$set&&$tbadkamervuur<time-600)
	sw('badkamervuur','Off',' prev='.$prev.', new='.$status);
elseif($status<$prev&&$status<$set&&$tbadkamervuur<time-600)
	sw('badkamervuur','On',' prev='.$prev.', new='.$status);