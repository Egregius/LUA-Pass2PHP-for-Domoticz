<?php
if($status=='Off'){
	if(status('pirkeuken')!='Off')ud('pirkeuken',0,'Off');
}
if(timestamp('werkblad')<time-5&||timestamp('werkblad2')<time-5)RefreshZwave(7);