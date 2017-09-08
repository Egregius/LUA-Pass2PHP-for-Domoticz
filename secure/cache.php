<?php
if(isset($_REQUEST['fetch']))echo status($_REQUEST['fetch']);
elseif(isset($_REQUEST['store'])&&isset($_REQUEST['value']))setstatus($_REQUEST['store'],$_REQUEST['value']);
function setstatus($name,$value){file_put_contents('/var/log/cache/s'.$name.'.cache',$value);}
function status($name){if(file_exists('/var/log/cache/s'.$name.'.cache'))return file_get_contents('/var/log/cache/s'.$name.'.cache');else return 0;}
?>
