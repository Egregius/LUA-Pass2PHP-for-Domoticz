<?php
$room='tobi';
$prev=status(''.$room.'_temp');
$set=status(''.$room.'_set');
$tbrander=timestamp('brander');
if($status>$prev&&$status>$set&&$tbrander<(time-900)){sw('brander','Off','door '.$room.' prev='.$prev.', new='.$status);}
elseif($status<$prev&&$status<$set&&$tbrander<(time-600)){sw('brander','On','door '.$room.' prev='.$prev.', new='.$status);}
