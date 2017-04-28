<?php
if($status=="On"){
	$kamer=apcu_fetch('skamer');
	if($Weg==0&&$kamer!=16)sl('kamer',17);
	elseif($Weg==0&&$kamer==16){
		sl('kamer',13);
		include('pass2php/minihall1s.php');
	}elseif($Weg==1){
		sl('kamer',10);
		apcu_store('dimactionkamer',1);
	}
}else{
	if($Weg==0)sw('lichtbadkamer1','On');
	else include('minihall3s.php');
}