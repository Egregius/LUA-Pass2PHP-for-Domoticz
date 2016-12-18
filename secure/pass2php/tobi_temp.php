<?php
include('pass2php/verwarming.php');
if(	$s['tobi_temp']>$weer['tobi_temp']
	&&$s['tobi_temp']>$s[str_replace("_temp","_set",'tobi_temp')]
	&&strtotime($t['brander'])<time-600)
		sw($i['brander'],'Off','Brander door '.'tobi_temp'.' prev='.$weer['tobi_temp'].', new='.$s['tobi_temp']);
