<?php
if($status=='On'){
	$msg='Rook gedecteerd op zolder!';
	shell_exec("/var/www/html/secure/ios.sh '$msg' > /dev/null 2>/dev/null &");
	telegram($msg,false,2);
	sleep(10);
	resetsecurity('SDzolder');
}