<?php
if($status=='On'){
	$msg='Rook gedecteerd in badkamer!';
	telegram($msg,false,3);
	resetsecurity('SDbadkamer');
}