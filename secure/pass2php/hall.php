<?php
if($status=='Off'){
	if(status('hall')!='Off')ud('pirhall',0,'Off');
	if(status('inkom')!='Off')ud('pirinkom',0,'Off');
}