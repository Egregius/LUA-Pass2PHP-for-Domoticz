<?php
if($s['sirene']=='On'){
	sw($i['deurbel'],'On','Deurbel sirene');
	sleep(2);
	sw($i['sirene'],'Off','sirene');
}