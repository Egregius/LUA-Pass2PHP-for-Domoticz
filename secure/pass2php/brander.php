<?php
if($status=='On'){
	if($heating){
		if(status('living_temp'>22)){
			if(timestamp('telegramtempliving')<time-3600){
				settimestamp('telegramtempliving');
				telegram('Te warm in living, '.$living_temp.' °C. Controleer verwarming',false,2);
			}
		}
	}
}