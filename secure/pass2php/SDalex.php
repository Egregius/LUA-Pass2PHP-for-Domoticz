<?php
if($status=='On'){
	$msg='Rook gedecteerd in kamer Alex!';
	telegram($msg,false,3);
	resetsecurity(apcu_fetch('iSDalex'),'Alex');
}