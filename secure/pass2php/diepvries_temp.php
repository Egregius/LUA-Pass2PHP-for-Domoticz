<?php
if($status> -15){
	if(apcu_fetch('telegramdiepvries')<time-1800){
		telegram('Te warm in diepvries! '.$status.' °C',false,2);
		apcu_store('telegramdiepvries',time);
	}
}