<?php
if($status>=5&&apcu_fetch('sfrigo')=='Off'&&apcu_fetch('tfrigo')<time-300){sw('frigo','On');apcu_store('tfrigo',time);}
elseif($status<=4.5&&apcu_fetch('sfrigo')=='On'&&apcu_fetch('tfrigo')<time-300){sw('frigo','Off');apcu_store('tfrigo',time);}