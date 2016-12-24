<?php
if($s['SDzolder']=='On'){
	$msg='Rook gedecteerd op zolder!';
	telegram($msg,false,3);
	resetsecurity(apcu_fetch('iSDzolder'),'zolder');
}