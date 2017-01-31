<?php
if($status=='On'){
	if(apcu_fetch('sslapen')=='On')sw('slapen','Off');
	if(apcu_fetch('shall')=='Off')sw('hall','On');
}