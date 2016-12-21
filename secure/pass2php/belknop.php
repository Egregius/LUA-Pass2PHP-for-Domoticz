<?php
if($s['belknop']=="On"&&$s['meldingen']=='On'){
	if($s['weg']=='Off'&&$s['slapen']=='Off')sw($i['deurbel'],'On','deurbel');
	if($s['slapen']=='Off'){
		telegram('Deurbel',false,2);
		ios('Deurbel');
	}
	else telegram('Deurbel',true,2);
	$ctx=stream_context_create(array('http'=>array('timeout'=>3)));
	file_get_contents('http://192.168.2.11/telegram.php?snapshot=true',false,$ctx);
	file_get_contents('http://192.168.2.11/fifo_command.php?cmd=record%20on%205%2055',false,$ctx);
	if($s['zon']<=10)sw($i['voordeur'],'On');
}