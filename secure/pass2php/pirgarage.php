<?php
if($s['pirgarage']=='On'){
	if((time<strtotime('10:30')||time>strtotime('18:30')||cget('zon')<1200)&&$s['garage']=='Off')
		sw($i['garage'],'On','garage');
	alarm('garage');
}