<?php
if($s['SDkamer']=='On'){
	$msg='Rook gedecteerd in slaapkamer!';
	telegram($msg,false,3);
	resetsecurity(apcu_fetch('iSDkamer'),'kamer');
}