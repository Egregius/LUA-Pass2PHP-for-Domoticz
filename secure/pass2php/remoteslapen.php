<?php
if($status=="On"){
	$kamer=status('kamer');
	if($Weg==0&&$kamer!=16)sl('kamer',17);
	elseif($Weg==0&&$kamer==16){
		sl('kamer',13);
		include('pass2php/minihall1s.php');
	}elseif($Weg==1){
		sl('kamer',10);
		setstatus('dimactionkamer',1);
	}
}else{
	if($Weg==0)sw('lichtbadkamer1','On');
	else include('minihall3s.php');
	if(status('sirene')!='Group Off')double('sirene','Off');
}