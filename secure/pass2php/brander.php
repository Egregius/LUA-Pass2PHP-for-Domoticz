<?php
if($status=='On'){
	if($heating){
		if(apcu_fetch('sliving_temp'>22)){
			if(apcu_fetch('telegramtempliving')<time-3600){
				apcu_store('telegramtempliving',time);
				telegram('Te warm in living, '.$living_temp.' °C. Controleer verwarming',false,2);
			}
		}
	}
}