<?php
if($status=='On'){
	$msg='Rook gedecteerd in living!';
	telegram($msg,false,3);
	resetsecurity(apcu_fetch('iSDliving'),'living');
}