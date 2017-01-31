<?php
if($status=='On'){
	sl('tobi',18,'sleep');
	apcu_store('dimactiontobi',1);
}