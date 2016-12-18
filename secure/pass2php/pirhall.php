<?php
if($s['pirhall']=='On'){if($s['slapen']=='Off'&&$s['hall']=='Off'&&(time<strtotime('8:00')||$s['zon']<100))sw($i['hall'],'On','hal');if($s['inkom']=='Off'&&(time<strtotime('8:00')||$s['zon']<100))sw($i['inkom'],'On','inkom');alarm('hall',false);}
