<?php
$d=array();
$d['Weg']=apcu_fetch('sWeg');
$d['meldingen']=apcu_fetch('smeldingen');
$d['poort']=apcu_fetch('spoort');
$d['tpoort']=apcu_fetch('tpoort');
echo serialize($d);
