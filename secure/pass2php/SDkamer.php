<?php
if($status=='On'){
	$msg='Rook gedecteerd in slaapkamer!';
	telegram($msg,false,3);
	resetsecurity(apcu_fetch('iSDkamer'),'kamer');
}