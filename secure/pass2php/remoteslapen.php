<?php
if($status=="On"){
	$kamer=apcu_fetch('skamer');
	if(apcu_fetch('sslapen')=='Off'&&$kamer!=16)sl(apcu_fetch('ikamer'),17);
	elseif(apcu_fetch('sslapen')=='Off'&&$kamer==16){
		sl(apcu_fetch('ikamer'),13);
		include('pass2php/minihall1s.php');
	}elseif(apcu_fetch('sslapen')=='On'&&$kamer==12){
		sl(apcu_fetch('ikamer'),11);
		apcu_store('dimmerkamer',1);
	}
}else include('pass2php/minihall3s.php');