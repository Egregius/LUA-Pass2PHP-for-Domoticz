<?php
if($s['SDtobi']=='On'){
	$msg='Rook gedecteerd in kamer Tobi!';
	telegram($msg,false,3);
	resetsecurity(apcu_fetch('iSDtobi'),'Tobi');
}