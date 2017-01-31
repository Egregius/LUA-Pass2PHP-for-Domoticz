<?php
if($status=='On'){
	$msg='Rook gedecteerd in kamer Tobi!';
	telegram($msg,false,3);
	resetsecurity('SDtobi');
}