<?php
if($s['slapen']=='Off')sw(apcu_fetch('islapen'),'On','slapen');
if($s['lichten_auto']=='Off')sw(apcu_fetch('ilichten_auto'),'On','lichten auto aan');
/*if($s['luifel']!='Open')sw(apcu_fetch('iluifel'),'Off','zonneluifel dicht');*/