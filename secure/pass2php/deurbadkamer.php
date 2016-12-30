<?php
if($status=='Open'){
	if(apcu_fetch('sslapen')=='Off'||(time>strtotime('6:00')&&time<strtotime('12:00'))){
		if(apcu_fetch('slichtbadkamer1')=='Off')sw(apcu_fetch('ilichtbadkamer1'),'On','lichtbadkamer1');
		if(apcu_fetch('slichtbadkamer2')=='On')sw(apcu_fetch('ilichtbadkamer2'),'Off','lichtbadkamer2');
	}else{
		if(apcu_fetch('slichtbadkamer2')=='Off')sw(apcu_fetch('ilichtbadkamer2'),'On','lichtbadkamer2');
		if(apcu_fetch('slichtbadkamer1')=='On')sw(apcu_fetch('ilichtbadkamer1'),'Off','lichtbadkamer1');
	}
	$slichtbadkamer='On';
}else include('__verwarmingbadkamer.php');
