<?php
if(apcu_fetch('sslapen')=='Off')sw(apcu_fetch('islapen'),'On','slapen');
if(apcu_fetch('slichten_auto')=='Off')sw(apcu_fetch('ilichten_auto'),'On','lichten auto aan');
/*if(apcu_fetch('sluifel']!='Open')sw(apcu_fetch('iluifel'),'Off','zonneluifel dicht');*/