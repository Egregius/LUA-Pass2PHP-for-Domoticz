<?php
$d=array();
$d['Weg']=status('Weg');
$d['meldingen']=status('meldingen');
$d['poort']=status('poort');
$d['tpoort']=timestamp('poort');
echo serialize($d);

function status($name){if(file_exists('/var/log/cache/s'.$name.'.cache'))return file_get_contents('/var/log/cache/s'.$name.'.cache');else return 0;}
function timestamp($name){if(file_exists('/var/log/cache/s'.$name.'.cache'))return filemtime('/var/log/cache/s'.$name.'.cache');else return 0;}
?>
