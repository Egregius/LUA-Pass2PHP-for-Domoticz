<?php
if($s['SDbadkamer']=='On'){
	$msg='Rook gedecteerd in badkamer!';
	telegram($msg,false,3);
	resetsecurity(apcu_fetch('iSDbadkamer'),'badkamer');
}