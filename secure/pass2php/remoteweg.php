<?php
$s['remoteweg']=='On'
	?sw(apcu_fetch('iweg'),'On')
	:sw(apcu_fetch('iweg'),'Off');