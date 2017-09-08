<?php
if($status=='Off'){
	if(status('pirkeuken')!='Off')ud('pirkeuken',0,'Off');
}
if(timestamp('wasbak')<time-5||timestamp('kookplaat')<time-5)RefreshZwave(6);