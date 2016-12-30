<?php
$status=='On'
	?sw(apcu_fetch('iweg'),'On')
	:sw(apcu_fetch('iweg'),'Off');