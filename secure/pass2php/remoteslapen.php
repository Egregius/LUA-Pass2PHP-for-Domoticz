<?php
if($s['remoteslapen']=="On"){
	$kamer=filter_var($s['kamer'],FILTER_SANITIZE_NUMBER_INT);
	if($s['slapen']=='Off'&&$kamer!=16)sl(apcu_fetch('ikamer'),17);
	elseif($s['slapen']=='Off'&&$kamer==16){
		sl(apcu_fetch('ikamer'),13);
		include('pass2php/minihall1s.php');
	}elseif($s['slapen']=='On'&&$kamer==12){
		sl(apcu_fetch('ikamer'),11);
		apcu_store('dimmerkamer',1);
	}
}else include('pass2php/minihall3s.php');