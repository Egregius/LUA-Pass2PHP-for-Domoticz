<?php
if($status=='On'){
	sw('deurbel','On','sirene');
	sleep(2);
	sw('sirene','Off');
}