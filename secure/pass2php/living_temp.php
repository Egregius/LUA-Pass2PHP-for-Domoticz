<?php
if($s['living_temp']>$weer['living_temp']&&$s['living_temp']>$s[str_replace("_temp","_set",'living_temp')]&&strtotime($t['brander'])<time-600)
	sw($i['brander'],'Off','Brander door '.'living_temp'.' prev='.$weer['living_temp'].', new='.$s['living_temp']);
