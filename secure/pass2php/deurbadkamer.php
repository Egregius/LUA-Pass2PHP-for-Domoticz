<?php
if($s['deurbadkamer']=='Open'){
	if($s['slapen']=='Off'||(time>strtotime('6:00')&&time<strtotime('12:00'))){
		if($s['lichtbadkamer1']=='Off')sw(apcu_fetch('ilichtbadkamer1'),'On','lichtbadkamer1');
		if($s['lichtbadkamer2']=='On')sw(apcu_fetch('ilichtbadkamer2'),'Off','lichtbadkamer2');
	}else{
		if($s['lichtbadkamer2']=='Off')sw(apcu_fetch('ilichtbadkamer2'),'On','lichtbadkamer2');
		if($s['lichtbadkamer1']=='On')sw(apcu_fetch('ilichtbadkamer1'),'Off','lichtbadkamer1');
	}
}else include('pass2php/verwarmingbadkamer.php');
