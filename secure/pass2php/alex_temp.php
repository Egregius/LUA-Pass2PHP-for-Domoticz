<?php
include('pass2php/verwarming.php');
if(	$s['alex_temp']>$weer['alex_temp']
	&&$s['alex_temp']>$s[str_replace("_temp","_set",'alex_temp')]
	&&strtotime($t['brander'])<time-600)
		sw($i['brander'],'Off','Brander door '.'alex_temp'.' prev='.$weer['alex_temp'].', new='.$s['alex_temp']);
