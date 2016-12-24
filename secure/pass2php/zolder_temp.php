<?php
$room='zolder';
$prev=apcu_fetch($room.'_temp');
if($s[$room.'_temp']!=$prev)apcu_store($room.'_temp',$s[$room.'_temp']);