<?php
if($status=='On'){
	$msg='Rook gedecteerd op zolder!';
	telegram($msg,false,3);
	resetsecurity('SDzolder');
}