<?php
if(isset($_REQUEST['script']))shell_exec('/home/pi/'.$_REQUEST['script'].'.sh');
