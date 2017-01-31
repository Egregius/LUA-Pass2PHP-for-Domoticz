<?php
$room='kamer';
$prev=apcu_fetch('s'.$room.'_temp');
$set=apcu_fetch('s'.$room.'_set');
$tbrander=apcu_fetch('tbrander');
if($status>$prev&&$status>$set&&$tbrander<(time-600)){sw('brander','Off','door '.$room.' prev='.$prev.', new='.$status);apcu_store('tbrander',time);}
elseif($status<$prev&&$status<$set&&$tbrander<(time-600)){sw('brander','On','door '.$room.' prev='.$prev.', new='.$status);apcu_store('tbrander',time);}