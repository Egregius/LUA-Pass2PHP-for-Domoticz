<?php
if($status=='On'){
	sw(apcu_fetch('ideurbel'),'On','Deurbel sirene');
	sleep(2);
	sw(apcu_fetch('isirene'),'Off','sirene');
}