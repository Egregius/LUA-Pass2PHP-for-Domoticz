<?php
include('pass2php/verwarming.php');
if($s['kamer_temp']>$weer['kamer_temp']&&$s['kamer_temp']>$s[str_replace("_temp","_set",'kamer_temp')]&&strtotime($t['brander'])<time-600)sw($i['brander'],'Off','Brander door '.'kamer_temp'.' prev='.$weer['kamer_temp'].', new='.$s['kamer_temp']);
