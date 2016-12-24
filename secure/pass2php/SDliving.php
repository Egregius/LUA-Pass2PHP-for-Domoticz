<?php
if($s['SDliving']=='On'){
	$msg='Rook gedecteerd in living!';
	telegram($msg,false,3);
	resetsecurity(apcu_fetch('iSDliving'),'living');
}