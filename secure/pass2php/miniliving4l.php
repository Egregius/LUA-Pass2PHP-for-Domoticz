<?php
$items=array('eettafel','zithoek','garage','inkom','hall','keuken','werkblad','wasbak','kookplaat');
foreach($items as $item)
	if($s[$item]!='Off')
		sw(apcu_fetch('i'.$item),'Off',$item);